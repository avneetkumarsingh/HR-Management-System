<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Walkwel AttendMS</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-left">
            <div class="decor-circles"></div>
            <div class="branding">
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
                    <svg width="64" height="64" viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg" style="flex-shrink: 0;">
                      <g fill="white" stroke="white" stroke-width="13" stroke-linecap="round">
                        <line x1="28" y1="38" x2="46" y2="82" />
                        <line x1="48" y1="38" x2="66" y2="82" />
                        <circle cx="76" cy="38" r="6.5" stroke="none" />
                        <line x1="92" y1="38" x2="92" y2="82" />
                        <circle cx="108" cy="38" r="6.5" stroke="none" />
                      </g>
                    </svg>
                    <div style="display: flex; flex-direction: column; line-height: 1.15;">
                        <span style="font-size: 1.8rem; font-weight: 700; color: white;">Walkwel</span>
                        <span style="font-size: 1.8rem; font-weight: 700; color: white;">AttendMS</span>
                    </div>
                </div>
                <p>Welcome to Walkwel Technology's Attendance Management System.</p>
                <ul class="auth-features">
                    <li>Live attendance tracking</li>
                    <li>Automated leave balances</li>
                    <li>Seamless manager approvals</li>
                </ul>
            </div>
        </div>
        <div class="auth-right">
            <div class="auth-card">
                <h2>Welcome Back!</h2>
                <p class="subtitle">Please sign in to your account</p>
                
                @if($errors->any())
                    <div class="invalid-feedback" style="margin-bottom:1rem; padding:0.75rem; background:var(--danger-light); color:var(--danger); border-radius:var(--radius-sm)">
                        {{ $errors->first() }}
                    </div>
                @endif
                
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <div class="input-wrapper">
                            <input type="email" name="email" id="email" class="form-input" value="{{ old('email') }}" required style="padding-left: 1rem;">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-wrapper">
                            <input type="password" name="password" id="password" class="form-input" value="" required style="padding-left: 1rem; padding-right: 2.5rem;">
                            <i class="fas fa-eye icon-right" onclick="toggleField('password', this)"></i>
                        </div>
                    </div>

                    <div class="form-group flex justify-between items-center text-sm">
                        <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer">
                            <input type="checkbox" name="remember"> Remember me
                        </label>
                        <a href="#">Forgot password?</a>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block mb-2">
                        Sign In </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleField(fieldId, icon) {
            var input = document.getElementById(fieldId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.add('fa-eye');
                icon.classList.remove('fa-eye-slash');
            }
        }
    </script>
</body>
</html>
