<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle an authentication attempt with rate limiting & session protection.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('username')) . '|' . $request->ip());

        // Check if user has exceeded login attempts (5 attempts, then 60 seconds lock)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'username' => 'Too many login attempts. Please try again in ' . $seconds . ' second' . ($seconds > 1 ? 's' : '') . '.',
            ]);
        }

        $loginInput = trim((string) $request->input('username'));
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Check if user exists and is deactivated before credential verification
        $existingUser = \App\Models\User::where($field, $loginInput)->first();
        if ($existingUser && !$existingUser->is_active) {
            RateLimiter::hit($throttleKey, 60);
            throw ValidationException::withMessages([
                'username' => 'Your account has been deactivated. Please contact an administrator.',
            ]);
        }

        $credentials = [
            $field => $loginInput,
            'password' => $request->input('password'),
        ];

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Check if user account is active
            if (!Auth::user()->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw ValidationException::withMessages([
                    'username' => 'Your account has been deactivated. Please contact an administrator.',
                ]);
            }

            // Clear throttle limiter upon successful login
            RateLimiter::clear($throttleKey);

            // Secure session regeneration against fixation
            $request->session()->regenerate();

            // Store security metadata in session
            $request->session()->put('login_ip', $request->ip());
            $request->session()->put('login_user_agent', $request->userAgent());
            $request->session()->put('last_activity_time', time());

            return redirect()->intended(route('dashboard'))->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        // Increment failed attempts count and lock out for 60 seconds if limit hit
        RateLimiter::hit($throttleKey, 60);

        $remainingAttempts = RateLimiter::remaining($throttleKey, 5);
        $message = 'The provided credentials do not match our records.';
        if ($remainingAttempts > 0 && $remainingAttempts <= 3) {
            $message .= ' (' . $remainingAttempts . ' attempt' . ($remainingAttempts > 1 ? 's' : '') . ' remaining before temporary lockout)';
        }

        throw ValidationException::withMessages([
            'username' => $message,
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'You have been successfully logged out.');
    }
}
