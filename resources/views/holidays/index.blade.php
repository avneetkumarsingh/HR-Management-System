@extends('layouts.app')
@section('title', 'Holidays')

@section('content')
<div class="filter-bar justify-between">
    <div style="display:flex; gap:1rem; align-items:center;">
        <h3 style="margin:0; font-size:1.25rem">{{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</h3>
        <div style="display:flex; gap:0.5rem">
            <a href="{{ route('holidays.index', ['month' => $month == 1 ? 12 : $month-1, 'year' => $month == 1 ? $year-1 : $year]) }}" class="btn btn-outline">Prev</a>
            <a href="{{ route('holidays.index', ['month' => $month == 12 ? 1 : $month+1, 'year' => $month == 12 ? $year+1 : $year]) }}" class="btn btn-outline">Next</a>
        </div>
    </div>
    @if(auth()->user()->hasAnyRole(['admin', 'super_admin', 'hr']))
    <button class="btn btn-primary" onclick="openModal('add-holiday-modal')">Add Holiday</button>
    @endif
</div>

<div style="display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); gap: 1.5rem;">
    <!-- Calendar Grid -->
    <div class="card" style="margin-bottom:0">
        <div class="card-header" style="border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1rem;">
            <h3 class="card-title" style="margin: 0;">Holiday Calendar</h3>
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
                    $hol = $holidaysByDate[$dateStr] ?? null;
                @endphp
                <div class="calendar-cell {{ $isToday ? 'today' : '' }}">
                    <div class="calendar-date">{{ $day }}</div>
                    @if($hol)
                        <div class="calendar-status bg-{{ $hol->type == 'company' ? 'blue-500' : 'orange-100' }}" style="font-size:0.7rem; white-space:normal; line-height:1.2; text-align:center; padding:2px; border-radius:4px; margin-top:2px; {{ $hol->type == 'company' ? 'background:#3b82f6; color:white;' : 'background:#fff7ed; color:#c2410c;' }}">
                            {{ $hol->name }}
                        </div>
                    @endif
                </div>
            @endfor
        </div>
    </div>

    <!-- Right Side Panel -->
    <div class="card" style="margin-bottom:0">
        <div class="card-header" style="border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1rem;">
            <h3 class="card-title" style="margin: 0;">This Month's Events</h3>
        </div>
        
        <div class="card-body" style="display:flex; flex-direction:column; gap:1.0rem; padding-top:0;">
            @php
                $thisMonthHols = $holidays->filter(function($hol) use ($year, $month) {
                    $pd = \Carbon\Carbon::parse($hol->date);
                    return $pd->year == $year && $pd->month == $month;
                });
            @endphp
            
            @if($thisMonthHols->count() > 0)
                @foreach($thisMonthHols as $hol)
                    @php 
                        $pd = \Carbon\Carbon::parse($hol->date); 
                    @endphp
                    <div style="display:flex; justify-content:space-between; align-items:center; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border);">
                        <div>
                            <div style="font-weight:600; color:var(--text); margin-bottom:0.25rem;">{{ $hol->name }}</div>
                            <div style="font-size:0.75rem; color:var(--text-muted)">
                                <span style="display:inline-block; width:8px; height:8px; border-radius:50%; margin-right:4px; {{ $hol->type == 'company' ? 'background:#3b82f6;' : 'background:#f59e0b;' }}"></span>
                                {{ ucfirst($hol->type) }}
                            </div>
                        </div>
                        <div style="display:flex; gap:1rem; align-items:center;">
                            <div style="text-align:center; background: var(--bg); padding: 0.5rem; border-radius: var(--radius-sm); min-width: 60px; border: 1px solid var(--border);">
                                <div style="font-size:1.1rem; font-weight:700; color:var(--text); line-height:1;">{{ $pd->format('d') }}</div>
                                <div style="font-size:0.7rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; margin-top:2px;">{{ $pd->format('D') }}</div>
                            </div>
                            @if(auth()->user()->hasAnyRole(['admin', 'super_admin', 'hr']))
                            <form action="{{ route('holidays.destroy', $hol->id) }}" method="POST" onsubmit="return confirm('Delete this holiday?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline text-danger" title="Delete" style="padding: 0.25rem 0.5rem;"><i class="fas fa-trash"></i></button>
                            </form>
                            @endif
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

@if(auth()->user()->hasAnyRole(['admin', 'super_admin', 'hr']))
<!-- Add Holiday Modal -->
<div id="add-holiday-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4 style="margin:0">Add Holiday</h4>
            <button class="btn-close" onclick="closeModal('add-holiday-modal')">&times;</button>
        </div>
        <form action="{{ route('holidays.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Type</label>
                <select name="type" class="form-select" required>
                    <option value="national">National</option>
                    <option value="regional">Regional</option>
                    <option value="optional">Optional</option>
                    <option value="company">Company Specific</option>
                </select>
            </div>
            <div class="text-right mt-4">
                <button type="submit" class="btn btn-primary">Save Holiday</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection
