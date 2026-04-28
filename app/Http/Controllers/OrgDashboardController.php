<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\RegularizationRequest;
use Illuminate\Support\Facades\Auth;

class OrgDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Overview Stats
        $headcount = User::where('is_active', true)->count();
        $registered = $headcount; // In this system, all active are registered
        $invited = 0; // Mocked
        $yetToRegister = 0; // Mocked
        
        // Pending Actions
        $pendingLeaves = LeaveRequest::where('status', 'pending')->count();
        $pendingRegs = RegularizationRequest::where('status', 'pending')->count();
        
        $pendingActions = [
            ['name' => 'Documents', 'count' => 0, 'icon' => 'fa-file-alt', 'link' => route('hr.documents')],
            ['name' => 'Expenses', 'count' => 0, 'icon' => 'fa-receipt', 'link' => route('hr.expenses')],
            ['name' => 'Tickets', 'count' => 0, 'icon' => 'fa-ticket-alt', 'link' => route('hr.tickets')],
            ['name' => 'Probations', 'count' => 0, 'icon' => 'fa-user-clock', 'link' => route('hr.probations')],
            ['name' => 'Profile changes', 'count' => 0, 'icon' => 'fa-id-badge', 'link' => route('hr.profile_changes')],
            ['name' => 'Leave Approvals', 'count' => $pendingLeaves, 'icon' => 'fa-calendar-check', 'link' => route('leaves.approvals')],
            ['name' => 'Reg. Approvals', 'count' => $pendingRegs, 'icon' => 'fa-clock', 'link' => route('regularization.approvals')],
        ];
        
        // Analytics Mock Data
        $demographics = [
            'labels' => ['Engineering', 'Sales', 'Design', 'HR'],
            'data' => [User::where('department_id', 1)->count() ?? 2, User::where('department_id', 2)->count() ?? 1, 1, 1]
        ];

        $pendingLeaveRequests = LeaveRequest::where('status', 'pending')->with(['user', 'type'])->get();
        
        $employees = User::where('is_active', true)->where('role', '!=', 'hr')->orderBy('name')->get();
        $performanceReviews = \App\Models\ProbationReview::with(['user.department', 'manager'])->latest()->get();

        return view('dashboard.org', compact(
            'headcount', 'registered', 'invited', 'yetToRegister', 'pendingActions', 'demographics', 'pendingLeaveRequests', 'employees', 'performanceReviews'
        ));
    }
}
