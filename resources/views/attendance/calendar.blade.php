@extends('layouts.app')
@section('title', 'Attendance Calendar')

@section('content')
<div class="filter-bar justify-between">
    <h3 style="margin:0; font-size:1.25rem">{{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</h3>
    <div style="display:flex; gap:0.5rem">
        <a href="{{ route('attendance.calendar', ['month' => $month == 1 ? 12 : $month-1, 'year' => $month == 1 ? $year-1 : $year]) }}" class="btn btn-outline">Prev</a>
        <a href="{{ route('attendance.calendar', ['month' => $month == 12 ? 1 : $month+1, 'year' => $month == 12 ? $year+1 : $year]) }}" class="btn btn-outline">Next </a>
    </div>
</div>

<div style="display:flex; gap:1.5rem; margin-bottom:2rem;">
    <div class="card" id="card-attendance" onclick="showCalendar('attendance')" style="flex:1; padding:2rem; text-align:center; cursor:pointer; border: 2px solid var(--primary); transition:all 0.2s;">
        <h3 style="margin:0 0 0.5rem;">Attendance Calendar</h3>
        <div style="font-size:0.9rem; color:var(--text-muted)">View your precise daily check-ins and work hours.</div>
    </div>
    <div class="card" id="card-holiday" onclick="showCalendar('holiday')" style="flex:1; padding:2rem; text-align:center; cursor:pointer; border: 2px solid transparent; transition:all 0.2s;">
        <h3 style="margin:0 0 0.5rem;">Holidays & Events</h3>
        <div style="font-size:0.9rem; color:var(--text-muted)">View national holidays and team scheduled outings.</div>
    </div>
</div>

<div id="calendar-attendance" style="display:block;">
    <div style="display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); gap: 1.5rem;">
        <div class="card" style="margin-bottom:0">
            <div class="card-header" style="border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1rem;">
                <h3 class="card-title" style="margin: 0;">Attendance Calendar</h3>
            </div>
            <div class="calendar-grid">
                <div class="calendar-header-cell">Sun</div>
                <div class="calendar-header-cell">Mon</div>
                <div class="calendar-header-cell">Tue</div>
                <div class="calendar-header-cell">Wed</div>
                <div class="calendar-header-cell">Thu</div>
                <div class="calendar-header-cell">Fri</div>
                <div class="calendar-header-cell">Sat</div>

            @php
                $firstDay = \Carbon\Carbon::createFromDate($year, $month, 1);
                $daysInMonth = $firstDay->daysInMonth;
                $startDayOfWeek = $firstDay->dayOfWeek; // 0 (Sun) to 6 (Sat)
            @endphp

            @for($i = 0; $i < $startDayOfWeek; $i++)
                <div class="calendar-cell" style="background:var(--bg)"></div>
            @endfor

            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
                    $isToday = $dateStr == date('Y-m-d');
                    $att = $attendances[$dateStr] ?? null;
                @endphp
                <div class="calendar-cell {{ $isToday ? 'today' : '' }}">
                    <div class="calendar-date">{{ $day }}</div>
                    @if($att)
                        <div class="calendar-status badge-{{ str_replace(' ', '_', $att->status) }}">
                            {{ ucfirst(str_replace('_', ' ', $att->status)) }}
                        </div>
                        @if($att->check_in)
                            <div style="font-size:0.75rem; text-align:center; color:var(--text-muted)">
                                {{ \Carbon\Carbon::parse($att->check_in)->format('H:i') }}
                            </div>
                        @endif
                    @endif
                </div>
            @endfor
        </div>
        </div>
        
        <div class="card" style="margin-bottom:0">
            <div class="card-header" style="border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1rem;">
                <h3 class="card-title" style="margin: 0;">This Month's Summary</h3>
            </div>
            
            @php
                $present = clone $attendances;
                $present = $present->where('status', 'present')->count();
                $absent = clone $attendances;
                $absent = $absent->where('status', 'absent')->count();
                $leave = clone $attendances;
                $leave = $leave->where('status', 'on_leave')->count();
                
                $totalWorkingDays = 0;
                for($d = 1; $d <= $daysInMonth; $d++) {
                    $dateObj = \Carbon\Carbon::createFromDate($year, $month, $d);
                    if (!$dateObj->isWeekend()) {
                        $totalWorkingDays++;
                    }
                }
            @endphp
            
            <div class="card-body" style="display:flex; flex-direction:column; gap:1.5rem; padding-top:0;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div style="font-weight:600; color:var(--text-muted)">Total Work Days</div>
                    <div style="font-size:1.5rem; font-weight:700">{{ $totalWorkingDays }}</div>
                </div>
                
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div style="font-weight:600; color:var(--text-muted)">Present Days</div>
                    <div style="font-size:1.5rem; font-weight:700; color:#10b981;">{{ $present }}</div>
                </div>
                
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div style="font-weight:600; color:var(--text-muted)">Absent Days</div>
                    <div style="font-size:1.5rem; font-weight:700; color:#ef4444;">{{ $absent }}</div>
                </div>
                
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div style="font-weight:600; color:var(--text-muted)">On Leave</div>
                    <div style="font-size:1.5rem; font-weight:700; color:#f59e0b;">{{ $leave }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
    
<div id="calendar-holiday" style="display:none;">
    <div style="display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); gap: 1.5rem;">
        <div class="card" style="margin-bottom:0">
            <div class="card-header" style="border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1rem;">
                <h3 class="card-title" style="margin: 0;">Event & Holiday Calendar</h3>
            </div>
            <div class="calendar-grid">
                <div class="calendar-header-cell">Sun</div>
                <div class="calendar-header-cell">Mon</div>
                <div class="calendar-header-cell">Tue</div>
                <div class="calendar-header-cell">Wed</div>
                <div class="calendar-header-cell">Thu</div>
                <div class="calendar-header-cell">Fri</div>
                <div class="calendar-header-cell">Sat</div>

                @for($i = 0; $i < $startDayOfWeek; $i++)
                    <div class="calendar-cell" style="background:var(--bg)"></div>
                @endfor

                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
                        $isToday = $dateStr == date('Y-m-d');
                        $hol = $holidays[$dateStr] ?? null;
                    @endphp
                    <div class="calendar-cell {{ $isToday ? 'today' : '' }}">
                        <div class="calendar-date">{{ $day }}</div>
                        @if($hol)
                            <div class="calendar-status bg-{{ $hol->type == 'event' ? 'blue-500' : 'orange-100' }}" style="font-size:0.7rem; white-space:normal; line-height:1.2; text-align:center; padding:2px; border-radius:4px; margin-top:2px; {{ $hol->type == 'event' ? 'background:#3b82f6; color:white;' : 'background:#fff7ed; color:#c2410c;' }}">
                                {{ $hol->name }}
                            </div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>

        <div class="card" style="margin-bottom:0">
            <div class="card-header" style="border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1rem;">
                <h3 class="card-title" style="margin: 0;">This Month's Events</h3>
            </div>
            
            <div class="card-body" style="display:flex; flex-direction:column; gap:1.0rem; padding-top:0;">
                @php
                    $thisMonthHols = collect($holidays)->filter(function($hol, $date) use ($year, $month) {
                        $pd = \Carbon\Carbon::parse($date);
                        return $pd->year == $year && $pd->month == $month;
                    })->sortBy(function($hol, $date) {
                        return $date;
                    });
                @endphp
                
                @if($thisMonthHols->count() > 0)
                    @foreach($thisMonthHols as $date => $hol)
                        @php 
                            $pd = \Carbon\Carbon::parse($date); 
                        @endphp
                        <div style="display:flex; justify-content:space-between; align-items:center; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border);">
                            <div>
                                <div style="font-weight:600; color:var(--text); margin-bottom:0.25rem;">{{ $hol->name }}</div>
                                <div style="font-size:0.75rem; color:var(--text-muted)">
                                    <span style="display:inline-block; width:8px; height:8px; border-radius:50%; margin-right:4px; {{ $hol->type == 'event' ? 'background:#3b82f6;' : 'background:#f59e0b;' }}"></span>
                                    {{ $hol->type == 'event' ? 'Team Event' : 'Holiday' }}
                                </div>
                            </div>
                            <div style="text-align:center; background: var(--bg); padding: 0.5rem; border-radius: var(--radius-sm); min-width: 60px; border: 1px solid var(--border);">
                                <div style="font-size:1.1rem; font-weight:700; color:var(--text); line-height:1;">{{ $pd->format('d') }}</div>
                                <div style="font-size:0.7rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; margin-top:2px;">{{ $pd->format('D') }}</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div style="text-align:center; padding: 2rem 0; color:var(--text-muted); font-size: 0.9rem;">
                        No holidays or events scheduled for this month.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showCalendar(type) {
    document.getElementById('calendar-attendance').style.display = type === 'attendance' ? 'block' : 'none';
    document.getElementById('calendar-holiday').style.display = type === 'holiday' ? 'block' : 'none';
    
    document.getElementById('card-attendance').style.borderColor = type === 'attendance' ? 'var(--primary)' : 'transparent';
    document.getElementById('card-holiday').style.borderColor = type === 'holiday' ? 'var(--primary)' : 'transparent';
}
</script>
@endpush
@endsection
