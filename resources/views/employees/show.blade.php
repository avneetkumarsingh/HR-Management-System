@extends('layouts.app')
@section('title', 'Employee Profile')

@section('content')
<div class="profile-header border" style="display: flex; flex-wrap: wrap; gap: 1.5rem; justify-content: center; text-align: center; background: var(--bg); padding: 1.5rem; border-radius: var(--radius-lg); margin-bottom: 1.5rem;">
    <img src="{{ $employee->avatar_url }}" class="profile-avatar" alt="Avatar" style="width:100px; height:100px; border-radius:50%; margin:0 auto;">
    <div class="profile-info" style="flex: 1 1 300px;">
        <h1 style="display:flex; justify-content:center; align-items:center; gap:0.75rem; flex-wrap:wrap; font-size:1.5rem; margin-bottom:0.5rem;">
            {{ $employee->name }}
            <span class="badge {{ $employee->is_active ? 'badge-present' : 'badge-danger' }}" style="font-size:0.75rem">{{ $employee->is_active ? 'Active' : 'Inactive' }}</span>
        </h1>
        <p style="color:var(--text-muted); font-size:0.9rem; margin-bottom:0.25rem;">{{ $employee->employee_id }} &nbsp; | &nbsp; {{ $employee->department->name ?? 'N/A' }}</p>
        <p style="color:var(--text-muted); font-size:0.85rem; margin-bottom:0.25rem;">{{ $employee->designation->name ?? 'N/A' }}</p>
        <p style="color:var(--text-muted); font-size:0.85rem; font-weight:500;">Joined {{ $employee->date_of_joining ? $employee->date_of_joining->format('d M, Y') : 'N/A' }}</p>
    </div>
    <div style="display:flex; flex-direction:column; gap:0.5rem; flex-wrap:wrap; justify-content:center; flex: 1 1 100%;">
        @if(auth()->user()->hasAnyRole(['admin', 'super_admin', 'hr']))
        <a href="{{ $employee->id === auth()->id() ? route('profile.show') : route('employees.edit', $employee->id) }}" class="btn btn-outline" style="width:100%">Edit</a>
        @if($employee->id !== auth()->id())
        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Deactivate this employee?')" style="width:100%">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger btn-block" style="width:100%">Deactivate</button>
        </form>
        @endif
        @endif
    </div>
</div>

<div class="tabs">
    <button class="tab-link active" onclick="switchTab('overview')">Overview</button>
    <button class="tab-link" onclick="switchTab('attendance')">Attendance</button>
    <button class="tab-link" onclick="switchTab('leaves')">Leaves</button>
</div>

<!-- Overview Tab -->
<div id="overview" class="tab-pane active" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:1.5rem;">
    <div class="card" style="margin-bottom:0;">
        <div class="card-header"><h3 class="card-title">Contact & Personal</h3></div>
        <div class="card-body text-sm">
            <div class="flex justify-between mb-2">
                <span class="text-muted">Email:</span>
                <span class="font-bold">{{ $employee->email }}</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="text-muted">Phone:</span>
                <span class="font-bold">{{ $employee->phone ?? '-' }}</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="text-muted">Gender:</span>
                <span class="font-bold">{{ ucfirst($employee->gender ?? '-') }}</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="text-muted">Blood Group:</span>
                <span class="font-bold">{{ $employee->profile->blood_group ?? '-' }}</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="text-muted">Address:</span>
                <span class="font-bold text-right" style="max-width:60%">{{ $employee->profile->address ?? '-' }}</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Employment Details</h3></div>
        <div class="card-body text-sm">
            <div class="flex justify-between mb-2">
                <span class="text-muted">Reporting Manager:</span>
                <span class="font-bold text-primary">{{ $employee->manager->name ?? 'None' }}</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="text-muted">Emp. Type:</span>
                <span class="font-bold badge badge-on_leave">{{ ucfirst(str_replace('_', ' ', $employee->profile->employment_type ?? 'Full Time')) }}</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="text-muted">Probation End:</span>
                <span class="font-bold">{{ $employee->profile->probation_end_date ? $employee->profile->probation_end_date->format('d M, Y') : '-' }}</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="text-muted">Bank Name:</span>
                <span class="font-bold">{{ $employee->profile->bank_name ?? '-' }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Tab -->
<div id="attendance" class="tab-pane">
    <div class="card">
        <div class="card-header justify-between">
            <h3 class="card-title">Recent Attendance (Last 10 Days)</h3>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Status</th>
                        <th>Reg.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employee->attendances as $att)
                    <tr>
                        <td>{{ $att->date->format('d M, Y') }}</td>
                        <td>{{ $att->check_in ? $att->check_in->format('H:i') : '--' }}</td>
                        <td>{{ $att->check_out ? $att->check_out->format('H:i') : '--' }}</td>
                        <td><span class="badge badge-{{ str_replace(' ', '_', $att->status) }}">{{ ucfirst($att->status) }}</span></td>
                        <td>
                            @if($att->is_regularized)
                                @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center p-4">No recent attendance.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Leaves Tab -->
<div id="leaves" class="tab-pane">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        @foreach($employee->leaveBalances as $bal)
        @if($bal->type->code === 'ML' && strtolower($employee->gender ?? '') !== 'female')
            @continue
        @endif
        <div class="card" style="border-left: 4px solid {{ $bal->type->color ?? 'var(--primary)' }}; margin-bottom:0">
            <div class="card-body p-4 text-center">
                <h4 style="margin:0 0 0.5rem; font-size:1rem">{{ $bal->type->code }}</h4>
                <div class="text-2xl font-bold">{{ $bal->pending }} <span class="text-sm text-muted">/ {{ $bal->allocated }}</span></div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Leave History</h3></div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Duration</th>
                        <th>Days</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employee->leaveRequests as $req)
                    <tr>
                        <td>{{ $req->type->name }}</td>
                        <td>{{ $req->from_date->format('d M') }} - {{ $req->to_date->format('d M') }}</td>
                        <td>{{ number_format($req->days, 1) }}</td>
                        <td><span class="badge badge-{{ $req->status }}">{{ ucfirst($req->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center p-4">No leave requests found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
