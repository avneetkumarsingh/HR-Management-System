<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Shift;
use App\Models\EmployeeProfile;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['department', 'designation']);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('employee_id', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status == 'active');
        }

        // Exclude HR department
        $query->whereHas('department', function($q) {
            $q->where('name', '!=', 'HR')->where('code', '!=', 'HR');
        });

        // Get all matching employees grouped by department_id
        $employees = $query->get()->groupBy('department_id');

        // Get departments for filter (excluding HR)
        $departments = Department::where('name', '!=', 'HR')
                                  ->where('code', '!=', 'HR')
                                  ->get();

        return view('employees.index', compact('employees', 'departments'));
    }

    public function create()
    {
        $departments = Department::all();
        $designations = Designation::all();
        $managers = User::role(['manager', 'hr'])->get();
        $shifts = Shift::all();
        $roles = \Spatie\Permission\Models\Role::all(); // Fetch dynamic roles
        
        // Auto generate employee_id
        $lastEmp = User::orderBy('id', 'desc')->first();
        $nextId = $lastEmp ? (int) str_replace('EMP', '', $lastEmp->employee_id) + 1 : 1;
        $autoEmployeeId = 'EMP' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('employees.create', compact('departments', 'designations', 'managers', 'shifts', 'autoEmployeeId', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string',
            'marital_status' => 'nullable|string|in:single,married,divorced,widowed',
            'children_count' => 'nullable|integer|min:0',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'employee_id' => 'required|string|unique:users',
            'role' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'manager_id' => 'nullable|exists:users,id',
            'shift_id' => 'required|exists:shifts,id',
            'date_of_joining' => 'required|date',
            'employment_type' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $dynamicRole = $validated['role'];
        $validated['role'] = in_array($dynamicRole, ['super_admin', 'admin', 'hr', 'manager', 'employee']) ? $dynamicRole : 'employee';

        $user = new User($validated);
        if (in_array($dynamicRole, ['manager', 'hr'])) {
            $user->manager_id = null;
        }
        $user->password = Hash::make($validated['password']);

        if ($request->hasFile('avatar')) {
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $user->save();
        $user->assignRole($dynamicRole);

        EmployeeProfile::create([
            'user_id' => $user->id,
            'employment_type' => $validated['employment_type']
        ]);

        // Assign default leave balances
        $year = Carbon::now()->year;
        $leaveTypes = LeaveType::where('is_active', true)->get();
        
        foreach ($leaveTypes as $type) {
            LeaveBalance::create([
                'user_id' => $user->id,
                'leave_type_id' => $type->id,
                'year' => $year,
                'allocated' => $type->days_allowed,
                'pending' => $type->days_allowed,
            ]);
        }

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function show($id)
    {
        $employee = User::with(['profile', 'department', 'designation', 'manager', 'attendances' => function($q) {
            $q->whereMonth('date', Carbon::now()->month)->latest('date')->take(10);
        }, 'leaveBalances.type', 'leaveRequests' => function($q) {
            $q->latest()->take(5);
        }])->findOrFail($id);

        return view('employees.show', compact('employee'));
    }

    public function edit($id)
    {
        $employee = User::with('profile')->findOrFail($id);    
        $departments = Department::all();
        $designations = Designation::all();
        $managers = User::role(['manager', 'hr'])->get();
        $shifts = Shift::all();
        $roles = \Spatie\Permission\Models\Role::all();
        
        return view('employees.edit', compact('employee', 'departments', 'designations', 'managers', 'shifts', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        
        $dynamicRole = $request->role ?? $user->roles->first()->name ?? $user->role;
        $dbRole = in_array($dynamicRole, ['super_admin', 'admin', 'hr', 'manager', 'employee']) ? $dynamicRole : 'employee';
        
        $updateData = $request->only([
            'name', 'phone', 'date_of_birth', 'gender', 'marital_status', 'children_count',
            'department_id', 'designation_id', 'manager_id', 'shift_id', 'date_of_joining'
        ]);
        $updateData['role'] = $dbRole;

        $user->update($updateData);

        if (in_array($dynamicRole, ['manager', 'hr'])) {
            $user->manager_id = null;
            $user->save();
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();      
        } 

        if ($request->hasFile('avatar')) {
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
            $user->save();
        }

        if ($request->has('role')) {
            $user->syncRoles([$request->role]);
        }

        $profile = EmployeeProfile::firstOrCreate(['user_id' => $user->id]);
        $profile->update($request->only([
            'blood_group', 'address', 'city', 'state', 'country', 'pincode',
            'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relation',
            'pan_number', 'aadhar_number', 'bank_account_number', 'bank_name', 'ifsc_code',
            'employment_type', 'probation_end_date', 'confirmation_date', 'notice_period'
        ]));

        return redirect()->route('employees.show', $user->id)->with('success', 'Employee updated.');
    } 

    public function destroy($id) 
    {
        $user = User::findOrFail($id);
        $user->is_active = false;
        $user->save();
        $user->delete(); // Soft delete
        return back()->with('success', 'Employee deactivated.');
    }
}
