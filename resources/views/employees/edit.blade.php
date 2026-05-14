@extends('layouts.app')
@section('title', 'Edit Employee')

@section('content')
<form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="card mb-6">
        <div class="card-header"><h3 class="card-title">Personal Information</h3></div>
        <div class="card-body grid grid-cols-2 gap-6">
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-input" required value="{{ old('name', $employee->name) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Email Address (Readonly)</label>
                <input type="email" class="form-input" value="{{ $employee->email }}" readonly style="background:var(--bg)">
            </div>
            <div class="form-group">
                <label class="form-label">New Password (leave blank to keep current)</label>
                <input type="password" name="password" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="password_confirmation" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-input" value="{{ old('phone', $employee->phone) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Profile Image (Avatar)</label>
                <input type="file" name="avatar" class="form-input" accept="image/*">
                @if($employee->avatar)
                    <div class="mt-2 text-sm text-muted">Current avatar is set.</div>
                @endif
            </div>
            <div class="form-group">
                <label class="form-label">Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-input" value="{{ old('date_of_birth', $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select">
                    <option value="male" {{ $employee->gender=='male'?'selected':'' }}>Male</option>
                    <option value="female" {{ $employee->gender=='female'?'selected':'' }}>Female</option>
                    <option value="other" {{ $employee->gender=='other'?'selected':'' }}>Other</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Marital Status</label>
                <select name="marital_status" class="form-select">
                    <option value="single" {{ $employee->marital_status=='single'?'selected':'' }}>Single</option>
                    <option value="married" {{ $employee->marital_status=='married'?'selected':'' }}>Married</option>
                    <option value="divorced" {{ $employee->marital_status=='divorced'?'selected':'' }}>Divorced</option>
                    <option value="widowed" {{ $employee->marital_status=='widowed'?'selected':'' }}>Widowed</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Number of Children</label>
                <input type="number" name="children_count" class="form-input" value="{{ old('children_count', $employee->children_count) }}" min="0">
            </div>
            <div class="form-group">
                <label class="form-label">Blood Group</label>
                <input type="text" name="blood_group" class="form-input" value="{{ old('blood_group', $employee->profile->blood_group ?? '') }}">
            </div>
            <div class="form-group" style="grid-column:span 2">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-textarea" rows="2">{{ old('address', $employee->profile->address ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="card md-6">
        <div class="card-header"><h3 class="card-title">Employment Information</h3></div>
        <div class="card-body grid grid-cols-2 gap-6">
            <div class="form-group">
                <label class="form-label">Employee ID (Readonly)</label>
                <input type="text" class="form-input" value="{{ $employee->employee_id }}" readonly style="background:var(--bg)">
            </div>
            <div class="form-group">
                <label class="form-label">System Role</label>
                <select name="role" class="form-select" required>
                    @foreach($roles as $r)
                        <option value="{{ $r->name }}" {{ ($employee->roles->first()->name ?? $employee->role) == $r->name ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $r->name)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-select" required>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ $employee->department_id == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Designation</label>
                <select name="designation_id" class="form-select" required>
                    @foreach($designations as $d)
                        <option value="{{ $d->id }}" {{ $employee->designation_id == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" id="manager_group">
                <label class="form-label">Reporting Manager</label>
                <select name="manager_id" class="form-select">
                    <option value="">None</option>
                    @foreach($managers as $m)
                        @if($m->id != $employee->id)
                            <option value="{{ $m->id }}" {{ $employee->manager_id == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Shift Assignment</label>
                <select name="shift_id" class="form-select" required>
                    @foreach($shifts as $s)
                        <option value="{{ $s->id }}" {{ $employee->shift_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Date of Joining</label>
                <input type="date" name="date_of_joining" class="form-input" required value="{{ $employee->date_of_joining ? $employee->date_of_joining->format('Y-m-d') : '' }}">
            </div>
            <div class="form-group">
                <label class="form-label">Employment Type</label>
                <select name="employment_type" class="form-select">
                    @foreach(['full_time', 'part_time', 'contract', 'intern'] as $type)
                        <option value="{{ $type }}" {{ ($employee->profile->employment_type ?? '') == $type ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    
    <div class="card md-6">
        <div class="card-header"><h3 class="card-title">Banking Information</h3></div>
        <div class="card-body grid grid-cols-2 gap-6">
            <div class="form-group"><label class="form-label">Bank Name</label><input type="text" name="bank_name" class="form-input" value="{{ old('bank_name', $employee->profile->bank_name ?? '') }}"></div>
            <div class="form-group"><label class="form-label">Account Number</label><input type="text" name="bank_account_number" class="form-input" value="{{ old('bank_account_number', $employee->profile->bank_account_number ?? '') }}"></div>
            <div class="form-group"><label class="form-label">IFSC Code</label><input type="text" name="ifsc_code" class="form-input" value="{{ old('ifsc_code', $employee->profile->ifsc_code ?? '') }}"></div>
            <div class="form-group"><label class="form-label">PAN Number</label><input type="text" name="pan_number" class="form-input" value="{{ old('pan_number', $employee->profile->pan_number ?? '') }}"></div>
        </div>
    </div>

    <div class="text-right pb-6">
        <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-outline mr-2">Cancel</a>
        <button type="submit" class="btn btn-primary">Update Employee</button>
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
