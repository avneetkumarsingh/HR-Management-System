@extends('layouts.app')
@section('title', 'Org Dashboard')

@push('scripts')

<script>
    function openOrgTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("org-tab-pane");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
            tabcontent[i].classList.remove("active");
        }
        tablinks = document.getElementsByClassName("org-tab");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "block";
        document.getElementById(tabName).classList.add("active");
        evt.currentTarget.className += " active";
        
        // Save active tab
        sessionStorage.setItem('activeOrgTab', tabName);
    }

    (function() {
        // Restore active tab
        setTimeout(() => {
            const savedTab = sessionStorage.getItem('activeOrgTab');
            if (savedTab) {
                const tabs = document.querySelectorAll('.org-tab');
                tabs.forEach(tab => {
                    // Match element onclick to savedTab since text might be slightly different
                    if (tab.getAttribute('onclick') && tab.getAttribute('onclick').includes(savedTab)) {
                        tab.click();
                    }
                });
            }
        }, 10);

        // Safe execution context for Charts
        setTimeout(() => {
            const chartCanvas1 = document.getElementById('demographicsChart');
            if (chartCanvas1) {
                const ctx = chartCanvas1.getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($demographics['labels']) !!},
                        datasets: [{
                            data: {!! json_encode($demographics['data']) !!},
                            backgroundColor: ['#007bff', '#28a745', '#ffc107', '#17a2b8'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: { position: 'right' }
                        }
                    }
                });
            }
            
            const chartCanvas2 = document.getElementById('growthChart');
            if (chartCanvas2) {
                const lineCtx = chartCanvas2.getContext('2d');
                new Chart(lineCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Headcount Growth',
                            data: [2, 3, 3, 4, 4, {{ $headcount }}],
                            borderColor: '#007bff',
                            tension: 0.3,
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                    }
                });
            }
        }, 100); // Tiny delay to ensure DOM is fully swapped by SPA engine
    })();
</script>
@endpush

@section('content')
<style>
/* Keka Org Dashboard Design Tokens & Layouts */
:root {
    --primary-blue: #007bff;
    --success-green: #28a745;
    --warning-amber: #ffc107;
    --danger-red: #dc3545;
    --neutral-gray: #6c757d;
}

.org-tabs {
    display: flex;
    gap: 1.5rem;
    border-bottom: 1px solid var(--border);
    margin-bottom: 2rem;
}
.org-tab {
    padding: 0.75rem 0;
    cursor: pointer;
    font-size: 0.95rem;
    font-weight: 500;
    color: var(--text-muted);
    border-bottom: 2px solid transparent;
}
.org-tab.active {
    color: var(--primary);
    border-bottom: 2px solid var(--primary);
    background: transparent;
}
.org-tab-pane {
    display: none;
}
.org-tab-pane.active {
    display: block;
}

/* Metric Cards */
.metrics-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 2rem;
}
.metric-card {
    background: var(--bg);
    border: 0.5px solid var(--border);
    border-radius: 10px;
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.metric-card-title {
    font-size: 14px;
    font-weight: 500;
    color: var(--text-muted);
}
.metric-card-value {
    font-size: 24px;
    font-weight: 600;
    color: var(--text);
}

/* Pending Actions */
.panel-section {
    background: var(--bg);
    border: 0.5px solid var(--border);
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 20px;
}
.panel-title {
    font-size: 18px;
    font-weight: 500;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.pending-actions-list {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
}
.pending-action-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    border: 1px solid var(--border);
    border-radius: 8px;
    min-width: 120px;
    text-decoration: none;
    color: var(--text);
    transition: all 0.2s;
}
.pending-action-item:hover {
    border-color: var(--primary);
}
.pending-action-icon {
    font-size: 1.5rem;
    color: var(--neutral-gray);
}
.pending-action-title {
    font-size: 13px;
    font-weight: 500;
    text-align: center;
}
.pending-action-badge {
    background: var(--danger-red);
    color: white;
    font-size: 12px;
    padding: 2px 8px;
    border-radius: 12px;
    font-weight: bold;
}
.pending-action-badge.zero {
    background: var(--neutral-gray);
    opacity: 0.5;
}

/* Quicklinks */
.quicklinks-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}
.quicklink-card {
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    text-decoration: none;
    color: var(--text);
    transition: 0.2s;
}
.quicklink-card:hover {
    border-color: var(--primary);
    background: #f8fbff;
}
.quicklink-icon {
    background: #eef5ff;
    color: var(--primary);
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}
.quicklink-text {
    font-weight: 500;
    font-size: 14px;
}

