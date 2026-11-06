<?php

namespace App\Http\Controllers;

use App\Models\SmsLog;
use App\Models\Customer;
use App\Services\FitSmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SmsLogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');
        $status = $request->input('status');
        $customerId = $request->input('customer_id');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $query = SmsLog::with('customer')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('recipient', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%")
                        ->orWhere('error_message', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($type, fn($q) => $q->where('type', $type))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
            ->when($fromDate, fn($q) => $q->whereDate('created_at', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('created_at', '<=', $toDate));

        // Stats summary
        $stats = [
            'total' => (clone $query)->count(),
            'sent' => (clone $query)->where('status', 'sent')->count(),
            'failed' => (clone $query)->where('status', 'failed')->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
        ];
        $stats['success_rate'] = $stats['total'] > 0 ? round(($stats['sent'] / $stats['total']) * 100, 1) : 0;

        $logs = $query->latest('created_at')->paginate(25)->withQueryString();
        $customers = Customer::where('is_active', true)->orderBy('name')->get(['id', 'name', 'phone']);

        return view('sms_logs.index', compact('logs', 'stats', 'customers'));
    }

    public function resend(SmsLog $smsLog)
    {
        $newLog = FitSmsService::send(
            $smsLog->recipient,
            $smsLog->message,
            $smsLog->type,
            $smsLog->customer_id,
            $smsLog->reference_type,
            $smsLog->reference_id
        );

        if ($newLog->status === 'sent') {
            return redirect()->back()->with('success', "SMS to {$newLog->recipient} resent successfully!");
        }

        return redirect()->back()->with('error', "SMS retry failed: " . ($newLog->error_message ?: 'Unknown gateway error'));
    }
}
