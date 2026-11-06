<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    protected static array $sensitiveKeys = [
        'password',
        'password_confirmation',
        'remember_token',
        '_token',
        '_method',
    ];

    /**
     * Record an audit log entry.
     */
    public static function log(
        string $action,
        string $module,
        ?string $recordTitle = null,
        ?string $description = null,
        ?int $recordId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): ?AuditLog {
        try {
            $user = Auth::user();

            $cleanOld = $oldValues ? self::sanitize($oldValues) : null;
            $cleanNew = $newValues ? self::sanitize($newValues) : null;

            if (empty($description)) {
                $description = self::generateDescription($action, $module, $recordTitle, $user?->name);
            }

            return AuditLog::create([
                'user_id' => $user?->id,
                'user_name' => $user?->name ?? 'System / Guest',
                'user_email' => $user?->email,
                'action' => strtolower($action),
                'module' => $module,
                'record_id' => $recordId,
                'record_title' => $recordTitle,
                'description' => $description,
                'old_values' => $cleanOld,
                'new_values' => $cleanNew,
                'ip_address' => Request::ip() ?? '127.0.0.1',
                'user_agent' => substr((string)Request::userAgent(), 0, 255),
            ]);
        } catch (\Throwable $e) {
            Log::warning('AuditLogger failed to write log: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Helper to log authentication actions (login, logout, failed).
     */
    public static function logAuth(string $action, $user = null, ?string $credentialsEmail = null): ?AuditLog
    {
        $userName = $user?->name ?? ($credentialsEmail ?: 'Unknown User');
        $userEmail = $user?->email ?? $credentialsEmail;

        $desc = match (strtolower($action)) {
            'login' => "User {$userName} logged in successfully",
            'logout' => "User {$userName} logged out",
            'failed_login' => "Failed login attempt for email: {$credentialsEmail}",
            default => "Authentication event: {$action}"
        };

        try {
            return AuditLog::create([
                'user_id' => $user?->id,
                'user_name' => $userName,
                'user_email' => $userEmail,
                'action' => strtolower($action),
                'module' => 'Auth',
                'record_id' => $user?->id,
                'record_title' => $userName,
                'description' => $desc,
                'old_values' => null,
                'new_values' => null,
                'ip_address' => Request::ip() ?? '127.0.0.1',
                'user_agent' => substr((string)Request::userAgent(), 0, 255),
            ]);
        } catch (\Throwable $e) {
            Log::warning('AuditLogger auth logging failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Remove sensitive fields from stored payload.
     */
    protected static function sanitize(array $data): array
    {
        foreach (self::$sensitiveKeys as $key) {
            unset($data[$key]);
        }

        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $data[$k] = self::sanitize($v);
            }
        }

        return $data;
    }

    /**
     * Generate fallback readable description.
     */
    protected static function generateDescription(string $action, string $module, ?string $title, ?string $userName): string
    {
        $who = $userName ?: 'System';
        $titleStr = $title ? " ({$title})" : '';

        return match (strtolower($action)) {
            'created' => "{$who} created {$module}{$titleStr}",
            'updated' => "{$who} updated {$module}{$titleStr}",
            'deleted' => "{$who} deleted {$module}{$titleStr}",
            'payment_received' => "{$who} recorded payment for {$module}{$titleStr}",
            'converted' => "{$who} converted {$module}{$titleStr}",
            default => "{$who} performed '{$action}' on {$module}{$titleStr}",
        };
    }
}
