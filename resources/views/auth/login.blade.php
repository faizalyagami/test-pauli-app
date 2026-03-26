{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Pauli Test System</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            text-align: center;
            padding: 40px 30px;
        }

        .login-header h2 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }

        .login-header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .input-group {
            position: relative;
        }

        .input-group-text {
            background: #f8f9fa;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }

        .form-control {
            border-left: none;
            padding: 12px 15px;
            font-size: 14px;
            border-radius: 0 10px 10px 0;
            transition: all 0.3s;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: var(--primary-color);
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--primary-color);
        }

        .checkbox {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
        }

        .checkbox label {
            margin: 0;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(52, 152, 219, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .alert {
            border-radius: 10px;
            margin-bottom: 20px;
            padding: 12px 15px;
            border: none;
        }

        .alert-danger {
            background: #fee2e2;
            color: #dc2626;
        }

        .alert-success {
            background: #d1fae5;
            color: #059669;
        }

        .footer-links {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .footer-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 14px;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        .demo-credentials {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
        }

        .demo-credentials h6 {
            margin-bottom: 10px;
            font-size: 13px;
            font-weight: 600;
            color: #666;
        }

        .demo-credentials p {
            margin: 0;
            font-size: 12px;
            color: #666;
        }

        .demo-credentials code {
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
            z-index: 10;
            background: white;
            padding-left: 5px;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        @media (max-width: 576px) {
            .login-header {
                padding: 30px 20px;
            }

            .login-header h2 {
                font-size: 24px;
            }

            .login-body {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-chart-line fa-3x mb-3"></i>
                <h2>Pauli Test System</h2>
                <p>Psychological Test Management System</p>
            </div>

            <div class="login-body">
                @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf

                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope me-2"></i> Email Address
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Enter your email"
                                required
                                autofocus>
                        </div>
                        @error('email')
                        <div class="invalid-feedback d-block mt-1">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock me-2"></i> Password
                        </label>
                        <div class="input-group position-relative">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                id="password"
                                name="password"
                                placeholder="Enter your password"
                                required>
                            <span class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                        @error('password')
                        <div class="invalid-feedback d-block mt-1">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="checkbox">
                        <label class="d-flex align-items-center">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span class="ms-2">Remember me</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-decoration-none">
                            Forgot password?
                        </a>
                    </div>

                    <button type="submit" class="btn-login" id="loginBtn">
                        <i class="fas fa-sign-in-alt me-2"></i> Login
                    </button>
                </form>

                <div class="demo-credentials">
                    <h6><i class="fas fa-info-circle me-1"></i> Demo Credentials</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Admin:</strong><br>
                                <code>admin@pauli.com</code><br>
                                <code>password</code>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Tester:</strong><br>
                                <code>tester@pauli.com</code><br>
                                <code>password</code>
                            </p>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <p><strong>Applicant:</strong><br>
                                <code>budi@example.com</code> / <code>password</code><br>
                                <code>siti@example.com</code> / <code>password</code>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="footer-links">
                    <p class="mb-0">
                        <small>&copy; {{ date('Y') }} Pauli Test System. All rights reserved.</small>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Prevent double submit
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Logging in...';
        });

        // Enter key submit
        document.getElementById('password').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('loginForm').submit();
            }
        });

        // Auto-fill demo credentials (optional - for development)
        @if(env('APP_ENV') === 'local')
        const demoEmail = document.getElementById('email');
        const demoPassword = document.getElementById('password');

        demoEmail.addEventListener('focus', function() {
            if (!this.value) {
                this.value = 'admin@pauli.com';
            }
        });

        demoPassword.addEventListener('focus', function() {
            if (!this.value) {
                this.value = 'password';
            }
        });
        @endif
    </script>
</body>

</html>