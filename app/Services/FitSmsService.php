<?php

namespace App\Services;

use App\Models\SmsLog;
use App\Models\Quotation;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FitSmsService
{
    /**
     * Format any Sri Lankan phone number into standard 947XXXXXXXX format.
     */
    public static function formatPhoneNumber(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Keep only digits
        $digits = preg_replace('/[^\d]/', '', $phone);

        if (empty($digits)) {
            return null;
        }

        // If starts with 0094
        if (str_starts_with($digits, '0094')) {
            $digits = substr($digits, 4);
        }
        // If starts with 94
        elseif (str_starts_with($digits, '94')) {
            $digits = substr($digits, 2);
        }
        // If starts with 0
        elseif (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        // Standard Sri Lankan mobile prefix checks (70, 71, 72, 74, 75, 76, 77, 78)
        if (strlen($digits) === 9) {
            return '94' . $digits;
        }

        if (strlen($digits) > 9) {
            return '94' . substr($digits, -9);
        }

        return null;
    }

    /**
     * Send an SMS using Fit SMS Gateway and record the transaction in the database.
     */
    public static function send(
        string $rawPhone,
        string $message,
        string $type = 'custom',
        ?int $customerId = null,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): SmsLog {
        $senderId = config('services.fitsms.sender_id', 'CEYLON WEDS');
        $apiUrl = config('services.fitsms.url', 'https://app.fitsms.lk/api/v4/sms/send');
        $apiToken = config('services.fitsms.token');

        $formattedPhone = self::formatPhoneNumber($rawPhone);

        // Convert any HTML line breaks <br>, <br/>, <br /> into real SMS newlines (\n)
        $message = str_ireplace(['<br />', '<br/>', '<br>'], "\n", $message);

        // If phone number is invalid
        if (!$formattedPhone) {
            $log = SmsLog::create([
                'recipient' => $rawPhone ?: 'NO_PHONE',
                'customer_id' => $customerId,
                'type' => $type,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'sender_id' => $senderId,
                'message' => $message,
                'status' => 'failed',
                'response_code' => 400,
                'response_body' => null,
                'error_message' => "Invalid Sri Lankan phone number format: '{$rawPhone}'",
                'sent_at' => null,
            ]);

            Log::warning("FitSMS delivery aborted: Invalid phone number '{$rawPhone}' for customer ID {$customerId}");
            return $log;
        }

        // Prepare database record as pending
        $log = SmsLog::create([
            'recipient' => $formattedPhone,
            'customer_id' => $customerId,
            'type' => $type,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'sender_id' => $senderId,
            'message' => $message,
            'status' => 'pending',
            'sent_at' => null,
        ]);

        try {
            // Fit SMS v4 Payload
            $payload = [
                'recipient' => $formattedPhone,
                'sender_id' => $senderId,
                'type' => 'plain',
                'message' => $message,
            ];

            $response = Http::withToken($apiToken)
                ->timeout(12)
                ->acceptJson()
                ->asJson()
                ->post($apiUrl, $payload);

            $statusCode = $response->status();
            $body = $response->body();
            $json = $response->json();

            // FitSMS returns HTTP 200 with JSON payload
            $isSuccess = $response->successful() && (!isset($json['status']) || in_array(strtolower((string)$json['status']), ['success', 'queued', 'sent', 'ok', '1']));

            if ($isSuccess) {
                $log->update([
                    'status' => 'sent',
                    'response_code' => $statusCode,
                    'response_body' => $body,
                    'error_message' => null,
                    'sent_at' => now(),
                ]);

                \App\Services\AuditLogger::log(
                    'sms_sent',
                    'SMS',
                    "SMS to {$formattedPhone}",
                    "Sent {$type} SMS to {$formattedPhone} via sender ID '{$senderId}'",
                    $log->id,
                    null,
                    ['recipient' => $formattedPhone, 'message' => $message, 'status' => 'sent']
                );
            } else {
                $errMsg = $json['message'] ?? $json['error'] ?? "Gateway returned HTTP status {$statusCode}";
                $log->update([
                    'status' => 'failed',
                    'response_code' => $statusCode,
                    'response_body' => $body,
                    'error_message' => $errMsg,
                ]);

                Log::warning("FitSMS delivery failed for {$formattedPhone}: {$errMsg}");
            }
        } catch (\Throwable $e) {
            $log->update([
                'status' => 'failed',
                'response_code' => 500,
                'error_message' => 'Connection / Gateway Exception: ' . $e->getMessage(),
            ]);

            Log::error("FitSMS exception when sending to {$formattedPhone}: " . $e->getMessage());
        }

        return $log;
    }

    /**
     * 1. Send SMS when a Quotation is created.
     */
    public static function sendQuotationCreatedSms(Quotation $quotation): ?SmsLog
    {
        $customer = $quotation->customer;
        $phone = $customer?->phone ?: $customer?->phone2;

        if (!$phone) {
            return null;
        }

        $custName = $customer?->name ?: 'Valued Client';
        $eventName = $quotation->event_name ?: 'Your Event';
        $totalFormatted = number_format((float)$quotation->total_amount, 2);
        $validUntil = $quotation->valid_until ? $quotation->valid_until->format('d M Y') : '14 days';

        $message = "Dear {$custName},\n"
            . "Your Quotation #{$quotation->quotation_no} for '{$eventName}' is ready.\n"
            . "Total: Rs. {$totalFormatted}.\n"
            . "Valid until: {$validUntil}.\n"
            . "Thank you for choosing CEYLON WEDS!\n"
            . "Hotline: 0777524937";

        return self::send(
            $phone,
            $message,
            'quotation_created',
            $quotation->customer_id,
            'Quotation',
            $quotation->id
        );
    }

    /**
     * 2. Send SMS when an Order is created (with invoice details).
     */
    public static function sendOrderCreatedSms(Order $order): ?SmsLog
    {
        $customer = $order->customer;
        $phone = $customer?->phone ?: $customer?->phone2;

        if (!$phone) {
            return null;
        }

        $custName = $customer?->name ?: 'Valued Client';
        $eventName = $order->event?->event_name ?: 'Event Service';
        $totalFormatted = number_format((float)$order->total_amount, 2);
        $paidFormatted = number_format((float)$order->paid_amount, 2);
        $balanceFormatted = number_format((float)$order->balance, 2);

        $message = "Dear {$custName},\n"
            . "Your Order #{$order->order_no} ({$eventName}) is confirmed.\n"
            . "Total: Rs. {$totalFormatted}.\n"
            . "Paid: Rs. {$paidFormatted}.\n"
            . "Balance Due: Rs. {$balanceFormatted}.\n"
            . "Thank you for trusting CEYLON WEDS!\n"
            . "Hotline: 0777524937";

        return self::send(
            $phone,
            $message,
            'order_created',
            $order->customer_id,
            'Order',
            $order->id
        );
    }

    /**
     * 3. Send SMS when a Payment is recorded.
     */
    public static function sendPaymentReceivedSms(Payment $payment): ?SmsLog
    {
        $payment->loadMissing(['customer', 'order']);
        $customer = $payment->customer;
        $order = $payment->order;
        $phone = $customer?->phone ?: $customer?->phone2;

        if (!$phone) {
            return null;
        }

        $custName = $customer?->name ?: 'Valued Client';
        $orderNo = $order?->order_no ?? 'Order';
        $paidAmountFormatted = number_format((float)$payment->amount, 2);
        $remainingBalanceFormatted = number_format((float)($order?->balance ?? 0), 2);
        $methodFormatted = ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'cash'));

        $message = "Dear {$custName},\n"
            . "Payment of Rs. {$paidAmountFormatted} received via {$methodFormatted} for Order #{$orderNo} (Receipt #{$payment->payment_no}).\n"
            . "Balance Due: Rs. {$remainingBalanceFormatted}.\n"
            . "Thank you - CEYLON WEDS!\n"
            . "Hotline: 0777524937";

        return self::send(
            $phone,
            $message,
            'payment_received',
            $payment->customer_id,
            'Payment',
            $payment->id
        );
    }
}
