<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RegularizationController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AnnouncementController;
use App\Models\Expense;
use App\Models\Ticket;
use App\Models\Document;
use App\Models\ProbationReview;
use App\Models\Policy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.show');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Attendance
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/calendar', [AttendanceController::class, 'calendar'])->name('calendar');
        Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('check_in');
        Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('check_out');
        Route::get('/team', [AttendanceController::class, 'teamAttendance'])->name('team');
        Route::get('/report', [AttendanceController::class, 'adminReport'])->name('report');
        Route::get('/{id}', [AttendanceController::class, 'show'])->name('show');
    });

    // Regularization
    Route::prefix('regularization')->name('regularization.')->group(function () {
        Route::get('/', [RegularizationController::class, 'index'])->name('index');
        Route::post('/store', [RegularizationController::class, 'store'])->name('store');
        Route::get('/approvals', [RegularizationController::class, 'approvals'])->name('approvals');
        Route::post('/{id}/approve', [RegularizationController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [RegularizationController::class, 'reject'])->name('reject');
    });

    // Leaves
    Route::prefix('leaves')->name('leaves.')->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->name('index');
        Route::get('/apply', [LeaveController::class, 'create'])->name('create');
        Route::post('/store', [LeaveController::class, 'store'])->name('store');
        Route::get('/approvals', [LeaveController::class, 'pendingApprovals'])->name('approvals');
        Route::get('/types', [LeaveController::class, 'types'])->name('types');
        Route::post('/types', [LeaveController::class, 'storeType'])->name('types.store');
        Route::put('/types/{id}', [LeaveController::class, 'updateType'])->name('types.update');
        Route::get('/{id}', [LeaveController::class, 'show'])->name('show');
        Route::post('/{id}/cancel', [LeaveController::class, 'cancel'])->name('cancel');
        Route::post('/{id}/approve', [LeaveController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [LeaveController::class, 'reject'])->name('reject');
    });

    // Admin Org Dashboard
    Route::get('/org-dashboard', [\App\Http\Controllers\OrgDashboardController::class, 'index'])->name('org.dashboard');

    Route::view('/coming-soon', 'coming_soon')->name('coming.soon');
   
    // HR Modules Empty States
    Route::prefix('hr')->name('hr.')->group(function () {
        // Expenses 
        Route::get('/expenses', function() {
            $isAdmin = Auth::user()->hasAnyRole(['hr', 'admin', 'super_admin']);
            $items = $isAdmin ? Expense::with('user')->latest()->get() : Expense::where('user_id', Auth::id())->latest()->get();
            
            return view('hr.generic_module', [
                'title' => 'Expenses & Claims', 'icon' => 'fa-receipt', 'items' => $items, 'submitRoute' => route('hr.expenses.store'),
                'headers' => ['Employee', 'Title', 'Amount', 'Submitted'], 'columns' => ['user_id', 'title', 'amount', 'created_at'],
                'submitFields' => [
                    ['name' => 'title', 'label' => 'Expense Name / Bill', 'type' => 'text', 'width' => 1],
                    ['name' => 'amount', 'label' => 'Total Amount ($)', 'type' => 'number', 'width' => 1]
                ],
                'approveRoute' => 'hr.expenses.approve', 'rejectRoute' => 'hr.expenses.reject'
            ]);
        })->name('expenses');
        Route::post('/expenses', function(Request $request) {
            Expense::create(['user_id' => Auth::id(), 'title' => $request->title, 'amount' => $request->amount, 'status' => 'pending']);
            return back()->with('success', 'Expense Claim Submitted!');
        })->name('expenses.store');
        Route::post('/expenses/{id}/approve', function($id) { Expense::find($id)->update(['status'=>'approved']); return back(); })->name('expenses.approve');
        Route::post('/expenses/{id}/reject', function($id) { Expense::find($id)->update(['status'=>'rejected']); return back(); })->name('expenses.reject');

        // Tickets
        Route::get('/tickets', function() {
            $isAdmin = Auth::user()->hasAnyRole(['hr', 'admin', 'super_admin']);
            $items = $isAdmin ? Ticket::with('user')->latest()->get() : Ticket::where('user_id', Auth::id())->latest()->get();
            
            return view('hr.generic_module', [
                'title' => 'Helpdesk Tickets', 'icon' => 'fa-ticket-alt', 'items' => $items, 'submitRoute' => route('hr.tickets.store'),
                'headers' => ['Employee', 'Subject', 'Priority', 'Created'], 'columns' => ['user_id', 'subject', 'priority', 'created_at'],
                'submitFields' => [
                    ['name' => 'subject', 'label' => 'Ticket Subject', 'type' => 'text', 'width' => 2],
                    ['name' => 'priority', 'label' => 'Priority Level', 'type' => 'select', 'options' => ['low'=>'Low', 'medium'=>'Medium', 'high'=>'High'], 'width' => 1],
                    ['name' => 'description', 'label' => 'Detailed Description', 'type' => 'textarea', 'width' => 2]
                ]
            ]);
        })->name('tickets');
        Route::post('/tickets', function(Request $request) {
            Ticket::create(['user_id' => Auth::id(), 'subject' => $request->subject, 'priority' => $request->priority, 'description' => $request->description, 'status' => 'open']);
            return back()->with('success', 'Ticket Opened!');
        })->name('tickets.store');

        // Documents
        Route::get('/documents', function() {
            $isAdmin = Auth::user()->hasAnyRole(['hr', 'admin', 'super_admin']);
            $items = $isAdmin ? Document::with('user')->latest()->get() : Document::where('user_id', Auth::id())->latest()->get();
            
            return view('hr.generic_module', [
                'title' => 'Company Documents', 'icon' => 'fa-file-alt', 'items' => $items, 'submitRoute' => route('hr.documents.store'),
                'headers' => ['Owner', 'Document Name', 'Type', 'Uploaded'], 'columns' => ['user_id', 'name', 'type', 'created_at'],
                'submitFields' => [
                    ['name' => 'name', 'label' => 'Document Title', 'type' => 'text', 'width' => 1],
                    ['name' => 'type', 'label' => 'Type / Category', 'type' => 'select', 'options' => ['policy'=>'Policy', 'contract'=>'Contract', 'other'=>'Other'], 'width' => 1]
                ]
            ]);
        })->name('documents');
        Route::post('/documents', function(Request $request) {
            Document::create(['user_id' => Auth::id(), 'name' => $request->name, 'type' => $request->type]);
            return back()->with('success', 'Document Record Created!');
        })->name('documents.store');

        // Probations
        Route::get('/probations', function() {
            $user = Auth::user();
            $isAdmin = $user->hasAnyRole(['hr', 'admin', 'super_admin']);
            
            if ($isAdmin) {
                $items = \App\Models\ProbationReview::with('user')->latest()->get();
                $employees = \App\Models\User::where('is_active', true)->where('role', '!=', 'hr')->orderBy('name')->get();
            } else {
                $teamIds = \App\Models\User::where('manager_id', $user->id)->pluck('id');
                $items = \App\Models\ProbationReview::whereIn('user_id', $teamIds)->with('user')->latest()->get();
                $employees = \App\Models\User::whereIn('id', $teamIds)->orderBy('name')->get();
            }
            
            $empOptions = ['' => '-- Select Employee --'];
            foreach($employees as $e) { $empOptions[$e->id] = $e->name . ' (' . $e->role . ')'; }
            
            return view('hr.generic_module', [
                'title' => 'Performance Reviews', 'icon' => 'fa-star', 'items' => $items, 'submitRoute' => route('hr.probations.store'),
                'headers' => ['Employee', 'Rating / 10', 'Comments', 'Created'], 'columns' => ['user_id', 'rating', 'comments', 'created_at'],
                'submitFields' => [
                    ['name' => 'employee_id', 'label' => 'Select Employee', 'type' => 'select', 'options' => $empOptions, 'width' => 2],
                    ['name' => 'rating', 'label' => 'Performance Rating (1-10)', 'type' => 'number', 'width' => 2],
                    ['name' => 'comments', 'label' => 'Comments', 'type' => 'textarea', 'width' => 2]
                ]
            ]);
        })->name('probations');
        Route::post('/probations', function(Request $request) {
            $userId = $request->employee_id ?? Auth::id();
            ProbationReview::create([
                'user_id' => $userId, 
                'manager_id' => Auth::id(),
                'rating' => $request->rating, 
                'comments' => $request->comments, 
                'status' => 'finalized'
            ]);
            return back()->with('success', 'Performance Review Recorded!');
        })->name('probations.store');

        // Policies
        Route::get('/policies', function() {
            $items = Policy::latest()->get();
            return view('hr.generic_module', [
                'title' => 'Employee Policies', 'icon' => 'fa-gavel', 'items' => $items, 'submitRoute' => route('hr.policies.store'),
                'headers' => ['Policy Title', 'Full Content', 'Created'], 'columns' => ['title', 'content', 'created_at'],
                'submitFields' => [
                    ['name' => 'title', 'label' => 'Policy Manual Title', 'type' => 'text', 'width' => 2],
                    ['name' => 'content', 'label' => 'Full Guidelines & Rules', 'type' => 'textarea', 'width' => 2]
                ]
            ]);
        })->name('policies');
        Route::post('/policies', function(Request $request) {
            Policy::create(['title' => $request->title, 'content' => $request->content, 'is_active' => true]);
            return back()->with('success', 'Policy Manual Enforced!'); 
        })->name('policies.store');

        Route::get('/profile-changes', function() { return view('generic_empty', ['title' => 'Profile Updates', 'icon' => 'fa-id-badge', 'message' => 'No Pending Profile Changes']); })->name('profile_changes');
        Route::get('/bulk-import', function() { return view('generic_empty', ['title' => 'Bulk Import Engine', 'icon' => 'fa-file-import', 'message' => 'Ready for Bulk Import']); })->name('bulk_import');
        
        // Announcements Module (Fully Working)
        Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements');
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

        Route::get('/keka-hire', function() { return view('generic_empty', ['title' => 'Keka Hire', 'icon' => 'fa-briefcase', 'message' => 'No Active Requisitions']); })->name('hire');
        Route::get('/invites', function() { 
            $items = \App\Models\User::where('status', 'invited')->orWhereNull('email_verified_at')->latest()->get();
            return view('hr.generic_module', [
                'title' => 'Invites & Registrations', 'icon' => 'fa-envelope-open-text', 'items' => $items, 'submitRoute' => null,
                'headers' => ['Name', 'Email', 'Invited At'], 'columns' => ['name', 'email', 'created_at'], 'submitFields' => []
            ]); 
        })->name('invites');
        
        Route::get('/joins-exits', function() { 
            $items = \App\Models\User::orderBy('created_at', 'desc')->take(20)->get();
            return view('hr.generic_module', [
                'title' => 'New Joins & Exits', 'icon' => 'fa-door-open', 'items' => $items, 'submitRoute' => null,
                'headers' => ['Employee', 'Role', 'Joining Date'], 'columns' => ['name', 'role', 'created_at'], 'submitFields' => []
            ]); 
        })->name('joins');
        
        Route::get('/logins', function() { 
            $items = \App\Models\User::orderBy('updated_at', 'desc')->take(50)->get();
            return view('hr.generic_module', [
                'title' => 'Audit Logins', 'icon' => 'fa-sign-in-alt', 'items' => $items, 'submitRoute' => null,
                'headers' => ['User', 'Email', 'Role', 'Last Activity'], 'columns' => ['name', 'email', 'role', 'updated_at'], 'submitFields' => []
            ]); 
        })->name('logins');
    });

    // Employees
    Route::resource('employees', EmployeeController::class); 

    // Holidays
    Route::resource('holidays', HolidayController::class)->except(['create', 'show', 'edit']);

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
        Route::get('/leave', [ReportController::class, 'leave'])->name('leave');
        Route::get('/summary', [ReportController::class, 'summary'])->name('summary');
    });
});
