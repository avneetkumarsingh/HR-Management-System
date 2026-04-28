@extends('layouts.app')
@section('title', 'Add New Employee')

@section('content')
<form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="card mb-6">
        <div class="card-header"><h3 class="card-title">Personal Information</h3></div>
        <div class="card-body grid grid-cols-2 gap-6">
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-input" required value="{{ old('name') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-input" required value="{{ old('email') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Login Password</label>
                <input type="password" name="password" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-input" value="{{ old('phone') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Profile Image (Avatar)</label>
                <input type="file" name="avatar" class="form-input" accept="image/*">
            </div>
            <div class="form-group">
                <label class="form-label">Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-input" value="{{ old('date_of_birth') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select">
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Marital Status</label>
                <select name="marital_status" class="form-select">
                    <option value="single">Single</option>
                    <option value="married">Married</option>
                    <option value="divorced">Divorced</option>
                    <option value="widowed">Widowed</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Number of Children</label>
                <input type="number" name="children_count" class="form-input" value="0" min="0">
            </div>
        </div>
    </div>

    <div class="card md-6">
        <div class="card-header"><h3 class="card-title">Employment Information</h3></div>
        <div class="card-body grid grid-cols-2 gap-6">
            <div class="form-group">
                <label class="form-label">Employee ID (Auto-generated)</label>
                <input type="text" name="employee_id" class="form-input" value="{{ $autoEmployeeId }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">System Role</label>
                <select name="role" class="form-select" required>
                    <option value="employee">Employee</option>
                    <option value="manager">Manager</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-select" required>
                    <option value="">Select Department</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Designation</label>
                <select name="designation_id" class="form-select" required>
                    <option value="">Select Designation</option>
                    @foreach($designations as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" id="manager_group">
                <label class="form-label">Reporting Manager</label>
                <select name="manager_id" class="form-select">
                    <option value="">None</option>
                    @foreach($managers as $m)
                        <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->employee_id }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Shift Assignment</label>
                <select name="shift_id" class="form-select" required>
                    @foreach($shifts as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->start_time }} - {{ $s->end_time }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Date of Joining</label>
                <input type="date" name="date_of_joining" class="form-input" required value="{{ date('Y-m-d') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Employment Type</label>
                <select name="employment_type" class="form-select">
                    <option value="full_time">Full Time</option>
                    <option value="part_time">Part Time</option>
                    <option value="contract">Contract</option>
                    <option value="intern">Intern</option>
                </select>
            </div>
        </div>
    </div>

    <div class="text-right">
        <a href="{{ route('employees.index') }}" class="btn btn-outline mr-2">Cancel</a>
        <button type="submit" class="btn btn-primary">Create Employee Profile</button>
    </div>
</form>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var roleSelect = document.querySelector('select[name="role"]');
        var managerGroup = document.getElementById('manager_group');

        function toggleManagerGroup() {
            if (roleSelect.value === 'manager' || roleSelect.value === 'hr') {
                managerGroup.style.display = 'none';
                document.querySelector('select[name="manager_id"]').value = '';
            } else {
                managerGroup.style.display = 'block';
            }
        }

        roleSelect.addEventListener('change', toggleManagerGroup);
        toggleManagerGroup(); // Initial call
    });
</script>
@endsection
