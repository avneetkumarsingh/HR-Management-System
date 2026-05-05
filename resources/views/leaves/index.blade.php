@extends('layouts.app')
@section('title', 'My Leaves')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem">
    <h3 style="margin:0">Leave Balances ({{ date('Y') }})</h3>
    <a href="{{ route('leaves.create') }}" class="btn btn-primary">Apply Leave</a>
</div>

<div class="grid grid-cols-3 gap-6 margin-bottom-2rem">
    @forelse($balances as $balance)
    @if($balance->type->code === 'ML' && strtolower(auth()->user()->gender ?? '') !== 'female')
        @continue
    @endif
    <div class="card leave-card" style="--card-color: {{ $balance->type->color ?? 'var(--primary)' }}; margin-bottom:0">
        <div class="card-body">
            <h4 style="margin:0 0 1rem">{{ $balance->type->name }}</h4>
            <div class="grid grid-cols-3 gap-2 text-center" style="margin-bottom:1rem">
                <div>
                    <div class="text-sm text-muted">Total</div>
                    <div class="font-bold">{{ number_format($balance->allocated, 1) }}</div>
                </div>
                <div style="border-left:1px solid var(--border); border-right:1px solid var(--border)">
                    <div class="text-sm text-muted">Used</div>
                    <div class="font-bold">{{ number_format($balance->used, 1) }}</div>
                </div>
                <div>
                    <div class="text-sm text-muted">Balance</div>
                    <div class="font-bold text-success">{{ number_format($balance->pending, 1) }}</div>
                </div>
            </div>
            @php $pct = $balance->allocated > 0 ? ($balance->used / $balance->allocated) * 100 : 0; @endphp
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $pct }}%"></div>
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column: span 3" class="text-muted p-4 card">No leave balances found for current year.</div>
    @endforelse
</div>

<div class="card mt-4">
    <div class="card-header"><h3 class="card-title">Leave History</h3></div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Leave Type</th>
                    <th>Dates</th>
                    <th>Days</th>
                    <th>Reason</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                <tr>
                    <td>
                        <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:{{ $req->type->color ?? '#ccc' }}; margin-right:5px"></span>
                        {{ $req->type->name }}
                    </td>
                    <td>{{ $req->from_date->format('d M y') }} - {{ $req->to_date->format('d M y') }}</td>
                    <td>{{ number_format($req->days, 1) }} Days</td>
                    <td>{{ \Illuminate\Support\Str::limit($req->reason, 30) }}</td>
                    <td><span class="badge badge-{{ $req->status }}">{{ ucfirst($req->status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted p-4">No leave history.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
