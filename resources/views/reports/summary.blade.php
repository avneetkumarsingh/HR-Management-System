@extends('layouts.app')
@section('title', 'Analytics Summary')

@section('content')
<div class="filter-bar">
    <div style="display: flex; align-items: center; margin-bottom: 1rem;">
        <button onclick="history.back()" class="btn btn-outline btn-sm" style="border:none; border-radius:50%; width:35px; height:35px; padding:0; display:flex; align-items:center; justify-content:center; margin-right: 1rem; background: var(--bg);" title="Go Back">
            <i class="fas fa-arrow-left"></i>
        </button>
        <h3 style="margin:0; font-size: 1.25rem;">Analytics Summary</h3>
    </div>
    <form action="{{ route('reports.summary') }}" method="GET" style="display:flex; gap:1rem; align-items:center;">
        <select name="month" class="form-select">
            @for($i=1; $i<=12; $i++)
                <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
            @endfor
        </select>
        <select name="year" class="form-select">
            @for($i=date('Y'); $i>=date('Y')-2; $i--)
                <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
        </select>
        <button type="submit" class="btn btn-primary">Analyze</button>
    </form>
</div>

<div class="grid grid-cols-3 gap-6">
    <div class="stat-card" style="flex-direction:column; align-items:flex-start; gap:0.75rem; padding:1.25rem; box-shadow:var(--shadow-md); background:var(--surface)">
        <div style="display:flex; justify-content:space-between; width:100%; align-items:center;">
            <div class="stat-label" style="margin:0; font-size:0.9rem; font-weight:600">Total Active Employees</div>
            </div>
        <div class="stat-info" style="flex:none; width:100%">
            <div class="stat-value" style="font-size:1.75rem">{{ $stats['total_employees'] }}</div>
        </div>
    </div>

    <div class="stat-card" style="flex-direction:column; align-items:flex-start; gap:0.75rem; padding:1.25rem; box-shadow:var(--shadow-md); background:var(--surface)">
        <div style="display:flex; justify-content:space-between; width:100%; align-items:center;">
            <div class="stat-label" style="margin:0; font-size:0.9rem; font-weight:600">Total Working Days</div>
            </div>
        <div class="stat-info" style="flex:none; width:100%">
            <div class="stat-value" style="font-size:1.75rem">{{ $stats['working_days'] }}</div>
        </div>
    </div>

    <div class="stat-card" style="flex-direction:column; align-items:flex-start; gap:0.75rem; padding:1.25rem; box-shadow:var(--shadow-md); background:var(--surface)">
        <div style="display:flex; justify-content:space-between; width:100%; align-items:center;">
            <div class="stat-label" style="margin:0; font-size:0.9rem; font-weight:600">Avg Attendance Rate</div>
            </div>
        <div class="stat-info" style="flex:none; width:100%">
            <div class="stat-value" style="font-size:1.75rem">{{ $stats['attendance_rate'] }}%</div>
        </div>
    </div>
    
    <div class="stat-card" style="flex-direction:column; align-items:flex-start; gap:0.75rem; padding:1.25rem; box-shadow:var(--shadow-md); background:var(--surface)">
        <div style="display:flex; justify-content:space-between; width:100%; align-items:center;">
            <div class="stat-label" style="margin:0; font-size:0.9rem; font-weight:600">Total Leave Days</div>
            </div>
        <div class="stat-info" style="flex:none; width:100%">
            <div class="stat-value" style="font-size:1.75rem">{{ number_format($stats['total_leave_days'], 1) }}</div>
        </div>
    </div>

    <div class="stat-card" style="flex-direction:column; align-items:flex-start; gap:0.75rem; padding:1.25rem; box-shadow:var(--shadow-md); background:var(--surface)">
        <div style="display:flex; justify-content:space-between; width:100%; align-items:center;">
            <div class="stat-label" style="margin:0; font-size:0.9rem; font-weight:600">Late Arrivals</div>
            </div>
        <div class="stat-info" style="flex:none; width:100%">
            <div class="stat-value" style="font-size:1.75rem">{{ $stats['late_arrivals'] }}</div>
        </div>
    </div>

    <div class="stat-card" style="flex-direction:column; align-items:flex-start; gap:0.75rem; padding:1.25rem; box-shadow:var(--shadow-md); background:var(--surface)">
        <div style="display:flex; justify-content:space-between; width:100%; align-items:center;">
            <div class="stat-label" style="margin:0; font-size:0.9rem; font-weight:600">WFH Count</div>
            </div>
        <div class="stat-info" style="flex:none; width:100%">
            <div class="stat-value" style="font-size:1.75rem">{{ $stats['wfh_count'] }}</div>
        </div>
    </div>
</div>
@endsection
