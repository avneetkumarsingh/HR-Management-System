@extends('layouts.app')
@section('title', 'Team Attendance')

@section('content')
<div class="filter-bar">
    <form action="" method="GET" style="display:flex; gap:1rem; align-items:center;">
        <input type="date" name="date" class="form-input" value="{{ $date }}" style="max-width:200px">
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>
</div>

<div class="grid grid-cols-3 gap-6">
    @forelse($team as $member)
        @php $att = $member->attendances->first(); @endphp
        <div class="card" style="margin-bottom:0">
            <div class="card-body" style="text-align:center">
                <img src="{{ $member->avatar_url }}" alt="av" style="width:80px; height:80px; border-radius:50%; margin-bottom:1rem">
                <h4 style="margin:0">{{ $member->name }}</h4>
                <p class="text-muted text-sm mb-4">{{ $member->designation->name ?? 'N/A' }}</p>
                
                <span class="badge badge-{{ $att ? str_replace(' ', '_', $att->status) : 'absent' }}" style="display:block; margin-bottom:1rem">
                    {{ $att ? ucfirst(str_replace('_', ' ', $att->status)) : 'Absent' }}
                </span>

                <div class="grid grid-cols-2 gap-2" style="border-top:1px solid var(--border); padding-top:1rem; text-align:left">
                    <div>
                        <div class="text-muted text-sm">Check In</div>
                        <div class="font-bold">{{ $att && $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '--:--' }}</div>
                    </div>
                    <div>
                        <div class="text-muted text-sm">Check Out</div>
                        <div class="font-bold">{{ $att && $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '--:--' }}</div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div style="grid-column: span 3" class="text-center text-muted p-4">No team members found.</div>
    @endforelse
</div>
@endsection
