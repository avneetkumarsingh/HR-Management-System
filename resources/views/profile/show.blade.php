@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="card" style="max-width: 800px; margin:0 auto;">
    <div class="card-header">
        <h3 class="card-title">Update Profile</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div style="display:flex; align-items:center; gap:2rem; margin-bottom:2rem">
                <img src="{{ $user->avatar_url }}" alt="avatar" style="width:100px; height:100px; border-radius:50%; object-fit:cover">
                <div>
                    <label class="form-label">Upload New Avatar</label>
                    <input type="file" name="avatar" class="form-input">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone', $user->phone) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address (Readonly)</label>
                    <input type="email" class="form-input" value="{{ $user->email }}" readonly style="background:var(--bg)">
                </div>
                <div class="form-group">
                    <label class="form-label">Employee ID (Readonly)</label>
                    <input type="text" class="form-input" value="{{ $user->employee_id }}" readonly style="background:var(--bg)">
                </div>
            </div>

            <h4 style="margin:2rem 0 1rem; border-bottom:1px solid var(--border); padding-bottom:0.5rem">Change Password</h4>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-input">
                </div>
            </div>

            <div style="margin-top:2rem; text-align:right">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
