@extends('layouts.app')
@section('title', 'Leave Approvals')

@section('content')
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Leave Type</th>
                    <th>Duration</th>
                    <th>Days</th>
                    <th>Reason</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:0.5rem">
                            <img src="{{ $req->user->avatar_url }}" style="width:30px; height:30px; border-radius:50%">
                            <div>
                                <div class="font-bold text-sm">{{ $req->user->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $req->type->name }}</td>
                    <td class="text-sm">{{ $req->from_date->format('d M') }} - {{ $req->to_date->format('d M Y') }}</td>
                    <td>{{ number_format($req->days, 1) }}</td>
                    <td class="text-sm" style="max-width:200px">{{ \Illuminate\Support\Str::limit($req->reason, 30) }}</td>
                    <td>
                        <div style="display:flex; gap:0.5rem">
                            <form action="{{ route('leaves.approve', $req->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-success"></button>
                            </form>
                            <button class="btn btn-sm btn-danger" onclick="openModal('reject-modal-{{$req->id}}')"></button>
                        </div>

                        <!-- Reject Modal -->
                        <div id="reject-modal-{{$req->id}}" class="modal">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 style="margin:0">Reject Leave</h4>
                                    <button class="btn-close" onclick="closeModal('reject-modal-{{$req->id}}')">&times;</button>
                                </div>
                                <form action="{{ route('leaves.reject', $req->id) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label class="form-label">Rejection Reason</label>
                                        <textarea name="rejection_reason" class="form-textarea" required></textarea>
                                    </div>
                                    <div class="text-right mt-4">
                                        <button type="submit" class="btn btn-danger">Confirm Reject</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center p-4 text-muted">No pending leave approvals.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
