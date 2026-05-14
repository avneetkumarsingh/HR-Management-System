@extends('layouts.app')
@section('title', 'Employees Directory')

@section('content')
<div class="card mb-6" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
    <div style="display: flex; align-items: center; width: 100%;">
        <button onclick="history.back()" class="btn btn-outline btn-sm" style="border:none; border-radius:50%; width:35px; height:35px; padding:0; display:flex; align-items:center; justify-content:center; margin-right: 0.75rem; background: var(--bg); flex-shrink: 0;" title="Go Back">
            <i class="fas fa-arrow-left"></i>
        </button>
        <h3 style="margin:0; font-size: 1.3rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Employee Directory</h3>
    </div>
    
    <form action="{{ route('employees.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.75rem; width: 100%;">
        <div class="input-wrapper" style="grid-column: 1 / -1;">
            <input type="text" name="search" class="form-input" placeholder="Search by name or ID..." value="{{ request('search') }}" style="width: 100%;">
        </div>
        <select name="department_id" class="form-select" style="width: 100%;">
            <option value="">All Departments</option>
            @foreach($departments as $d)
                <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
            @endforeach
        </select>
        <select name="status" class="form-select" style="width: 100%;">
            <option value="">All Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <div style="display: flex; gap: 0.5rem; width: 100%; grid-column: 1 / -1;">
            <button type="submit" class="btn btn-primary" style="flex: 2; padding: 0.6rem;">Filter</button>
            <a href="{{ route('employees.create') }}" class="btn btn-success" style="flex: 1; text-align: center; padding: 0.6rem;">Add</a>
        </div>
    </form>
</div>

@php
    $displayDepartments = request()->filled('department_id') 
        ? $departments->where('id', request('department_id')) 
        : $departments;
@endphp

@if($displayDepartments->isEmpty())
    <div class="card"><div class="card-body text-center text-muted p-4">No specific departments to display.</div></div>
@else
    @foreach($displayDepartments as $dept)
        @php
            $deptEmployees = $employees->get($dept->id, collect());
        @endphp
        
        {{-- Hide empty departments if searching by name --}}
        @if(request()->filled('search') && $deptEmployees->isEmpty())
            @continue
        @endif

        <div class="card mb-6">
            <div class="card-header" style="cursor: pointer;">
                <h3 class="card-title" style="margin: 0; width:100%;">
                    <div style="display:flex; align-items:center;">
                        {{ $dept->name }} Team
                        <span class="badge" style="background:var(--border); margin-left:10px">{{ $deptEmployees->count() }} Members</span>
                    </div>
                </h3>
            </div>
            
            <div class="card-body" style="background: var(--bg);">
                @if($deptEmployees->isEmpty())
                    <div class="text-center text-muted">
                        No employees currently assigned to the {{ $dept->name }} team.
                    </div>
                @else
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; align-items: start;">
                    @foreach($deptEmployees as $emp)
                    <div class="card hover-card" style="margin-bottom:0; cursor:pointer;" onclick="window.location='{{ route('employees.show', $emp->id) }}'">
                        <div class="card-body" style="padding: 1rem; display: flex; align-items: center; gap: 1rem; position: relative;">
                            <img src="{{ $emp->avatar_url }}" style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid var(--border); flex-shrink: 0;">
                            
                            <div style="flex: 1; text-align: left; overflow: hidden;">
                                <h4 style="margin: 0; font-size: 0.95rem; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: flex; align-items: center; gap: 0.5rem;">
                                    {{ $emp->name }}
                                    <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: {{ $emp->is_active ? 'var(--success)' : 'var(--danger)' }}; flex-shrink: 0;" title="{{ $emp->is_active ? 'Active' : 'Inactive' }}"></span>
                                </h4>
                                <div style="font-size: 0.8rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $emp->designation->name ?? 'Employee' }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem; font-weight: 500;">{{ $emp->employee_id }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    @endforeach
    
    @if(request()->filled('search') && $employees->flatten()->isEmpty())
        <div class="card"><div class="card-body text-center text-muted p-4">No employees match your search query.</div></div>
    @endif
@endif
@endsection
