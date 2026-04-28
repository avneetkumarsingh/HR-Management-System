@extends('layouts.app')
@section('title', 'My Attendance')

@section('content')
<div class="filter-bar">
    <form action="{{ route('attendance.index') }}" method="GET" style="display:flex; gap:1rem; align-items:center; width:100%">
        <select name="month" class="form-select" style="max-width:200px">
            @for($i=1; $i<=12; $i++)
                <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
            @endfor
        </select>
        <select name="year" class="form-select" style="max-width:150px">
            @for($i=date('Y'); $i>=date('Y')-2; $i--)
                <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        
        <div style="margin-left:auto; display:flex; gap:0.5rem">
            <span class="badge badge-present">Present: {{ $stats['present'] }}</span>
            <span class="badge badge-absent">Absent: {{ $stats['absent'] }}</span>
            <span class="badge badge-leave">Leave: {{ $stats['leave'] }}</span>
            <span style="padding:0.25rem 0.75rem; background:var(--bg); border-radius:99px; font-size:0.85rem; font-weight:600">Total Hours: {{ number_format($stats['total_hours'], 1) }}h</span>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Day</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Working Hours</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $att)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($att->date)->format('M d, Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($att->date)->format('l') }}</td>
                    <td>{{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '--:--' }}</td>
                    <td>{{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '--:--' }}</td>
                    <td>{{ number_format($att->working_hours, 2) }} hrs</td>
                    <td><span class="badge badge-{{ str_replace(' ', '_', $att->status) }}">{{ ucfirst(str_replace('_', ' ', $att->status)) }}</span></td>
                    <td>
                        @if(!$att->is_regularized)
                            <a href="{{ route('regularization.index') }}?date={{ $att->date }}" class="btn btn-sm btn-outline">Regularize</a>
                        @else
                            <span class="text-success text-sm">Regularized</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted p-4">No attendance records found for this month.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