/* Analytics */
.analytics-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
.chart-container {
    height: 300px;
    position: relative;
    width: 100%;
}
</style>
<div class="org-tabs">
    <div class="org-tab active" onclick="openOrgTab(event, 'Summary')">Summary</div>
    <div class="org-tab" onclick="openOrgTab(event, 'Analytics')">Analytics</div>
    <div class="org-tab" onclick="openOrgTab(event, 'Reports')">Reports</div>
    <div class="org-tab" onclick="openOrgTab(event, 'Performance')">Performance</div>
</div>

<div id="Summary" class="org-tab-pane active">
    <!-- Employees Overview Metric Cards -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-card-title">Total headcount</div>
            <div class="metric-card-value">{{ $headcount }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-card-title">Registered</div>
            <div class="metric-card-value">{{ $registered }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-card-title">Invited</div>
            <div class="metric-card-value">{{ $invited }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-card-title">Yet to register</div>
            <div class="metric-card-value">{{ $yetToRegister }}</div>
        </div>
    </div>

    <!-- Pending Actions Panel -->
    <div class="panel-section" style="border-top: 4px solid var(--danger-red);">
        <h3 class="panel-title">Pending actions</h3>
        <div class="pending-actions-list">
            @foreach($pendingActions as $action)
            <a href="{{ $action['link'] ?? '#' }}" class="pending-action-item">
                <div class="pending-action-icon"><i class="fas {{ $action['icon'] }}"></i></div>
                <div class="pending-action-title">{{ $action['name'] }}</div>
                <div class="pending-action-badge {{ $action['count'] == 0 ? 'zero' : '' }}">{{ $action['count'] }}</div>
            </a>
            @endforeach
        </div>
    </div>

    <!-- Pending Leave Requests Panel -->
    <div class="panel-section" style="border-top: 4px solid var(--warning-amber);">
        <h3 class="panel-title">Pending Leave Requests</h3>
        @if(count($pendingLeaveRequests) > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; margin-top: 1rem; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border); color: var(--neutral-gray); font-size: 0.9rem;">
                            <th style="padding: 1rem 0.5rem;">Employee</th>
                            <th style="padding: 1rem 0.5rem;">Leave Type</th>
                            <th style="padding: 1rem 0.5rem;">Duration</th>
                            <th style="padding: 1rem 0.5rem;">Reason</th>
                            <th style="padding: 1rem 0.5rem; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingLeaveRequests as $req)
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 1rem 0.5rem; font-weight: 500;">
                                <div style="display:flex; align-items:center; gap:0.5rem;">
                                    <img src="{{ $req->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($req->user->name).'&background=random' }}" style="width:32px; height:32px; border-radius:50%">
                                    {{ $req->user->name }}
                                </div>
                            </td>
                            <td style="padding: 1rem 0.5rem;">
                                <span style="background: rgba(100, 116, 139, 0.1); color: #475569; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">{{ $req->type->name }}</span>
                            </td>
                            <td style="padding: 1rem 0.5rem;">{{ \Carbon\Carbon::parse($req->from_date)->format('M d') }} - {{ \Carbon\Carbon::parse($req->to_date)->format('M d') }} 
                                <br><small style="color:var(--neutral-gray)">({{ $req->days }} days)</small>
                            </td>
                            <td style="padding: 1rem 0.5rem; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $req->reason }}">
                                {{ $req->reason }}
                            </td>
                            <td style="padding: 1rem 0.5rem; text-align: right;">
                                <div style="display:flex; gap:0.5rem; justify-content:flex-end">
                                    <form action="{{ route('leaves.approve', $req->id) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.85rem; background: var(--success-green); border-color: var(--success-green);">Approve</button>
                                    </form>
                                    <form action="{{ route('leaves.reject', $req->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="rejection_reason" value="Rejected internally from HR dashboard">
                                        <button class="btn btn-outline" style="padding: 0.35rem 0.75rem; font-size: 0.85rem; color: var(--danger-red); border-color: var(--danger-red);">Reject</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="background:#f8f9fa; padding:1.5rem; text-align:center; border-radius:8px; border:1px dashed #ced4da; margin-top: 1rem;">
                <p style="color:var(--neutral-gray);">Whoo-hoo! No pending leave requests to approve right now.</p>
            </div>
        @endif
    </div>

    <!-- Quicklinks -->
    <div class="panel-section" style="border-top: 4px solid var(--success-green);">
        <h3 class="panel-title">Quicklinks</h3>
        <div class="quicklinks-grid">
            <a href="{{ route('employees.create') }}" class="quicklink-card">
                <div class="quicklink-icon"><i class="fas fa-user-plus"></i></div>
                <div class="quicklink-text">Add employee</div>
            </a>
            <a href="{{ route('hr.bulk_import') }}" class="quicklink-card">
                <div class="quicklink-icon"><i class="fas fa-file-import"></i></div>
                <div class="quicklink-text">Bulk import</div>
            </a>
            <a href="{{ route('hr.announcements') }}" class="quicklink-card">
                <div class="quicklink-icon"><i class="fas fa-bullhorn"></i></div>
                <div class="quicklink-text">New announcement</div>
            </a>
            <a href="{{ route('hr.hire') }}" class="quicklink-card">
                <div class="quicklink-icon"><i class="fas fa-briefcase"></i></div>
                <div class="quicklink-text">Keka Hire</div>
            </a>
        </div>
    </div>
</div>

<div id="Analytics" class="org-tab-pane">
    <div class="analytics-grid">
        <div class="panel-section" style="border-top: 4px solid var(--primary-blue);">
            <h3 class="panel-title">Workforce demographics</h3>
            <div class="chart-container">
                <canvas id="demographicsChart"></canvas>
            </div>
        </div>
        <div class="panel-section" style="border-top: 4px solid var(--success-green);">
            <h3 class="panel-title">Headcount growth</h3>
            <div class="chart-container">
                <canvas id="growthChart"></canvas>
            </div>
        </div>
        <div class="panel-section" style="border-top: 4px solid var(--danger-red);">
            <h3 class="panel-title">Attrition rate</h3>
            <div class="chart-container" style="display:flex; justify-content:center; align-items:center; text-align:center; color:var(--neutral-gray)">
                <p>No attrition data available yet.</p>
            </div>
        </div>
        <div class="panel-section" style="border-top: 4px solid var(--warning-amber);">
            <h3 class="panel-title">Dept distribution</h3>
            <div class="chart-container" style="display:flex; justify-content:center; align-items:center; text-align:center; color:var(--neutral-gray)">
                <p>Data matches workforce demographics.</p>
            </div>
        </div>
    </div>
</div>

<div id="Reports" class="org-tab-pane">
    <div class="panel-section" style="border-top: 4px solid var(--primary-blue);">
        <div class="quicklinks-grid">
            <a href="{{ route('employees.index') }}" class="quicklink-card">
                <div class="quicklink-icon"><i class="fas fa-users"></i></div>
                <div class="quicklink-text">Employee info<br><small style="color:var(--text-muted); font-weight:normal">Active</small></div>
            </a>
            <a href="{{ route('hr.joins') }}" class="quicklink-card">
                <div class="quicklink-icon"><i class="fas fa-door-open"></i></div>
                <div class="quicklink-text">New joins & exits<br><small style="color:var(--text-muted); font-weight:normal">Active</small></div>
            </a>
            <a href="{{ route('hr.policies') }}" class="quicklink-card">
                <div class="quicklink-icon"><i class="fas fa-gavel"></i></div>
                <div class="quicklink-text">Employee policies<br><small style="color:var(--text-muted); font-weight:normal">Config</small></div>
            </a>
            <a href="{{ route('hr.logins') }}" class="quicklink-card">
                <div class="quicklink-icon"><i class="fas fa-sign-in-alt"></i></div>
                <div class="quicklink-text">Logins<br><small style="color:var(--text-muted); font-weight:normal">Audit</small></div>
            </a>
        </div>
    </div>
</div>

<div id="Performance" class="org-tab-pane">
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
        
        <!-- Submit Performance Review Form -->
        <div class="panel-section" style="border-top: 4px solid var(--primary-blue); height: fit-content;">
            <h3 class="panel-title"><i class="fas fa-star text-primary mr-2"></i> Submit Employee Review</h3>
            <p style="color:var(--text-muted); font-size:0.9rem; margin-bottom:1.5rem">Evaluate and submit performance details for an employee.</p>
            
            <form action="{{ route('hr.probations.store') }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500; font-size: 0.9rem; margin-bottom: 0.5rem; display: block;">Select Employee</label>
                    <select name="employee_id" class="form-input" style="width: 100%; border-radius: 6px; border: 1px solid var(--border); padding: 0.5rem;" required>
                        <option value="">-- Choose Employee --</option>
                        @foreach($employees ?? [] as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->role }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500; font-size: 0.9rem; margin-bottom: 0.5rem; display: block;">Performance Rating (1-10)</label>
                    <input type="number" name="rating" min="1" max="10" class="form-input" style="width: 100%; border-radius: 6px; border: 1px solid var(--border); padding: 0.5rem;" required>
                </div>
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label" style="font-weight: 500; font-size: 0.9rem; margin-bottom: 0.5rem; display: block;">Comments</label>
                    <textarea name="comments" rows="4" class="form-input" style="width: 100%; border-radius: 6px; border: 1px solid var(--border); padding: 0.5rem;" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Submit Review</button>
            </form>
        </div>

        <!-- Recent Reviews List -->
        <div class="panel-section" style="border-top: 4px solid var(--success-green);">
            <h3 class="panel-title"><i class="fas fa-clipboard-list text-success mr-2"></i> Recent Performance Details</h3>
            
            @if(isset($performanceReviews) && $performanceReviews->count() > 0)
                @php
                    $groupedReviews = $performanceReviews->groupBy(function($review) {
                        return $review->user->department->name ?? 'General / Unassigned';
                    });
                @endphp
                
                @foreach($groupedReviews as $deptName => $reviews)
                    <div style="margin-top: 1.5rem; background: var(--bg); border: 1px solid var(--border); border-radius: 8px; overflow: hidden;">
                        <div style="background: rgba(13, 148, 136, 0.05); padding: 0.75rem 1rem; border-bottom: 2px solid var(--primary); font-weight: 600; font-size: 1.05rem; display: flex; align-items: center; justify-content: space-between;">
                            <span>{{ $deptName }} Team</span>
                            <span style="font-size: 0.8rem; background: var(--primary); color: white; padding: 2px 8px; border-radius: 12px; font-weight: normal;">{{ $reviews->count() }} Reviews</span>
                        </div>
                        <div style="overflow-x: auto; padding: 0 0.5rem 0.5rem;">
                            <table style="width: 100%; border-collapse: collapse; text-align: left; margin-top: 0.5rem;">
                                <thead>
                                    <tr style="border-bottom: 1px dashed var(--border); color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">
                                        <th style="padding: 0.5rem;">Employee</th>
                                        <th style="padding: 0.5rem; text-align: center;">Rating</th>
                                        <th style="padding: 0.5rem;">Feedback & Reviewer</th>
                                        <th style="padding: 0.5rem; text-align: right;">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reviews as $review)
                                    <tr style="border-bottom: 1px solid rgba(226, 232, 240, 0.5);">
                                        <td style="padding: 1rem 0.5rem; font-weight: 500;">
                                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                                <img src="{{ $review->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($review->user->name ?? 'User') }}" style="width:28px; height:28px; border-radius:50%">
                                                {{ $review->user->name ?? 'Unknown' }}
                                            </div>
                                        </td>
                                        <td style="padding: 1rem 0.5rem; text-align: center;">
                                            <span style="font-size: 1rem; font-weight: 700; color: {{ $review->rating >= 8 ? 'var(--success-green)' : ($review->rating >= 5 ? 'var(--warning-amber)' : 'var(--danger-red)') }};">
                                                {{ $review->rating }}/10
                                            </span>
                                        </td>
                                        <td style="padding: 1rem 0.5rem; max-width: 250px;">
                                            <div style="font-size: 0.75rem; font-weight: 600; color: var(--primary); text-transform: uppercase; margin-bottom: 0.25rem;">
                                                Review by {{ $review->manager ? ucfirst($review->manager->role) : 'HR/Admin' }}
                                            </div>
                                            <div style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.3;">
                                                "{{ $review->comments }}"
                                            </div>
                                        </td>
                                        <td style="padding: 1rem 0.5rem; text-align: right; font-size: 0.8rem; color: var(--text-muted);">
                                            {{ $review->created_at->format('M d') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            @else
                <div style="background:#f8f9fa; padding:2rem; text-align:center; border-radius:8px; border:1px dashed #ced4da; margin-top: 1rem;">
                    <i class="fas fa-chart-line text-muted" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                    <p style="color:var(--text-muted); font-weight: 500;">No performance details available yet.</p>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
