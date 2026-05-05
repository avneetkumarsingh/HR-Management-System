@extends('layouts.app')
@section('title', 'Team Attendance')

@section('content')
<div class="filter-bar">
    <form action="" method="GET" style="display:flex; gap:1rem; align-items:center;">
        <input type="date" name="date" class="form-input" value="{{ $date }}" style="max-width:200px">
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>
</div>

@php
    $groupedTeam = $team->groupBy(function($m) { return $m->department->name ?? 'Unassigned'; });
@endphp

<style>
    .rotate-180 { transform: rotate(180deg); }
    .hidden-block { display: none !important; }
</style>

@forelse($groupedTeam as $groupName => $members)
<div class="card mb-6">
    <div class="card-header" style="background: rgba(0,0,0,0.02); cursor: pointer;" onclick="document.getElementById('team-{{ \Illuminate\Support\Str::slug($groupName) }}').classList.toggle('hidden-block'); this.querySelector('i').classList.toggle('rotate-180')">
        <h3 class="card-title" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <span>{{ $groupName }} <span class="badge" style="background:var(--border); margin-left:10px">{{ count($members) }} Members</span></span>
            <i class="fas fa-chevron-down rotate-180" style="transition: transform 0.3s ease;"></i>
        </h3>
    </div>
    <div id="team-{{ \Illuminate\Support\Str::slug($groupName) }}" class="table-responsive hidden-block">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:280px">Employee</th>
                    <th>Status</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th style="text-align:right">Total Hours</th>
                </tr>
            </thead>
            <tbody>
                @foreach($members as $member)
                    @php $att = $member->attendances->first(); @endphp
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:12px;">
                                <img src="{{ $member->avatar_url }}" alt="avatar" style="width:40px; height:40px; border-radius:50%; object-fit:cover; background:#fff">
                                <div>
                                    <div style="font-weight:600;">{{ $member->name }}</div>
                                    <div style="font-size:0.75rem; color:var(--text-muted)">{{ $member->designation->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $att ? str_replace(' ', '_', $att->status) : 'absent' }}">
                                {{ $att ? ucfirst(str_replace('_', ' ', $att->status)) : 'Absent' }}
                            </span>
                        </td>
                        <td style="font-variant-numeric:tabular-nums; font-weight:600; color:var(--text)">
                            {{ $att && $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '--:--' }}
                        </td>
                        <td style="font-variant-numeric:tabular-nums; font-weight:600; color:var(--text)">
                            {{ $att && $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '--:--' }}
                        </td>
                        <td style="text-align:right; font-variant-numeric:tabular-nums; font-weight:500;">
                            @if($att && $att->working_hours)
                                {{ number_format($att->working_hours, 1) }} hrs
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@empty
<div class="card p-8 text-center text-muted" style="border: 2px dashed var(--border)">
    No team attendance records found for this date.
</div>
@endforelse
@endsection
