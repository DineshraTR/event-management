<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionSecurity
{
    /**
     * Handle an incoming request for session validity, account status, and hijacking prevention.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // 1. Kick out immediately if user account has been deactivated while logged in
            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Your account has been deactivated. Please contact an administrator.'
                    ], 403);
                }

                return redirect()->route('login')->withErrors([
                    'username' => 'Your account has been deactivated. Please contact an administrator.',
                ]);
            }

            // 2. Session Hijacking Protection (User-Agent fingerprint validation)
            $sessionAgent = $request->session()->get('login_user_agent');
            $currentAgent = $request->userAgent();

            if (!empty($sessionAgent) && !empty($currentAgent) && $sessionAgent !== $currentAgent) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Security alert: Session invalidated due to client environment mismatch. Please sign in again.'
                    ], 401);
                }

                return redirect()->route('login')->withErrors([
                    'username' => 'Security alert: Session invalidated due to client environment mismatch. Please sign in again.',
                ]);
            }

            // 3. Idle Inactivity Session Timeout
            $lastActivity = $request->session()->get('last_activity_time');
            $lifetimeMinutes = (int) config('session.lifetime', 120);
            $idleTimeoutSeconds = $lifetimeMinutes * 60;

            if ($lastActivity && (time() - $lastActivity > $idleTimeoutSeconds)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Your session has expired due to inactivity. Please log in again.'
                    ], 401);
                }

                return redirect()->route('login')->with('info', 'Your session has expired due to inactivity. Please log in again.');
            }

            // Update last activity timestamp in current session
            $request->session()->put('last_activity_time', time());
        }

        return $next($request);
    }
}
