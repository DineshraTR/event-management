<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <title>Event Management - Login</title>

    <!-- Google font-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Bootstrap css-->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary-color: #484646;
            --primary-color-rgb: 72, 70, 70;
            --primary-dark: #3a3838;
            --primary-light: #5f5c5c;
            --base-font-size: 14px;
            --font-outfit: 'Outfit', sans-serif;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-outfit);
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
            overflow: hidden;
            height: 100vh;
        }

        .login-wrapper {
            display: flex;
            width: 100%;
            height: 100vh;
        }

        /* Left Side: Branded Visual */
        .login-visual {
            flex: 1.2;
            background: linear-gradient(135deg, rgba(72, 70, 70, 0.88), rgba(40, 38, 38, 0.95)),
                url("{{ asset('assets/images/login_visual_bg.png') }}");
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 80px;
            color: white;
            position: relative;
        }

        .login-visual .brand-content {
            z-index: 2;
            max-width: 520px;
        }

        .login-visual .company-logo-large {
            margin-bottom: 50px;
            background: #ffffff;
            padding: 16px 24px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.22);
        }

        .logo-icon-wrap {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #484646, #3a3838);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 20px;
            box-shadow: 0 6px 14px rgba(72, 70, 70, 0.4);
        }

        .logo-text-wrap {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }

        .logo-title {
            font-size: 1.25rem;
            font-weight: 900;
            letter-spacing: 0.5px;
            color: #1e293b;
        }

        .logo-subtitle {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 2px;
            color: #484646;
        }

        .login-visual h1 {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 20px;
            letter-spacing: -1px;
            color: #ffffff;
        }

        .login-visual p {
            font-size: 1.15rem;
            opacity: 0.92;
            line-height: 1.6;
            margin-bottom: 30px;
            color: #f1f5f9;
        }

        .login-visual .copyright-bar {
            position: absolute;
            bottom: 40px;
            left: 80px;
            right: 80px;
            z-index: 2;
            opacity: 0.75;
            font-size: 0.85rem;
            font-weight: 500;
            letter-spacing: 0.3px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        /* Right Side: Login Panel */
        .login-panel {
            flex: 1;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px;
            position: relative;
            box-shadow: -20px 0 40px rgba(0, 0, 0, 0.05);
            z-index: 10;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .login-header {
            margin-bottom: 35px;
        }

        .login-header h2 {
            font-weight: 800;
            font-size: 2.2rem;
            color: #1a1a1a;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .login-header span {
            color: #6c757d;
            font-size: 0.98rem;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            font-weight: 700;
            font-size: 0.85rem;
            color: #343a40;
            margin-bottom: 10px;
            display: block;
            letter-spacing: 0.5px;
        }

        .input-group {
            border-radius: 16px;
            border: 2px solid #edf2f7;
            padding: 2px 15px;
            transition: all 0.2s ease;
            background: #ffffff;
            align-items: center;
        }

        .input-group:focus-within {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 5px rgba(var(--primary-color-rgb), 0.1);
        }

        .input-group.is-invalid-group {
            border-color: #ef4444;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        .input-group-text {
            background: none;
            border: none;
            color: #94a3b8;
            padding-right: 14px;
            padding-left: 0;
            font-size: 1.05rem;
        }

        .form-control {
            border: none;
            height: 52px;
            font-size: 1rem;
            font-family: var(--font-outfit);
            background: transparent;
            color: #1e293b;
        }

        .form-control:focus {
            box-shadow: none;
            background: transparent;
        }

        .password-toggle-btn {
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 0 8px;
            font-size: 1.05rem;
            transition: color 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle-btn:hover {
            color: #334155;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%) !important;
            border: 1px solid var(--primary-dark) !important;
            height: 56px;
            border-radius: 18px;
            font-weight: 800;
            font-size: 1.05rem;
            letter-spacing: 0.5px;
            box-shadow: 0 10px 24px rgba(72, 70, 70, 0.35);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
            margin-top: 10px;
            color: #ffffff !important;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #252424 100%) !important;
            border-color: #252424 !important;
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(72, 70, 70, 0.45);
            color: #ffffff !important;
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        /* Mobile Adjustments */
        @media (max-width: 992px) {
            body {
                overflow-y: auto;
                height: auto;
                min-height: 100vh;
            }

            .login-wrapper {
                height: auto;
                min-height: 100vh;
            }

            .login-visual {
                display: none;
            }

            .login-panel {
                flex: 1;
                padding: 40px 24px;
            }
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <!-- Left Visual Partition -->
        <div class="login-visual">
            <div class="brand-content">
                <!-- <div class="company-logo-large">
                    <div class="logo-icon-wrap">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div class="logo-text-wrap">
                        <span class="logo-title">Event</span>
                        <span class="logo-subtitle">MANAGEMENT</span>
                    </div>
                </div> -->
                <h1>Empowering Events with Intelligence</h1>
                <p>Welcome to <strong>CEYLON WEDDING COMPANY</strong>. Experience the next generation of event planning, bookings, quotations, and financial tracking streamlined for success.</p>
            </div>

            <div class="copyright-bar">
                <span>© {{ date('Y') }} CEYLON WEDDING COMPANY. All rights reserved.</span>
                <span>Software by <a href="https://www.tritcal.com" target="_blank" style="color: #fff; text-decoration: underline;">Tritcal International LLC</a></span>
            </div>
        </div>

        <!-- Right Login Panel -->
        <div class="login-panel">
            <div class="login-container">
                <div class="login-header text-center">
                    <h2>Sign In</h2>
                    <span>Enter your credentials to continue</span>
                </div>

                <!-- Session & Error Alerts -->
                @if(session('success'))
                    <div class="alert alert-success py-2 px-3 small rounded-3 mb-4 d-flex align-items-center gap-2" style="border-radius: 12px; background-color: #f0fdf4; border-color: #bbf7d0; color: #166534;">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info py-2 px-3 small rounded-3 mb-4 d-flex align-items-center gap-2" style="border-radius: 12px; background-color: #eff6ff; border-color: #bfdbfe; color: #1e40af;">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>{{ session('info') }}</span>
                    </div>
                @endif

                @if(session('status'))
                    <div class="alert alert-info py-2 px-3 small rounded-3 mb-4 d-flex align-items-center gap-2" style="border-radius: 12px; background-color: #eff6ff; border-color: #bfdbfe; color: #1e40af;">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger py-2 px-3 small rounded-3 mb-4 d-flex align-items-center gap-2" style="border-radius: 12px; background-color: #fef2f2; border-color: #fecaca; color: #991b1b;">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if(isset($errors) && $errors->any())
                    <div class="alert alert-danger py-2 px-3 small rounded-3 mb-4 d-flex align-items-center gap-2" style="border-radius: 12px; background-color: #fef2f2; border-color: #fecaca; color: #991b1b;">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" name="login_form" id="loginForm">
                    @csrf

                    <!-- Username Field -->
                    <div class="form-group">
                        <label class="form-label">USERNAME OR EMAIL</label>
                        <div class="input-group @error('username') is-invalid-group @enderror">
                            <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                            <input class="form-control" 
                                   type="text" 
                                   name="username" 
                                   id="username"
                                   value="{{ old('username') }}" 
                                   required 
                                   autofocus 
                                   placeholder="name@company.com or username" 
                                   autocomplete="username">
                        </div>
                        @error('username')
                            <small class="text-danger mt-1 d-block fw-bold" style="font-size: 0.8rem;">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <label class="form-label">PASSWORD</label>
                        <div class="input-group @error('password') is-invalid-group @enderror">
                            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                            <input class="form-control" 
                                   type="password" 
                                   name="password" 
                                   id="password" 
                                   required 
                                   placeholder="••••••••" 
                                   autocomplete="current-password">
                            <button type="button" class="password-toggle-btn" id="togglePasswordBtn" title="Toggle password visibility">
                                <i class="fa-regular fa-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <small class="text-danger mt-1 d-block fw-bold" style="font-size: 0.8rem;">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Remember Session Checkbox -->
                    <div class="d-flex align-items-center justify-content-between mb-4 mt-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1" checked>
                            <label class="form-check-label text-muted" for="remember" style="font-size: 0.9rem; cursor: pointer;">
                                Remember session
                            </label>
                        </div>
                        <span class="text-muted" style="font-size: 0.85rem; display: flex; align-items: center; gap: 5px;">
                            <i class="fa-solid fa-shield-halved text-success"></i> Secure Access
                        </span>
                    </div>

                    <!-- Submit Button -->
                    <button class="btn btn-primary" type="submit" id="submitLoginBtn">
                        <span id="submitBtnText">Access Dashboard <i class="fa-solid fa-arrow-right ms-2"></i></span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Script for password toggle & form UX -->
    <script>
        function fillCredentials(user, pass) {
            document.getElementById('username').value = user;
            document.getElementById('password').value = pass;
            document.getElementById('password').focus();
        }

        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('togglePasswordBtn');
            const pwdInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');

            if (toggleBtn && pwdInput && toggleIcon) {
                toggleBtn.addEventListener('click', function () {
                    if (pwdInput.type === 'password') {
                        pwdInput.type = 'text';
                        toggleIcon.classList.remove('fa-eye');
                        toggleIcon.classList.add('fa-eye-slash');
                    } else {
                        pwdInput.type = 'password';
                        toggleIcon.classList.remove('fa-eye-slash');
                        toggleIcon.classList.add('fa-eye');
                    }
                });
            }

            const loginForm = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitLoginBtn');
            const submitBtnText = document.getElementById('submitBtnText');

            if (loginForm && submitBtn && submitBtnText) {
                loginForm.addEventListener('submit', function () {
                    submitBtn.disabled = true;
                    submitBtnText.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Authenticating...';
                });
            }
        });
    </script>
</body>

</html>
