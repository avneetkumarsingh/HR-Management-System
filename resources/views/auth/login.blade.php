<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AttendMS</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-left">
            <div class="decor-circles"></div>
            <div class="branding">
                <h1>Smarter attendance.<br>Happier teams.</h1>
                <p>Welcome to the next generation Attendance Management System.</p>
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
                            <input type="email" name="email" class="form-input" value="{{ old('email', 'avneet@gmail.com') }}" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-wrapper">
                            <input type="password" name="password" id="password" class="form-input" value="password" required>
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


                    
                    <div style="margin-top:2rem; padding-top:1rem; border-top:1px solid var(--border); font-size:0.85rem; color:var(--text-muted); text-align:center">
                        Demo: avneet@gmail.com / password
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePass() {
            var input = document.getElementById('password');
            var icon = event.currentTarget;
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
