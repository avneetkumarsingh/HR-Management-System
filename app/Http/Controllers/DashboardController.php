<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        // Today's attendance
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        // Monthly stats
        $monthlyAttendances = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();
            
        $myApprovedLeaves = LeaveRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where(function($q) use ($month, $year) {
                $q->whereMonth('from_date', $month)->whereYear('from_date', $year)
                  ->orWhereMonth('to_date', $month)->whereYear('to_date', $year);
            })
            ->get();
            
        $leaveDaysCount = 0;
        foreach ($myApprovedLeaves as $leave) {
            $start = Carbon::parse($leave->from_date);
            $end = Carbon::parse($leave->to_date);
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                if ($d->month == $month && $d->year == $year) {
                    $leaveDaysCount += ($leave->half_day !== 'none' && !is_null($leave->half_day) && $leave->half_day !== '') ? 0.5 : 1;
                }
            }
        }

        $stats = [
            'present' => $monthlyAttendances->whereIn('status', ['present', 'late', 'half_day'])->count(),
            'absent' => $monthlyAttendances->where('status', 'absent')->count(),
            'late' => $monthlyAttendances->where('status', 'late')->count(),
            'leave' => $leaveDaysCount,
            'wfh' => $monthlyAttendances->where('status', 'work_from_home')->count(),
            'avg_hours' => $monthlyAttendances->where('working_hours', '>', 0)->avg('working_hours') ?? 0,
        ];

        // Upcoming holidays
        $upcomingHolidays = Holiday::where('date', '>=', $today)
            ->where('is_active', true)
            ->orderBy('date')
            ->take(5)
            ->get();

        $managerData = [];
        $adminData = [];

        // Team Today Visibility (Employees see own team, HR sees all cross-org)
        if ($user->hasAnyRole(['admin', 'hr', 'super_admin'])) {
            $managerData['team_today'] = User::where('is_active', true)->where('id', '!=', $user->id)
                ->with(['attendances' => function($q) use ($today) { $q->where('date', $today); }, 'department'])
                ->take(15)->get(); // Limit to 15 for HR preview
        } else {
            $teamManagerId = $user->role === 'manager' ? $user->id : $user->manager_id;
            if ($teamManagerId) {
                $managerData['team_today'] = User::where(function($q) use ($teamManagerId) {
                        $q->where('manager_id', $teamManagerId)->orWhere('id', $teamManagerId);
                    })
                    ->where('id', '!=', $user->id)
                    ->with(['attendances' => function($q) use ($today) { $q->where('date', $today); }, 'department'])
                    ->get();
            } else {
                $managerData['team_today'] = collect();
            }
        }

        // Pending Approvals (Managers & Up)
        if ($user->hasAnyRole(['manager', 'admin', 'hr', 'super_admin'])) {
            $managedIds = User::where('manager_id', $user->id)->pluck('id');
            $managerData['pending_leaves'] = LeaveRequest::whereIn('user_id', $managedIds)
                ->where('status', 'pending')
                ->with(['user', 'type'])
                ->get();
        }

        // Upcoming Birthdays within 30 days
        $now = Carbon::now();
        $in30Days = Carbon::now()->addDays(30);

        $upcomingBirthdays = User::where('is_active', true)->whereNotNull('date_of_birth')->get()->filter(function($u) use ($now, $in30Days) {
            $date = Carbon::parse($u->date_of_birth);
            $date->year = $now->year;
            if ($date->isPast()) $date->addYear();
            return $date->between($now, $in30Days);
        })->sortBy(fn($u) => Carbon::parse($u->date_of_birth)->year(Carbon::parse($u->date_of_birth)->year($now->year)->isPast() ? $now->year + 1 : $now->year)->timestamp)
          ->take(5);

        // Upcoming Work Anniversaries within 30 days
        $upcomingAnniversaries = User::where('is_active', true)->whereNotNull('date_of_joining')->get()->filter(function($u) use ($now, $in30Days) {
            $date = Carbon::parse($u->date_of_joining);
            if ($date->copy()->addYear()->isAfter($in30Days)) return false; // Exclude people who haven't completed 1 year yet
            $date->year = $now->year;
            if ($date->isPast()) $date->addYear();
            return $date->between($now, $in30Days);
        })->sortBy(fn($u) => Carbon::parse($u->date_of_joining)->year(Carbon::parse($u->date_of_joining)->year($now->year)->isPast() ? $now->year + 1 : $now->year)->timestamp)
          ->take(5);

        if ($user->hasAnyRole(['admin', 'hr', 'super_admin'])) {
            $activeUsers = User::where('is_active', true)->with('shift')->get();
            $activeUserIds = $activeUsers->pluck('id')->toArray();
            $totalEmployees = count($activeUserIds);
            
            $todayAttendances = Attendance::where('date', $today)->get();
            $presentUserIds = [];
            $lateUserIds = [];
            
            // Dynamically evaluate late arrivals across the company using shifted work times
            foreach ($todayAttendances as $att) {
                if (in_array($att->status, ['present', 'late', 'half_day'])) {
                    $presentUserIds[] = $att->user_id;
                    $employee = $activeUsers->firstWhere('id', $att->user_id);
                    if ($employee && $att->check_in) {
                        $checkInTime = Carbon::parse($att->check_in)->format('H:i:s');
                        $shiftStart = $employee->shift ? $employee->shift->start_time : '10:00:00';
                        $grace = $employee->shift ? ($employee->shift->grace_time ?? 0) : 0;
                        
                        $allowedTime = Carbon::parse($shiftStart)->addMinutes($grace)->format('H:i:s');
                        if ($checkInTime > $allowedTime) {
                            $lateUserIds[] = $att->user_id;
                        }
                    }
                }
            }
            
            $onLeaveUserIds = LeaveRequest::where('status', 'approved')
                                          ->whereDate('from_date', '<=', $today)
                                          ->whereDate('to_date', '>=', $today)
                                          ->pluck('user_id')
                                          ->toArray();
            
            $presentUserIds = array_diff($presentUserIds, $onLeaveUserIds);
            
            $presentToday = count(array_intersect($presentUserIds, $activeUserIds));
            $onLeaveToday = count(array_unique(array_intersect($onLeaveUserIds, $activeUserIds)));
            $absentUserIds = array_diff($activeUserIds, $presentUserIds, $onLeaveUserIds);
            
            $adminData = [
                'total_employees' => $totalEmployees,
                'present_today' => $presentToday,
                'on_leave_today' => $onLeaveToday,
                'absent_today' => count($absentUserIds),
                'pending_approvals' => LeaveRequest::where('status', 'pending')->count(),
            ];
            
            // Override Personal Stats block with the exact Company Metrics for HR Dashboard expectation
            $stats = [
                'present' => $presentToday,
                'absent' => count($absentUserIds),
                'late' => count(array_intersect($lateUserIds, $activeUserIds)),
                'leave' => $onLeaveToday,
                'wfh' => $todayAttendances->where('status', 'work_from_home')->whereIn('user_id', $activeUserIds)->count(),
                'avg_hours' => number_format($todayAttendances->where('working_hours', '>', 0)->avg('working_hours') ?? 0, 1),
            ];
        }

        $myLeaves = LeaveRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with('type')
            ->get();
            
        $announcements = Announcement::with('author')->where('is_active', true)->latest()->take(3)->get();

        return view('dashboard.index', compact(
            'user', 'todayAttendance', 'stats', 'upcomingHolidays', 
            'managerData', 'adminData', 'myLeaves', 'today', 'upcomingBirthdays', 'upcomingAnniversaries', 'announcements'
        ));
    }
}
