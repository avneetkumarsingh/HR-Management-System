<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveType;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use App\Models\User;
use App\Models\Attendance; // ✅ IMPORTANT
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $year = \Carbon\Carbon::now()->year;
        // Clear notification dot
        session(['last_seen_leaves_at' => now()]);

        $balances = LeaveBalance::where('user_id', $user->id)
            ->where('year', $year)
            ->with('type')
            ->get()
            ->filter(function($balance) use ($user) {
                $name = strtolower($balance->type->name);
                if (strpos($name, 'maternity') !== false && strtolower($user->gender) !== 'female') return false;
                if (strpos($name, 'paternity') !== false && strtolower($user->gender) !== 'male') return false;
                return true;
            });

        $requests = LeaveRequest::where('user_id', $user->id)
            ->with('type')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('leaves.index', compact('balances', 'requests'));
    }

    public function create()
    {
        $allTypes = LeaveType::where('is_active', true)->get();
        $user = Auth::user();

        // Filter out leaves based on eligibility
        $types = $allTypes->filter(function($type) use ($user) {
            $name = strtolower($type->name);
            
            if (strpos($name, 'maternity') !== false) {
                // Must be female, married, and have at least 1 year of service
                if ($user->gender !== 'female' || $user->marital_status !== 'married') {
                    return false;
                }
                if (!$user->date_of_joining || Carbon::parse($user->date_of_joining)->addYear()->isFuture()) {
                    return false;
                }
            }
            
            if (strpos($name, 'paternity') !== false) {
                // Must be male and married
                if ($user->gender !== 'male' || $user->marital_status !== 'married') {
                    return false;
                }
            }
            
            return true;
        });

        $users = User::where('is_active', true)
            ->where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get();

        return view('leaves.create', compact('types', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type_name' => 'required|string',
            'from_date' => 'required|date|after_or_equal:today',
            'to_date' => 'required|date|after_or_equal:from_date',
            'half_day' => 'in:none,first_half,second_half',
            'reason' => 'required|string',
            'cc_users' => 'nullable|array',
            'cc_users.*' => 'exists:users,id',
            'document' => 'nullable|file|mimes:pdf,jpg,png|max:2048'
        ]);

        $user = Auth::user();

        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date);
        $days = $fromDate->diffInDays($toDate) + 1;

        if ($request->half_day != 'none') {
            $days -= 0.5;
        }

        // Overlap check
        $overlap = LeaveRequest::where('user_id', $user->id)
            ->whereNotIn('status', ['rejected', 'cancelled'])
            ->where(function ($q) use ($request) {
                $q->whereBetween('from_date', [$request->from_date, $request->to_date])
                  ->orWhereBetween('to_date', [$request->from_date, $request->to_date])
                  ->orWhere(function ($q2) use ($request) {
                      $q2->where('from_date', '<=', $request->from_date)
                         ->where('to_date', '>=', $request->to_date);
                  });
            })->exists();

        if ($overlap) {
            return back()->withErrors(['overlap' => 'You already have a leave request during this period.']);
        }

        // Leave type
        $leaveType = LeaveType::whereRaw('LOWER(name) = ?', [strtolower($request->leave_type_name)])->first();

        if (!$leaveType) {
            $leaveType = LeaveType::create([
                'name' => $request->leave_type_name,
                'code' => strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $request->leave_type_name), 0, 3)) . rand(10,99),
                'days_allowed' => 0,
                'is_paid' => false,
                'carry_forward' => false,
                'color' => '#64748b'
            ]);
        }

        // Balance logic handling dynamic allowed days for Maternity/Paternity
        $allowedDays = $leaveType->days_allowed;
        $nameLower = strtolower($leaveType->name);

        if (strpos($nameLower, 'maternity') !== false) {
            // First 2 children = 6 months (~180 days). 3rd+ = 12 weeks (84 days)
            $allowedDays = $user->children_count < 2 ? 180 : 84;
        } elseif (strpos($nameLower, 'paternity') !== false) {
            $allowedDays = 12;
        }

        $balance = LeaveBalance::firstOrCreate([
            'user_id' => $user->id,
            'leave_type_id' => $leaveType->id,
            'year' => Carbon::now()->year
        ], [
            'allowed' => $allowedDays,
            'used' => 0,
            'pending' => $allowedDays
        ]);

        if ($leaveType->is_paid && $balance->pending < $days) {
            return back()->withErrors(['balance' => 'Insufficient leave balance.']);
        }

        // Save leave
        $leaveRequest = new LeaveRequest();
        $leaveRequest->user_id = $user->id;
        $leaveRequest->leave_type_id = $leaveType->id;
        $leaveRequest->from_date = $request->from_date;
        $leaveRequest->to_date = $request->to_date;
        $leaveRequest->half_day = $request->half_day ?? 'none';
        $leaveRequest->reason = $request->reason;
        $leaveRequest->days = $days;
        $leaveRequest->status = 'pending';

        if ($request->hasFile('document')) {
            $leaveRequest->document = $request->file('document')->store('leaves', 'public');
        }

        $leaveRequest->save();

        return redirect()->route('leaves.index')->with('success', 'Leave request submitted successfully.');
    }

    public function show($id)
    {
        $leave = LeaveRequest::with(['user', 'type'])->findOrFail($id);
        return view('leaves.show', compact('leave'));
    }

    public function cancel($id)
    {
        $leave = LeaveRequest::where('user_id', Auth::id())->findOrFail($id);

        if ($leave->status === 'pending') {
            $leave->update(['status' => 'cancelled']);
            return back()->with('success', 'Leave cancelled.');
        }

        return back()->withErrors(['error' => 'Cannot cancel processed request.']);
    }

    // ✅ Approvals
    public function pendingApprovals()
    {
        $user = Auth::user();

        if ($user->hasAnyRole(['hr', 'admin', 'super_admin'])) {

            $requests = LeaveRequest::where('status', 'pending')
                ->with(['user', 'type'])
                ->get();

        } else {

            $teamIds = User::where('manager_id', $user->id)->pluck('id');

            $requests = LeaveRequest::whereIn('user_id', $teamIds)
                ->where('status', 'pending')
                ->with(['user', 'type'])
                ->get();
        }

        return view('leaves.approvals', compact('requests'));
    }

    // ✅ APPROVE LEAVE (FIXED 🔥)
    public function approve($id)
    {
        $leave = LeaveRequest::findOrFail($id);

        $leave->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        // ✅ Create attendance (safe)
        $start = Carbon::parse($leave->from_date);
        $end = Carbon::parse($leave->to_date);

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {

            Attendance::updateOrCreate(
            [
                'user_id' => $leave->user_id,
                'date' => $date->toDateString(),
            ],
            [
                'status' => 'on_leave'
            ]
        );
        }

        // Deduct balance
        $balance = LeaveBalance::where('user_id', $leave->user_id)
            ->where('leave_type_id', $leave->leave_type_id)
            ->where('year', Carbon::parse($leave->from_date)->year)
            ->first();

        if ($balance) {
            $balance->used += $leave->days;
            $balance->pending -= $leave->days;
            $balance->save();
        }

        return back()->with('success', 'Leave approved.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['rejection_reason' => 'required|string']);

        $leave = LeaveRequest::findOrFail($id);

        $leave->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason
        ]);

        return back()->with('success', 'Leave rejected.');
    }

    public function types()
    {
        $types = LeaveType::all();
        return view('leaves.types', compact('types'));
    }

    public function storeType(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|unique:leave_types',
            'days_allowed' => 'required|integer'
        ]);

        LeaveType::create([
            'name' => $request->name,
            'code' => $request->code,
            'days_allowed' => $request->days_allowed,
            'is_paid' => $request->is_paid ?? false,
            'carry_forward' => $request->carry_forward ?? false,
            'color' => '#475569'
        ]);

        return back()->with('success', 'Leave type created.');
    }

    public function updateType(Request $request, $id)
    {
        $type = LeaveType::findOrFail($id);
        $type->update($request->all());

        return back()->with('success', 'Leave type updated.');
    }
}