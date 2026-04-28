@extends('layouts.app')
@section('title', 'Daily Attendance Report')

@section('content')
<div class="filter-bar">
    <form action="{{ route('attendance.report') }}" method="GET" style="display:flex; gap:1rem; align-items:center;">
        <input type="date" name="date" class="form-input" value="{{ $date }}" required>
        <button type="submit" class="btn btn-primary">Refresh</button>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Hours</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $emp)
                @php $att = $emp->attendances->first(); @endphp
                <tr>
                    <td>{{ $emp->name }}</td>
                    <td>{{ $emp->department->name ?? '-' }}</td>
                    <td>
                        <span class="badge badge-{{ $att ? str_replace(' ', '_', $att->status) : 'absent' }}">
                            {{ $att ? ucfirst(str_replace('_', ' ', $att->status)) : 'Absent' }}
                        </span>
                    </td>
                    <td>{{ $att && $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '--:--' }}</td>
                    <td>{{ $att && $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '--:--' }}</td>
                    <td>{{ $att ? number_format($att->working_hours, 1) : '0' }}h</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $attendances->links('pagination::bootstrap-4') }}</div>
@endsection
