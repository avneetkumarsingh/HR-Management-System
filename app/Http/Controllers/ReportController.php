<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function attendance(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        
        $users = User::with(['department', 'attendances' => function($q) use ($month, $year) {
            $q->whereMonth('date', $month)->whereYear('date', $year);
        }])->where('is_active', true)->get();

        $report = $users->map(function ($user) {
            return [
                'employee' => $user,
                'present' => $user->attendances->where('status', 'present')->count(),
                'absent' => $user->attendances->where('status', 'absent')->count(),
                'leave' => $user->attendances->where('status', 'on_leave')->count(),
                'late' => $user->attendances->where('status', 'late')->count(),
                'total_hours' => $user->attendances->sum('working_hours'),
                'avg_hours' => $user->attendances->where('working_hours', '>', 0)->avg('working_hours') ?? 0,
            ];
        });

        return view('reports.attendance', compact('report', 'month', 'year'));
    }

    public function leave(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        
        $leaves = LeaveRequest::with(['user', 'type', 'approver'])
            ->whereYear('from_date', $year)
            ->paginate(20);
            
        $stats = [
            'total' => LeaveRequest::whereYear('from_date', $year)->count(),
            'pending' => LeaveRequest::whereYear('from_date', $year)->where('status', 'pending')->count(),
            'approved' => LeaveRequest::whereYear('from_date', $year)->where('status', 'approved')->count(),
            'rejected' => LeaveRequest::whereYear('from_date', $year)->where('status', 'rejected')->count(),
        ];

        return view('reports.leave', compact('leaves', 'stats', 'year'));
    }

    public function summary(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        
        $attendances = Attendance::whereMonth('date', $month)->whereYear('date', $year)->get();
        $totalEmployees = User::where('is_active', true)->count();
        $workingDays = $attendances->pluck('date')->unique()->count();
        
        $stats = [
            'total_employees' => $totalEmployees,
            'working_days' => $workingDays,
            'total_leave_days' => LeaveRequest::where('status', 'approved')->whereMonth('from_date', $month)->sum('days'),
            'late_arrivals' => $attendances->where('status', 'late')->count(),
            'wfh_count' => $attendances->where('status', 'work_from_home')->count(),
        ];
        
        $expected = $totalEmployees * $workingDays;
        $actual = $attendances->whereIn('status', ['present', 'late', 'half_day'])->count();
        $stats['attendance_rate'] = $expected > 0 ? round(($actual / $expected) * 100, 2) : 0;

        return view('reports.summary', compact('stats', 'month', 'year'));
    }
}
