@extends('layouts.app')
@section('title', 'Manage Announcements')

@section('content')
<div style="display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); gap: 1.5rem;">
    <!-- Active Announcements List -->
    <div class="card" style="margin-bottom:0">
        <div class="card-header" style="border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1rem;">
            <h3 class="card-title" style="margin: 0;"><i class="fas fa-bullhorn text-primary mr-2"></i> Current Announcements</h3>
        </div>
        <div class="card-body" style="padding-top:0;">
            @if($announcements->isEmpty())
                <div style="text-align:center; padding: 3rem 0; color:var(--text-muted); border: 1px dashed var(--border); border-radius: var(--radius-sm);">
                    <i class="fas fa-comment-slash" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                    <p style="margin:0;">No announcements published yet. Create one to inform your employees.</p>
                </div>
            @else
                <div style="display:flex; flex-direction:column; gap:1.5rem;">
                    @foreach($announcements as $announcement)
                        <div style="padding-bottom: 1.5rem; border-bottom: 1px solid var(--border);">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.5rem;">
                                <div>
                                    <h4 style="margin:0 0 0.25rem 0; font-size:1.1rem;">{{ $announcement->title }}</h4>
                                    <div style="font-size:0.8rem; color:var(--text-muted); display:flex; gap:1rem;">
                                        <span><i class="fas fa-user mr-2"></i>{{ $announcement->author->name ?? 'System' }}</span>
                                        <span><i class="fas fa-clock mr-2"></i>{{ $announcement->created_at->diffForHumans() }}</span>
                                        <span class="badge {{ $announcement->type == 'critical' ? 'badge-absent' : ($announcement->type == 'event' ? 'badge-on_leave' : 'badge-holiday') }}" style="padding:0.1rem 0.5rem; font-size:0.7rem;">
                                            {{ ucfirst($announcement->type) }}
                                        </span>
                                    </div>
                                </div>
                                <form action="{{ route('hr.announcements.destroy', $announcement->id) }}" method="POST" onsubmit="return confirm('Remove this announcement?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline text-danger" title="Remove"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                            <div style="font-size:0.95rem; color:var(--text); white-space:pre-wrap;">{{ $announcement->content }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Create Announcement Form -->
    <div class="card" style="margin-bottom:0; align-self: start;">
        <div class="card-header" style="border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1rem; background: var(--bg);">
            <h3 class="card-title" style="margin: 0;"><i class="fas fa-plus-circle text-primary mr-2"></i> New Announcement</h3>
        </div>
        <div class="card-body" style="padding-top:0;">
            <form action="{{ route('hr.announcements.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Subject / Title</label>
                    <input type="text" name="title" class="form-input" required placeholder="e.g. Office Closed on Friday">
                </div>
                <div class="form-group">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select" required>
                        <option value="general">General Info</option>
                        <option value="event">Company Event</option>
                        <option value="critical">Critical / Urgent</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Message Content</label>
                    <textarea name="content" class="form-textarea" rows="5" required placeholder="Write your announcement here... It will immediately appear on all employee dashboards."></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-paper-plane mr-2"></i> Publish Announcement</button>
            </form>
        </div>
    </div>
</div>
@endsection
