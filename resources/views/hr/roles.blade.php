@extends('layouts.app')
@section('title', 'Roles & Permissions')

@section('content')
<div class="grid grid-cols-2 gap-6 mb-6">
    <div class="card">
        <div class="card-header"><h3 class="card-title">Add New Role</h3></div>
        <div class="card-body">
            <form action="{{ route('roles.store_role') }}" method="POST" style="display: flex; gap: 1rem;">
                @csrf
                <input type="text" name="name" class="form-input" placeholder="e.g., intern, team_lead" required style="flex: 1">
                <button type="submit" class="btn btn-primary">Add Role</button>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3 class="card-title">Add New Permission</h3></div>
        <div class="card-body">
            <form action="{{ route('roles.store_permission') }}" method="POST" style="display: flex; gap: 1rem;">
                @csrf
                <input type="text" name="name" class="form-input" placeholder="e.g., manage_assets" required style="flex: 1">
                <button type="submit" class="btn btn-primary" style="white-space: nowrap">Add Permission</button>
            </form>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-6">
    <div class="card">
        <div class="card-header" style="justify-content: space-between">
            <h3 class="card-title">Role Permissions Management</h3>
            <span class="text-sm text-muted">Use this panel to define securely what actions Employee, Manager, and HR roles can perform.</span>
        </div>
        <div class="card-body">
            <form action="{{ route('roles.update') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table" style="table-layout: fixed;">
                        <thead>
                            <tr style="background: var(--bg);">
                                <th style="width: 30%;">Permission / Capability</th>
                                @foreach($roles as $role)
                                <th style="text-align: center; text-transform: capitalize; padding-bottom: 5px;">
                                    {{ $role->name }}
                                    <div style="font-size: 0.8rem; margin-top: 6px; display:flex; justify-content:center; gap:8px;">
                                        <button type="button" onclick="editRole({{ $role->id }}, '{{ $role->name }}')" title="Edit Role" style="color:var(--primary); background:none; border:none; cursor:pointer"><i class="fas fa-edit"></i></button>
                                        @if(!in_array($role->name, ['hr', 'manager', 'employee']))
                                        <button type="button" onclick="deleteRole({{ $role->id }})" title="Delete Role" style="color:var(--danger, #ef4444); background:none; border:none; cursor:pointer"><i class="fas fa-trash"></i></button>
                                        @endif
                                    </div>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissions as $permission)
                            <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <td style="font-weight: 500; font-family: monospace;">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <div>
                                            {{ str_replace('_', ' ', Str::title($permission->name)) }} 
                                            <div class="text-xs text-muted" style="font-family: sans-serif">{{ $permission->name }}</div>
                                        </div>
                                        <div style="display: flex; gap: 0.75rem;">
                                            <button type="button" onclick="editPermission({{ $permission->id }}, '{{ $permission->name }}')" title="Edit Permission" style="color:var(--primary); background:none; border:none; cursor:pointer"><i class="fas fa-edit"></i></button>
                                            <button type="button" onclick="deletePermission({{ $permission->id }})" title="Delete Permission" style="color:var(--danger, #ef4444); background:none; border:none; cursor:pointer"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </div>
                                </td>
                                @foreach($roles as $role)
                                    <td style="text-align: center;">
                                        <label style="cursor: pointer; display: inline-flex; align-items: center; justify-content: center; width: 100%; height: 100%;">
                                            <input type="checkbox" name="roles[{{ $role->id }}][]" value="{{ $permission->name }}" 
                                                {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                                style="width: 18px; height: 18px; cursor: pointer; accent-color: var(--primary);">
                                        </label>
                                    </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1rem;">Save Permissions Matrix</button>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="editForm" method="POST" style="display: none;">
    @csrf @method('PUT')
    <input type="hidden" name="name" id="editNameInput">
</form>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf @method('DELETE')
</form>

<script>
    function editRole(id, currentName) {
        let newName = prompt("Edit Role Name:", currentName);
        if (newName && newName.trim() !== "" && newName !== currentName) {
            let form = document.getElementById('editForm');
            form.action = '/hr/roles/update-role/' + id;
            document.getElementById('editNameInput').value = newName;
            form.submit();
        }
    }

    function editPermission(id, currentName) {
        let newName = prompt("Edit Permission Name:", currentName);
        if (newName && newName.trim() !== "" && newName !== currentName) {
            let form = document.getElementById('editForm');
            form.action = '/hr/roles/update-permission/' + id;
            document.getElementById('editNameInput').value = newName;
            form.submit();
        }
    }

    function deleteRole(id) {
        if(confirm('WARNING: Are you sure you want to permanently delete this Custom Role?')) {
            let form = document.getElementById('deleteForm');
            form.action = '/hr/roles/destroy-role/' + id;
            form.submit();
        }
    }

    function deletePermission(id) {
        if(confirm('WARNING: Are you sure you want to permanently delete this Permission?')) {
            let form = document.getElementById('deleteForm');
            form.action = '/hr/roles/destroy-permission/' + id;
            form.submit();
        }
    }
</script>
@endsection
