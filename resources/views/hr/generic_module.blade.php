@extends('layouts.app')
@section('title', $title)

@section('content')
<div class="card mb-4" style="border-top: 4px solid var(--primary);">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <button onclick="history.back()" class="btn btn-outline btn-sm" style="border:none; border-radius:50%; width:35px; height:35px; padding:0; display:flex; align-items:center; justify-content:center;" title="Go Back">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h3 class="card-title" style="margin: 0;"><i class="fas {{ $icon }} text-primary mr-2"></i> {{ $title }}</h3>
        </div>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('addForm').style.display = 'block'">
            <i class="fas fa-plus"></i> Add New
        </button>
    </div>
    <div class="card-body">
        
        <!-- Add Form (Hidden by default) -->
        <div id="addForm" style="display:none; padding:1.5rem; background:var(--bg); border:1px solid var(--border); border-radius:var(--radius-sm); margin-bottom:2rem;">
            <div style="display:flex; justify-content:space-between; margin-bottom:1rem">
                <h4 style="margin:0">Create New {{ rtrim($title, 's') }}</h4>
                <button onclick="document.getElementById('addForm').style.display = 'none'" style="background:none; border:none; cursor:pointer"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ $submitRoute }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    @foreach($submitFields as $field)
                        <div class="form-group" style="grid-column: span {{ $field['width'] ?? 2 }}">
                            <label class="form-label">{{ $field['label'] }}</label>
                            @if($field['type'] == 'textarea')
                                <textarea name="{{ $field['name'] }}" class="form-textarea" rows="3" required></textarea>
                            @elseif($field['type'] == 'select')
                                <select name="{{ $field['name'] }}" class="form-select" required>
                                    @foreach($field['options'] as $key => $val)
                                        <option value="{{ $key }}">{{ $val }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" class="form-input" required>
                            @endif
                        </div>
                    @endforeach
                </div>
                <button type="submit" class="btn btn-primary mt-4">Save / Submit</button>
            </form>
        </div>

        @if($items->isEmpty())
            <div style="text-align:center; padding: 4rem 2rem; color:var(--text-muted);">
                <i class="fas {{ $icon }}" style="font-size:3rem; margin-bottom:1rem; opacity:0.3"></i>
                <h4>No {{ strtolower($title) }} found!</h4>
                <p>Data will appear here once submitted.</p>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; text-align:left;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border); background:var(--bg);">
                            @foreach($headers as $header)
                                <th style="padding:1rem;">{{ $header }}</th>
                            @endforeach
                            <th style="padding:1rem; text-align:right">{{ isset($approveRoute) ? 'Status / Actions' : 'Status' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr style="border-bottom:1px solid var(--border);">
                            @foreach($columns as $col)
                                <td style="padding:1rem;">
                                    @if(str_contains($col, 'user'))
                                        {{ $item->user->name ?? 'Unknown' }}
                                    @elseif($col == 'amount')
                                        ${{ number_format($item->$col, 2) }}
                                    @else
                                        {{ $item->$col }}
                                    @endif
                                </td>
                            @endforeach
                            <td style="padding:1rem; text-align:right;">
                                @if(isset($item->status))
                                    <span class="badge badge-{{ $item->status == 'approved' ? 'present' : ($item->status=='rejected' ? 'absent' : 'pending') }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                @elseif(isset($item->is_active))
                                    <span class="badge badge-{{ $item->is_active ? 'present' : 'absent' }}">
                                        {{ $item->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                @endif
                                
                                @if(isset($approveRoute))
                                    <div style="display:flex; justify-content:flex-end; gap:0.5rem; margin-top:0.5rem">
                                        <form method="POST" action="{{ route($approveRoute, $item->id) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline text-success" title="Approve"><i class="fas fa-check"></i></button>
                                        </form>
                                        <form method="POST" action="{{ route($rejectRoute, $item->id) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline text-danger" title="Reject"><i class="fas fa-times"></i></button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
