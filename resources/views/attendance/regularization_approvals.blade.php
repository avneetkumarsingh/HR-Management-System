@extends('layouts.app')
@section('title', 'Regularization Approvals')

@section('content')
<div class="card">
    <div class="card-header mobile-toggle-header" onclick="if(true) { const b=document.getElementById('reg-apprs'); b.style.display=b.style.display==='block'?'none':'block'; this.querySelector('.mobile-chevron').style.transform=b.style.display==='block'?'rotate(0deg)':'rotate(-90deg)'; }">
        <h3 class="card-title" style="display:flex; justify-content:space-between; align-items:center; width:100%; margin:0;">
            <span>Pending Requests</span>
            <i class="fas fa-chevron-down mobile-chevron" style="transition:0.3s; transform:rotate(-90deg);"></i>
        </h3>
    </div>
    <div class="card-body mobile-collapsible-body" id="reg-apprs">
        @if(count($requests) > 0)
            <div style="overflow-x:auto;">
                <table style="width:100%; text-align:left; border-collapse:collapse;">
                    <thead>
                        <tr style="border-bottom:2px solid var(--border); color:var(--text-muted)">
                            <th style="padding:1rem;">Employee</th>
                            <th style="padding:1rem;">Date</th>
                            <th style="padding:1rem;">Hours Requested</th>
                            <th style="padding:1rem;">Reason</th>
                            <th style="padding:1rem;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $req)
                            <tr style="border-bottom:1px solid var(--border);">
                                <td style="padding:1rem;">
                                    <div style="display:flex; align-items:center; gap:0.75rem;">
                                        <img src="{{ $req->user->avatar_url }}" style="width:32px; height:32px; border-radius:50%;">
                                        <div>
                                            <div style="font-weight:500">{{ $req->user->name }}</div>
                                            <div style="font-size:0.75rem; color:var(--text-muted)">{{ $req->user->employee_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:1rem;">{{ \Carbon\Carbon::parse($req->date)->format('d M, Y') }}</td>
                                <td style="padding:1rem;">{{ $req->requested_hours }} hrs</td>
                                <td style="padding:1rem;">{{ \Illuminate\Support\Str::limit($req->reason, 40) }}</td>
                                <td style="padding:1rem;">
                                    <div style="display:flex; gap:0.5rem;">
                                        <form action="{{ route('regularization.approve', $req->id) }}" method="POST">
                                            @csrf
                                            <button class="btn" style="background:#eafaf1; color:var(--success); border:1px solid var(--success); padding:0.25rem 0.75rem; font-size:0.85rem">Approve</button>
                                        </form>
                                        <form action="{{ route('regularization.reject', $req->id) }}" method="POST">
                                            @csrf
                                            <button class="btn" style="background:#fbeaea; color:var(--danger); border:1px solid var(--danger); padding:0.25rem 0.75rem; font-size:0.85rem">Reject</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align:center; padding:3rem; color:var(--text-muted);">
                <p>All caught up! No pending regularization requests.</p>
            </div>
        @endif
    </div>
</div>
@endsection
