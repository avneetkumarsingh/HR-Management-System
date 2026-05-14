<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{

    public function index()
    {
        // Seed default foundational permissions if they do not exist
        $permissionsList = [
            // Employee Base
            'view_own_dashboard', 'view_own_attendance', 'mark_own_attendance', 
            'view_own_leaves', 'apply_for_leave', 'submit_own_expenses', 
            'submit_helpdesk_tickets', 'view_company_documents', 
            'view_company_policies', 'view_announcements',
            
            // Manager Capabilities
            'view_team_attendance', 'approve_team_leaves', 'approve_team_expenses', 
            'submit_probation_reviews',

            // HR / Admin Capabilities
            'view_org_dashboard', 'manage_all_employees', 'manage_roles_permissions', 
            'manage_departments', 'manage_designations', 'manage_shifts', 
            'manage_leave_types', 'view_all_reports', 'manage_company_documents', 
            'publish_announcements', 'manage_company_policies', 'manage_recruitment', 
            'view_audit_logs', 'perform_bulk_import'
        ];

        foreach($permissionsList as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return view('hr.roles', compact('roles', 'permissions'));
    }

    public function update(Request $request)
    {
        $rolePermissions = $request->input('roles', []);

        // Sync permissions specifically for each role provided
        foreach(Role::all() as $role) {
            $perms = isset($rolePermissions[$role->id]) ? $rolePermissions[$role->id] : [];
            $role->syncPermissions($perms);
        }

        return back()->with('success', 'Roles & Permissions updated successfully!');
    }

    public function storeRole(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:roles,name']);
        Role::create(['name' => strtolower(str_replace(' ', '_', $request->name))]);
        return back()->with('success', 'New Role created successfully!');
    }

    public function storePermission(Request $request) 
    {
        $request->validate(['name' => 'required|string|unique:permissions,name']);
        Permission::create(['name' => strtolower(str_replace(' ', '_', $request->name))]);
        return back()->with('success', 'New Permission added successfully!');
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|unique:roles,name,' . $id]);
        $role = Role::findOrFail($id);
        $role->update(['name' => strtolower(str_replace(' ', '_', $request->name))]);
        return back()->with('success', 'Role updated successfully!');
    }

    public function destroyRole($id)
    {
        if (!auth()->user() || !auth()->user()->hasAnyRole(['hr', 'admin', 'super_admin'])) {
            abort(403, 'UNAUTHORIZED: Only HR administrators can manage and delete roles.');
        }

        $role = Role::findOrFail($id);
        if(in_array($role->name, ['super_admin', 'admin', 'hr', 'manager', 'employee'])) {
            return back()->with('error', 'Cannot delete core system roles.');
        }
        $role->delete();
        return back()->with('success', 'Role deleted successfully!');
    }

    public function updatePermission(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|unique:permissions,name,' . $id]);
        $permission = Permission::findOrFail($id);
        $permission->update(['name' => strtolower(str_replace(' ', '_', $request->name))]);
        return back()->with('success', 'Permission updated successfully!');
    }

    public function destroyPermission($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();
        return back()->with('success', 'Permission deleted successfully!');
    }
}
