<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $module = $request->input('module');
        $action = $request->input('action');
        $userId = $request->input('user_id');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $query = AuditLog::with('user')->latest('id');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('user_email', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('record_title', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($module) {
            $query->where('module', $module);
        }

        if ($action) {
            $query->where('action', strtolower($action));
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        $logs = $query->paginate(30)->withQueryString();

        // Statistics
        $today = Carbon::today();
        $totalLogs = AuditLog::count();
        $actionsToday = AuditLog::whereDate('created_at', $today)->count();
        $loginsToday = AuditLog::where('action', 'login')->whereDate('created_at', $today)->count();
        $deletionsCount = AuditLog::whereIn('action', ['deleted', 'deactivated'])->count();

        // Dropdown data
        $modules = AuditLog::select('module')->distinct()->orderBy('module')->pluck('module');
        $actions = AuditLog::select('action')->distinct()->orderBy('action')->pluck('action');
        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('audit_logs.index', compact(
            'logs',
            'totalLogs',
            'actionsToday',
            'loginsToday',
            'deletionsCount',
            'modules',
            'actions',
            'users'
        ));
    }

    public function show(AuditLog $auditLog)
    {
        return response()->json([
            'id' => $auditLog->id,
            'user_name' => $auditLog->user_name,
            'user_email' => $auditLog->user_email,
            'action' => $auditLog->action,
            'module' => $auditLog->module,
            'record_title' => $auditLog->record_title,
            'description' => $auditLog->description,
            'ip_address' => $auditLog->ip_address,
            'user_agent' => $auditLog->user_agent,
            'created_at' => $auditLog->created_at?->format('M d, Y h:i:s A'),
            'old_values' => $auditLog->old_values,
            'new_values' => $auditLog->new_values,
        ]);
    }
}
