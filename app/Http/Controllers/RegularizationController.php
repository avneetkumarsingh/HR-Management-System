<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegularizationRequest;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RegularizationController extends Controller
{
    public function index()
    {
        $requests = RegularizationRequest::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('attendance.regularization', compact('requests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'check_in' => 'nullable|date_format:Y-m-d\TH:i',
            'check_out' => 'nullable|date_format:Y-m-d\TH:i|after:check_in',
            'reason' => 'required|string',
        ]);

        RegularizationRequest::create([
            'user_id' => Auth::id(),
            'date' => $request->date,
            'check_in' => $request->check_in ? Carbon::parse($request->check_in) : null,
            'check_out' => $request->check_out ? Carbon::parse($request->check_out) : null,
            'reason' => $request->reason,
        ]);

        return back()->with('success', 'Regularization request submitted.');
    }

    public function approvals()
    {
        $teamIds = \App\Models\User::where('manager_id', Auth::id())->pluck('id');
        
        $requests = RegularizationRequest::whereIn('user_id', $teamIds)
            ->where('status', 'pending')
            ->with(['user'])
            ->get();
            
        return view('attendance.regularization_approvals', compact('requests'));
    }

    public function approve($id)
    {
        $req = RegularizationRequest::findOrFail($id);
        
        $req->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        // Update attendance
        $attendance = Attendance::firstOrCreate(
            ['user_id' => $req->user_id, 'date' => $req->date],
            ['shift_id' => $req->user->shift_id ?? null]
        );

        $attendance->update([
            'check_in' => $req->check_in ?? $attendance->check_in,
            'check_out' => $req->check_out ?? $attendance->check_out,
            'is_regularized' => true,
        ]);
        
        // Recalculate hours if both exist
        if ($attendance->check_in && $attendance->check_out) {
            $attendance->working_hours = Carbon::parse($attendance->check_in)->floatDiffInHours($attendance->check_out);
            $attendance->status = 'present'; // Simplification
            $attendance->save();
        }

        return back()->with('success', 'Request approved.');
    }

    public function reject(Request $request, $id)
    {
        $req = RegularizationRequest::findOrFail($id);
        $req->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason
        ]);

        return back()->with('success', 'Request rejected.');
    }
}
