@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

<!-- Recent Announcements Alert Banner -->
@if(isset($announcements) && $announcements->count() > 0)
    <style>
        @keyframes alert-ring {
            0% { transform: rotate(0); }
            5% { transform: rotate(15deg); }
            10% { transform: rotate(-10deg); }
            15% { transform: rotate(5deg); }
            20% { transform: rotate(-5deg); }
            25% { transform: rotate(0); }
            100% { transform: rotate(0); }
        }
        @keyframes alert-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
    </style>
    <div id="announcements-banner" style="margin-bottom: 2rem; display: flex; flex-direction: column; gap: 1rem; transition: opacity 0.5s ease-out, max-height 0.5s ease-out, margin 0.5s ease-out; opacity: 1; max-height: 2000px; overflow: hidden;">
        @foreach($announcements as $announcement)
            <div style="background: {{ $announcement->type == 'critical' ? '#fef2f2' : ($announcement->type == 'event' ? '#fffbeb' : '#eff6ff') }}; border: 1px solid {{ $announcement->type == 'critical' ? '#fecaca' : ($announcement->type == 'event' ? '#fde68a' : '#bfdbfe') }}; border-left: 4px solid {{ $announcement->type == 'critical' ? '#ef4444' : ($announcement->type == 'event' ? '#f59e0b' : '#3b82f6') }}; padding: 1.25rem 1.5rem; border-radius: var(--radius-md); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); display: flex; align-items: flex-start; gap: 1.25rem; position: relative;">
                
                <!-- Icon -->
                <div style="font-size: 1.75rem; color: {{ $announcement->type == 'critical' ? '#ef4444' : ($announcement->type == 'event' ? '#f59e0b' : '#3b82f6') }}; padding-top: 0.1rem;">
                    @if($announcement->type == 'critical')
                        <i class="fas fa-exclamation-triangle" style="animation: alert-pulse 2s infinite;"></i>
                    @elseif($announcement->type == 'event')
                        <i class="fas fa-star"></i>
                    @else
                        <i class="fas fa-bell" style="animation: alert-ring 4s infinite; transform-origin: top center;"></i>
                    @endif
                </div>

                <!-- Content -->
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
                        <h4 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: #111827; display: flex; align-items: center; gap: 0.75rem;">
                            {{ $announcement->title }}
                            <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; background: {{ $announcement->type == 'critical' ? '#ef4444' : ($announcement->type == 'event' ? '#f59e0b' : '#3b82f6') }}; color: white; padding: 0.2rem 0.6rem; border-radius: 9999px;">
                                Notification
                            </span>
                        </h4>
                        <div style="font-size: 0.85rem; color: #6b7280; font-weight: 500; display: flex; align-items: center; gap: 1rem;">
                            <span><i class="fas fa-user-circle mr-1"></i> {{ $announcement->author->name ?? 'HR Team' }}</span>
                            <span><i class="fas fa-clock mr-1"></i> {{ $announcement->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div style="font-size: 0.95rem; color: #374151; line-height: 1.6; white-space: pre-wrap;">{{ $announcement->content }}</div>
                </div>
            </div>
        @endforeach
    </div>
    <script>
        setTimeout(function() {
            var banner = document.getElementById('announcements-banner');
            if (banner) {
                banner.style.opacity = '0';
                banner.style.maxHeight = '0';
                banner.style.marginBottom = '0';
                setTimeout(function() { banner.style.display = 'none'; }, 500);
            }
        }, 60000); // 60 seconds
    </script>
@endif
<div class="card hero-card">
    <div class="card-body">
        <div class="hero-info">
            <h2>{{ \Carbon\Carbon::parse($today)->format('l, F j, Y') }}</h2>
            <p>Welcome back, {{ $user->name }}!</p>
            
            <div style="margin-top:2rem; display:flex; gap:2rem">
                <div>
                    <div style="font-size:0.85rem; opacity:0.8; margin-bottom:0.25rem">Check In</div>
                    <div style="font-size:1.25rem; font-weight:600">{{ $todayAttendance && $todayAttendance->check_in ? \Carbon\Carbon::parse($todayAttendance->check_in)->format('H:i') : '--:--' }}</div>
                </div>
                <div>
                    <div style="font-size:0.85rem; opacity:0.8; margin-bottom:0.25rem">Check Out</div>
                    <div style="font-size:1.25rem; font-weight:600">{{ $todayAttendance && $todayAttendance->check_out ? \Carbon\Carbon::parse($todayAttendance->check_out)->format('H:i') : '--:--' }}</div>
                </div>
            </div>
        </div>
        <div class="hero-action text-right">
            <h1 id="live-hours">00:00</h1>
            <span class="badge badge-present" style="font-size:1rem; padding:0.5rem 1rem">
                {{ $todayAttendance ? ucfirst(str_replace('_', ' ', $todayAttendance->status)) : 'Not Checked In' }}
            </span>
        </div>
    </div>
</div>

<div class="stat-grid" style="display:grid; grid-template-columns: repeat(6, 1fr); gap: 1rem;">
    <div class="stat-card" style="flex-direction:column; align-items:flex-start; gap:0.75rem; padding:1.25rem">
        <div style="display:flex; justify-content:space-between; width:100%; align-items:center;">
            <div class="stat-label" style="margin:0; font-size:0.9rem; font-weight:600">Present</div>
            </div>
        <div class="stat-info" style="flex:none; width:100%">
            <div class="stat-value" style="font-size:1.75rem">{{ $stats['present'] }}</div>
        </div>
    </div>

    <div class="stat-card" style="flex-direction:column; align-items:flex-start; gap:0.75rem; padding:1.25rem">
        <div style="display:flex; justify-content:space-between; width:100%; align-items:center;">
            <div class="stat-label" style="margin:0; font-size:0.9rem; font-weight:600">Absent</div>
            </div>
        <div class="stat-info" style="flex:none; width:100%">
            <div class="stat-value" style="font-size:1.75rem">{{ $stats['absent'] }}</div>
        </div>
    </div>

    <div class="stat-card" style="flex-direction:column; align-items:flex-start; gap:0.75rem; padding:1.25rem">
        <div style="display:flex; justify-content:space-between; width:100%; align-items:center;">
            <div class="stat-label" style="margin:0; font-size:0.9rem; font-weight:600">Late Arrivals</div>
            </div>
        <div class="stat-info" style="flex:none; width:100%">
            <div class="stat-value" style="font-size:1.75rem">{{ $stats['late'] }}</div>
        </div>
    </div>

    <div class="stat-card" style="flex-direction:column; align-items:flex-start; gap:0.75rem; padding:1.25rem">
        <div style="display:flex; justify-content:space-between; width:100%; align-items:center;">
            <div class="stat-label" style="margin:0; font-size:0.9rem; font-weight:600">On Leave</div>
            </div>
        <div class="stat-info" style="flex:none; width:100%">
            <div class="stat-value" style="font-size:1.75rem">{{ $stats['leave'] }}</div>
        </div>
    </div>

    <div class="stat-card" style="flex-direction:column; align-items:flex-start; gap:0.75rem; padding:1.25rem">
        <div style="display:flex; justify-content:space-between; width:100%; align-items:center;">
            <div class="stat-label" style="margin:0; font-size:0.9rem; font-weight:600">Work from Home</div>
            </div>
        <div class="stat-info" style="flex:none; width:100%">
            <div class="stat-value" style="font-size:1.75rem">{{ $stats['wfh'] }}</div>
        </div>
    </div>

    <div class="stat-card" style="flex-direction:column; align-items:flex-start; gap:0.75rem; padding:1.25rem">
        <div style="display:flex; justify-content:space-between; width:100%; align-items:center;">
            <div class="stat-label" style="margin:0; font-size:0.9rem; font-weight:600">Avg. Hours</div>
            </div>
        <div class="stat-info" style="flex:none; width:100%">
            <div class="stat-value" style="font-size:1.75rem">{{ number_format($stats['avg_hours'], 1) }}h</div>
        </div>
    </div>
</div>

<!-- Today's Personal Log -->
<div class="card mb-6" style="border-top: 4px solid var(--primary);">
    <div class="card-header"><h3 class="card-title">Your Log for Today</h3></div>
    <div class="card-body">
        @if($todayAttendance)
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem">
                <div style="flex:1; min-width:150px; background:var(--bg); padding:1rem; border-radius:var(--radius-sm); text-align:center;">
                    <div style="font-size:0.85rem; color:var(--text-muted); margin-bottom:0.25rem">Check In</div>
                    <div style="font-size:1.25rem; font-weight:600; color:var(--success)">
                        {{ $todayAttendance->check_in ? \Carbon\Carbon::parse($todayAttendance->check_in)->format('H:i A') : '--:--' }}
                    </div>    
                </div>
                <div style="flex:1; min-width:150px; background:var(--bg); padding:1rem; border-radius:var(--radius-sm); text-align:center;">
                    <div style="font-size:0.85rem; color:var(--text-muted); margin-bottom:0.25rem">Check Out</div>
                    <div style="font-size:1.25rem; font-weight:600; color:var(--danger)">
                        {{ $todayAttendance->check_out ? \Carbon\Carbon::parse($todayAttendance->check_out)->format('H:i A') : '--:--' }}
                    </div>
                </div>
                <div style="flex:1; min-width:150px; background:var(--bg); padding:1rem; border-radius:var(--radius-sm); text-align:center;">
                    <div style="font-size:0.85rem; color:var(--text-muted); margin-bottom:0.25rem">Total Effective Hours</div>
                    <div style="font-size:1.5rem; font-weight:700; color:var(--primary)">
                        @if($todayAttendance->check_out)
                            {{ number_format($todayAttendance->working_hours, 2) }} <span style="font-size:1rem">hrs</span>
                        @else
                            <span id="live-hours-dashboard" style="font-variant-numeric: tabular-nums;">00:00</span> <span style="font-size:1rem; font-weight:500; font-variant-numeric: normal;">hrs</span>
                        @endif
                    </div>
                </div>
            </div>
            @if(!$todayAttendance->check_out)
                <div style="margin-top:1rem; text-align:center; font-size:0.9rem; color:var(--text-muted)">
                    Currently Clocked In
                </div>
            @endif
        @else
            <div style="text-align:center; padding:2rem; color:var(--text-muted)">
                <p>You haven't checked in today. Please use the Web Check-in button in the top right.</p>
            </div>
        @endif
    </div>
</div>

    <!-- Team Today (Restricted contextually in DashboardController) -->
    @if(isset($managerData['team_today']))
    <div class="card" style="border-top: 4px solid var(--info);">
        <div class="card-header"><h3 class="card-title">Team Today</h3></div>
        <div class="card-body">
            @if(count($managerData['team_today']) > 0)
                @php
                    $groupedTeam = collect($managerData['team_today'])->groupBy(function($member) {
                        return $member->department ? $member->department->name : 'Other';
                    });
                @endphp
                
                @foreach($groupedTeam as $deptName => $members)
                    <div style="margin-bottom: 1.5rem">
                        <h4 style="margin-top: 0; margin-bottom: 0.75rem; font-size: 0.95rem; font-weight: 600; color: var(--text-muted); border-bottom: 1px solid var(--border); padding-bottom: 0.25rem;">
                            {{ $deptName }} Team
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                        @foreach($members as $member)
                            @php $att = $member->attendances->first(); @endphp
                            <div style="display:flex; align-items:center; gap:0.75rem; padding:0.75rem; border:1px solid var(--border); border-radius:var(--radius-sm); background: #fafafa;">
                                <img src="{{ $member->avatar_url }}" alt="av" style="width:40px; height:40px; border-radius:50%">
                                <div style="flex:1">
                                    <div style="font-weight:500; font-size:0.9rem">{{ $member->name }}</div>
                                    <div style="font-size:0.75rem; color:var(--text-muted)">{{ $att && $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '--:--' }}</div>
                                </div>
                                <span class="badge badge-{{ $att ? str_replace(' ', '_', $att->status) : 'absent' }}">
                                    {{ $att ? ucfirst(str_replace('_', ' ', $att->status)) : 'Absent' }}
                                </span>
                            </div>
                        @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-muted text-center" style="padding:2rem">No team members found.</p>
            @endif
        </div>
    </div>
    @endif

    <!-- Upcoming Holidays -->
    <div class="card" style="border-top: 4px solid var(--warning);">
        <div class="card-header"><h3 class="card-title">Upcoming Holidays</h3></div>
        <div class="card-body" style="padding:0">
            <ul style="list-style:none">
                @forelse($upcomingHolidays as $holiday)
                <li style="padding:1rem 1.5rem; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center">
                    <div>
                        <div style="font-weight:500">{{ $holiday->name }}</div>
                        <div style="font-size:0.85rem; color:var(--text-muted)">{{ \Carbon\Carbon::parse($holiday->date)->format('l, F j') }}</div>
                    </div>
                    <span class="badge badge-holiday">{{ ucfirst($holiday->type) }}</span>
                </li>
                @empty
                <li style="padding:2rem; text-align:center; color:var(--text-muted)">No upcoming holidays.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <!-- Upcoming Celebrations -->
    <div class="card" style="grid-column: span 1; border-top: 4px solid var(--danger);">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h3 class="card-title">Upcoming Birthdays <span style="font-size:0.85rem;font-weight:normal;color:var(--text-muted)"></span></h3>
        </div>
        <div class="card-body" style="padding:0">
            <ul style="list-style:none">
                @forelse($upcomingBirthdays ?? [] as $birthdayUser)
                <li style="padding:1rem 1.5rem; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; gap:1rem;">
                    <div style="display:flex; align-items:center; gap:1rem;">
                        <img src="{{ $birthdayUser->avatar_url }}" alt="avatar" style="width:40px; height:40px; border-radius:50%">
                        <div>
                            <div style="font-weight:500">{{ $birthdayUser->name }}</div>
                            <div style="font-size:0.85rem; color:var(--text-muted)">{{ $birthdayUser->designation ? $birthdayUser->designation->name : 'Employee' }}</div>
                        </div>
                    </div>
                    <div style="text-align:right">
                        @php 
                           $bDate = \Carbon\Carbon::parse($birthdayUser->date_of_birth); 
                           $bDate->year = now()->year;
                           if ($bDate->isPast()) $bDate->addYear();
                        @endphp
                        <div style="font-weight:600; color:var(--primary)">{{ $bDate->format('d M') }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted)">{{ $bDate->diffForHumans() }}</div>
                    </div>
                </li>
                @empty
                <li style="padding:2rem; text-align:center; color:var(--text-muted)">No upcoming birthdays.</li>
                @endforelse 
            </ul>       
        </div>
    </div>
    
    <div class="card" style="grid-column: span 1; border-top: 4px solid var(--primary);">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h3 class="card-title">Work Anniversaries <span style="font-size:0.85rem;font-weight:normal;color:var(--text-muted)"></span></h3>
        </div>
        <div class="card-body" style="padding:0">
            <ul style="list-style:none">
                @forelse($upcomingAnniversaries ?? [] as $anniUser)
                <li style="padding:1rem 1.5rem; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; gap:1rem;">
                    <div style="display:flex; align-items:center; gap:1rem;">
                        <img src="{{ $anniUser->avatar_url }}" alt="avatar" style="width:40px; height:40px; border-radius:50%">
                        <div>
                            <div style="font-weight:500">{{ $anniUser->name }}</div>
                            @php
                                $years = \Carbon\Carbon::parse($anniUser->date_of_joining)->diffInYears(now()) + 1;
                            @endphp
                            <div style="font-size:0.85rem; color:var(--text-muted)">Celebrating {{ $years }} Year{{ $years > 1 ? 's' : '' }}</div>
                        </div>
                    </div>
                    <div style="text-align:right">
                        @php 
                           $aDate = \Carbon\Carbon::parse($anniUser->date_of_joining);
                           $aDate->year = now()->year;
                           if ($aDate->isPast()) $aDate->addYear();
                        @endphp
                        <div style="font-weight:600; color:var(--success)">{{ $aDate->format('d M') }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted)">{{ $aDate->diffForHumans() }}</div>
                    </div>
                </li>
                @empty
                <li style="padding:2rem; text-align:center; color:var(--text-muted)">No upcoming anniversaries.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Live hours calc if checked in
    @if($todayAttendance && $todayAttendance->check_in && !$todayAttendance->check_out)
        // Ensure timezone string is parsed safely by appending 'Z' or parsing local time if required
        // Since we are generating the string from PHP, let's use the raw Unix timestamp directly to avoid JS timezone bugs
        const checkInTime = {{ strtotime($todayAttendance->check_in) * 1000 }};
        
        function updateWorkingHours() {
            const now = new Date().getTime();
            // Since JS now() runs on the local PC clock and server clock might differ by a few seconds,
            // we calculate the difference.
            const diff = Math.max(0, now - checkInTime);
            
            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            
            const textToDisplay = (hours < 10 ? "0" + hours : hours) + ":" + (minutes < 10 ? "0" + minutes : minutes);
            
            const liveEls = document.querySelectorAll('#live-hours, #live-hours-dashboard');
            liveEls.forEach(el => { if(el) el.textContent = textToDisplay; });
        }
        
        setInterval(updateWorkingHours, 1000); // Check more frequently so clock seems alive
        updateWorkingHours();
    @elseif($todayAttendance && $todayAttendance->check_out)
        const h = {{ floor($todayAttendance->working_hours) }};
        const m = {{ round(($todayAttendance->working_hours - floor($todayAttendance->working_hours)) * 60) }};
        const statText = (h < 10 ? "0" + h : h) + ":" + (m < 10 ? "0" + m : m);
        const liveEls = document.querySelectorAll('#live-hours, #live-hours-dashboard');
        liveEls.forEach(el => { if(el) el.textContent = statText; });
    @endif
</script>
@endpush
@endsection
