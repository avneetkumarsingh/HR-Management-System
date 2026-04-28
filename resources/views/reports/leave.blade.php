@extends('layouts.app')
@section('title', 'Leave Report')

@section('content')
<div class="filter-bar justify-between">
    <form action="{{ route('reports.leave') }}" method="GET" style="display:flex; gap:1rem; align-items:center;">
        <select name="year" class="form-select" onchange="this.form.submit()">
            @for($i=date('Y'); $i>=date('Y')-2; $i--)
                <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
        </select>
    </form>
    <div>
        <span class="badge badge-present p-2 mr-2">Total: {{ $stats['total'] }}</span>
        <span class="badge badge-approved p-2 mr-2">Approved: {{ $stats['approved'] }}</span>
        <span class="badge badge-pending p-2 mr-2">Pending: {{ $stats['pending'] }}</span>
        <span class="badge badge-rejected p-2">Rejected: {{ $stats['rejected'] }}</span>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Leave Type</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Days</th>
                    <th>Status</th>
                    <th>Appr. By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaves as $req)
                <tr>
                    <td>{{ $req->user->name }}</td>
                    <td>{{ $req->type->name }}</td>
                    <td>{{ $req->from_date->format('d M, Y') }}</td>
                    <td>{{ $req->to_date->format('d M, Y') }}</td>
                    <td>{{ number_format($req->days, 1) }}</td>
                    <td><span class="badge badge-{{ $req->status }}">{{ ucfirst($req->status) }}</span></td>
                    <td>{{ $req->approver->name ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center p-4">No extended leave history.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $leaves->links('pagination::bootstrap-4') }}</div>
@endsection
