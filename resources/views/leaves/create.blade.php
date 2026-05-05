@extends('layouts.app')
@section('title', 'Apply Leave')

@section('content')
<div class="grid grid-cols-2 gap-6">
    <div class="card">
        <div class="card-header"><h3 class="card-title">Leave Application</h3></div>
        <div class="card-body">
            <form action="{{ route('leaves.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label">Leave Type</label>
                    <input type="text" name="leave_type_name" class="form-input" list="leave-types" required placeholder="Select or type a new leave type...">
                    <datalist id="leave-types">
                        @foreach($types as $type)
                            @if($type->code === 'ML' && strtolower(auth()->user()->gender ?? '') !== 'female')
                                @continue
                            @endif
                            <option value="{{ $type->name }}"></option>
                        @endforeach
                    </datalist>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">From Date</label>
                        <input type="date" name="from_date" id="from_date" class="form-input" required onchange="calcDays()">
                    </div>
                    <div class="form-group">
                        <label class="form-label">To Date</label>
                        <input type="date" name="to_date" id="to_date" class="form-input" required onchange="calcDays()">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Half Day Options</label>
                    <select name="half_day" id="half_day" class="form-select" onchange="calcDays()">
                        <option value="none">None (Full Day)</option>
                        <option value="first_half">First Half</option>
                        <option value="second_half">Second Half</option>
                    </select>
                </div>

                <div style="background:var(--bg); padding:1rem; border-radius:var(--radius-sm); margin-bottom:1.25rem; font-weight:600">
                    Applying for: <span id="days_count" class="text-primary">0</span> Days
                </div>

                <div class="form-group">
                    <label class="form-label">Reason</label>
                    <textarea name="reason" class="form-textarea" rows="4" required placeholder="Reason for leave..."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem; display: block;">Notify Colleagues (CC)</label>
                    <div style="max-height: 200px; overflow-y: auto; border: 1px solid var(--border); padding: 0.5rem 1rem; border-radius: var(--radius-sm); background: var(--bg); transition: var(--transition);">
                        @if(isset($users))
                            @foreach($users as $ccu)
                                <label style="display: flex; align-items: center; gap: 1rem; padding: 0.75rem 0; cursor: pointer; border-bottom: 1px solid rgba(0,0,0,0.05); transition: background-color 0.2s;">
                                    <input type="checkbox" name="cc_users[]" value="{{ $ccu->id }}" style="width: 18px; height: 18px; margin: 0; cursor: pointer; accent-color: var(--primary);">
                                    <div style="display: flex; align-items: center; gap: 0.75rem; flex: 1;">
                                        <img src="{{ $ccu->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($ccu->name).'&background=random' }}" alt="avatar" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                                        <div>
                                            <div style="font-weight: 500; font-size: 0.95rem; color: var(--text);">{{ $ccu->name }}</div>
                                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $ccu->department ? $ccu->department->name : 'No Dept' }}</div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        @endif
                    </div>
                    <div class="text-sm text-muted mt-2" style="font-size: 0.8rem;">Select one or multiple colleagues to notify.</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Attachment (Optional)</label>
                    <input type="file" name="document" class="form-input">
                    <div class="text-sm text-muted mt-2">Max 2MB. PDF, JPG, PNG.</div>
                </div>

                <div class="text-sm text-center mb-4" style="color: var(--text-muted);">
                    Your leave needs to be approved by your HR or Manager before it becomes active.
                </div>
                <button type="submit" class="btn btn-primary btn-block">Apply Leave</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function calcDays() {
        const from = document.getElementById('from_date').value;
        const to = document.getElementById('to_date').value;
        const hd = document.getElementById('half_day').value;
        
        if (from && to) {
            const d1 = new Date(from);
            const d2 = new Date(to);
            if (d2 >= d1) {
                // simple diff
                let days = Math.round((d2 - d1) / (1000 * 60 * 60 * 24)) + 1;
                
                // Exclude weekends logic could go here via an ajax call or JS logic...
                // Keeping it simple as per implementation plan bounds
                
                if(hd !== 'none') {
                    days -= 0.5;
                }
                
                if (days < 0) days = 0;
                document.getElementById('days_count').textContent = days;
            } else {
                document.getElementById('days_count').textContent = 0;
            }
        }
    }
</script>
@endpush
@endsection
