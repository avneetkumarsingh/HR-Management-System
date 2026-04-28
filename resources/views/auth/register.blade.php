<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - AttendMS</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>

<div class="auth-wrapper">

    <!-- LEFT SIDE -->
    <div class="auth-left">
        <div class="branding">
            <h1>Create Account</h1>
            <p>Join your team and start managing attendance.</p>
        </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="auth-right">
        <div class="auth-card">

            <h2>Register</h2>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <input type="text" name="name" placeholder="Name" required class="form-input">
                </div>

                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required class="form-input">
                </div>

                <div class="form-group">
                    <select name="department_id" class="form-input" required>
                        <option value="">Choose Team</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <select name="gender" class="form-input" required>
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <select name="marital_status" class="form-input" required>
                        <option value="">Marital Status</option>
                        <option value="single">Single</option>
                        <option value="married">Married</option>
                        <option value="divorced">Divorced</option>
                        <option value="widowed">Widowed</option>
                    </select>
                </div>

                <div class="form-group">
                    <input type="number" name="children_count" placeholder="Number of Children" value="0" min="0" required class="form-input">
                </div>

                <div class="form-group">
                    <label style="font-size: 0.85rem; color: var(--text-muted); display: block; margin-bottom: 0.25rem;">Date of Joining</label>
                    <input type="date" name="date_of_joining" required class="form-input">
                </div>

                <div class="form-group">
                    <label style="font-size: 0.85rem; color: var(--text-muted); display: block; margin-bottom: 0.25rem;">Date of Birth (DOB)</label>
                    <input type="date" name="date_of_birth" required class="form-input">
                </div>

                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required class="form-input">
                </div>

                <div class="form-group">
                    <input type="password" name="password_confirmation" placeholder="Confirm Password" required class="form-input">
                </div>

                <button type="submit" class="btn btn-primary">Register</button>

                <div class="auth-link">
                    <a href="{{ route('login') }}">Already registered?</a>
                </div>

            </form>

        </div>
    </div>

</div>

</body>
</html>