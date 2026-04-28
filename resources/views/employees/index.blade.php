@extends('layouts.app')
@section('title', 'Employees Directory')

@section('content')
<div class="filter-bar">
    <div style="display: flex; align-items: center; margin-bottom: 1rem;">
        <button onclick="history.back()" class="btn btn-outline btn-sm" style="border:none; border-radius:50%; width:35px; height:35px; padding:0; display:flex; align-items:center; justify-content:center; margin-right: 1rem; background: var(--bg);" title="Go Back">
            <i class="fas fa-arrow-left"></i>
        </button>
        <h3 style="margin:0; font-size: 1.25rem;">Employee Directory</h3>
    </div>
    <form action="{{ route('employees.index') }}" method="GET" style="display:flex; gap:1rem; align-items:center; width:100%">
        <div class="input-wrapper" style="flex:1">
            <input type="text" name="search" class="form-input" placeholder="Search by name or ID..." value="{{ request('search') }}">
        </div>
        <select name="department_id" class="form-select" style="max-width:200px">
            <option value="">All Departments</option>
            @foreach($departments as $d)
                <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
            @endforeach
        </select>
        <select name="status" class="form-select" style="max-width:150px">
            <option value="">All Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('employees.create') }}" class="btn btn-success">Add</a>
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

        <div class="team-section" style="margin-bottom: 2.5rem;">
            <h3 style="margin: 0 0 1.5rem 0; font-size: 1.5rem; color: var(--text); border-bottom: 2px solid var(--primary); padding-bottom: 0.5rem; display: inline-block;">
                {{ $dept->name }} Team
            </h3>
            
            @if($deptEmployees->isEmpty())
                <div class="card" style="background:var(--bg); border: 1px dashed var(--border); box-shadow: none; margin-bottom: 0;">
                    <div class="card-body text-center text-muted p-4" style="padding: 2rem;">
                        No employees currently assigned to the {{ $dept->name }} team.
                    </div>
                </div>
            @else
                <div class="grid grid-cols-3 gap-6">
                    @foreach($deptEmployees as $emp)
                    <div class="card" style="margin-bottom:0">
                        <div class="card-body" style="text-align:center; position:relative">
                            <div style="position:absolute; top:1rem; right:1rem">
                                <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:{{ $emp->is_active ? 'var(--success)' : 'var(--danger)' }}" title="{{ $emp->is_active ? 'Active' : 'Inactive' }}"></span>
                            </div>
                            
                            <img src="{{ $emp->avatar_url }}" style="width:80px; height:80px; border-radius:50%; margin:0 auto 1rem; border:2px solid var(--primary-light)">
                            
                            <h4 style="margin:0"><a href="{{ route('employees.show', $emp->id) }}" style="color:var(--text)">{{ $emp->name }}</a></h4>
                            <div class="text-sm text-muted mb-4">{{ $emp->designation->name ?? 'N/A' }}</div>
                            
                            <div class="grid grid-cols-2 gap-2 text-sm text-left mb-4" style="background:var(--bg); padding:0.75rem; border-radius:var(--radius-sm)">
                                <div>
                                    <div class="text-muted" style="font-size:0.75rem">Employee ID</div>
                                    <div class="font-bold">{{ $emp->employee_id }}</div>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size:0.75rem">Department</div>
                                    <div class="font-bold">{{ $emp->department->code ?? 'N/A' }}</div>
                                </div>
                            </div>

                            <a href="{{ route('employees.show', $emp->id) }}" class="btn btn-outline btn-block btn-sm">View Profile</a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
    
    @if(request()->filled('search') && $employees->flatten()->isEmpty())
        <div class="card"><div class="card-body text-center text-muted p-4">No employees match your search query.</div></div>
    @endif
@endif
@endsection
