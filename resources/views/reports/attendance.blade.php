@extends('layouts.app')
@section('title', 'Attendance Report')

@section('content')
<div class="filter-bar">
    <form action="{{ route('reports.attendance') }}" method="GET" style="display:flex; gap:1rem; align-items:center;">
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
        <button type="submit" class="btn btn-primary">Generate Report</button>
        <button type="button" class="btn btn-outline" style="margin-left:auto">Export Excel</button>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Dept</th>
                    <th>Present</th>
                    <th>Absent</th>
                    <th>Leave</th>
                    <th>Late</th>
                    <th>Total Hrs</th>
                    <th>Avg Hrs</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report as $row)
                <tr>
                    <td>
                        <div class="font-bold cursor-pointer" onclick="window.location='{{ route('employees.show', $row['employee']->id) }}'">
                            {{ $row['employee']->name }}
                        </div>
                        <div class="text-sm text-muted">{{ $row['employee']->employee_id }}</div>
                    </td>
                    <td>{{ $row['employee']->department->code ?? '-' }}</td>
                    <td><span class="text-success font-bold">{{ $row['present'] }}</span></td>
                    <td><span class="text-danger font-bold">{{ $row['absent'] }}</span></td>
                    <td><span class="text-info font-bold">{{ $row['leave'] }}</span></td>
                    <td><span class="text-warning font-bold">{{ $row['late'] }}</span></td>
                    <td>{{ number_format($row['total_hours'], 1) }}h</td>
                    <td>{{ number_format($row['avg_hours'], 1) }}h</td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center p-4">No data available.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
