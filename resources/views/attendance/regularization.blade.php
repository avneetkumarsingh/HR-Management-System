@extends('layouts.app')
@section('title', 'Attendance Regularization')

@section('content')
<div style="display:flex; flex-wrap:wrap; gap:1.5rem;">
    <div class="card" style="flex: 1; min-width: 300px; margin-bottom:0;">
        <div class="card-header mobile-toggle-header" onclick="if(true) { const b=document.getElementById('reg-form'); b.style.display=b.style.display==='block'?'none':'block'; this.querySelector('.mobile-chevron').style.transform=b.style.display==='block'?'rotate(0deg)':'rotate(-90deg)'; }">
            <h3 class="card-title" style="display:flex; justify-content:space-between; align-items:center; width:100%; margin:0;">
                <span>Apply Regularization</span>
                <i class="fas fa-chevron-down mobile-chevron" style="transition:0.3s; transform:rotate(-90deg);"></i>
            </h3>
        </div>
        <div class="card-body mobile-collapsible-body" id="reg-form">
            <form action="{{ route('regularization.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-input" value="{{ request('date', date('Y-m-d')) }}" required>
                </div>
                <div class="form-group flex gap-4" style="flex-direction: column;">
                    <div>
                        <label class="form-label">Check In Time</label>
                        <input type="datetime-local" name="check_in" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Check Out Time</label>
                        <input type="datetime-local" name="check_out" class="form-input">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Reason</label>
                    <textarea name="reason" class="form-textarea" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Submit Request</button>
            </form>
        </div>
    </div>

    <div class="card" style="flex: 2; min-width: 300px; margin-bottom:0;">
        <div class="card-header mobile-toggle-header" onclick="if(true) { const b=document.getElementById('reg-reqs'); b.style.display=b.style.display==='block'?'none':'block'; this.querySelector('.mobile-chevron').style.transform=b.style.display==='block'?'rotate(0deg)':'rotate(-90deg)'; }">
            <h3 class="card-title" style="display:flex; justify-content:space-between; align-items:center; width:100%; margin:0;">
                <span>My Requests</span>
                <i class="fas fa-chevron-down mobile-chevron" style="transition:0.3s; transform:rotate(-90deg);"></i>
            </h3>
        </div>
        <div class="table-responsive mobile-collapsible-body" id="reg-reqs">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Requested Times</th>
                        <th>Reason</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td>{{ $req->date->format('d M, Y') }}<br><span class="text-muted text-sm">Applied on {{ $req->created_at->format('d M') }}</span></td>
                        <td>
                            <div class="text-sm">IN: {{ $req->check_in ? $req->check_in->format('H:i') : '--:--' }}</div>
                            <div class="text-sm">OUT: {{ $req->check_out ? $req->check_out->format('H:i') : '--:--' }}</div>
                        </td>
                        <td style="max-width:200px">{{ $req->reason }}</td>
                        <td><span class="badge badge-{{ str_replace(' ', '_', $req->status) }}">{{ ucfirst($req->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted p-4">No regularization requests.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
