<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $attendances = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->get();

        $stats = [
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'leave' => $attendances->where('status', 'on_leave')->count(),
            'wfh' => $attendances->where('status', 'work_from_home')->count(),
            'holiday' => $attendances->where('status', 'holiday')->count(),
            'weekend' => $attendances->where('status', 'weekend')->count(),
            'total_hours' => $attendances->sum('working_hours'),
        ];

        return view('attendance.index', compact('attendances', 'stats', 'month', 'year'));
    }

    public function checkIn(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            [
                'check_in' => $now,
                'status' => 'present', // simple logic: present. Real logic would check shift time
                'check_in_ip' => $request->ip(),
                'shift_id' => $user->shift_id,
            ]
        );

        if (!$attendance->wasRecentlyCreated && !$attendance->check_in) {
            $attendance->update([
                'check_in' => $now,
                'check_in_ip' => $request->ip(),
                'status' => 'present'
            ]);
        }

        return back()->with('success', 'Checked in successfully at ' . $now->format('H:i'));
    }

    public function checkOut(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        $attendance = Attendance::where('user_id', $user->id)->where('date', $today)->first();

        if (!$attendance || !$attendance->check_in) {
            return back()->withArray(['error' => 'You must check in first.']);
        }

        $checkIn = Carbon::parse($attendance->check_in);
        $hours = $checkIn->floatDiffInHours($now);

        $attendance->update([
            'check_out' => $now,
            'working_hours' => $hours,
            'check_out_ip' => $request->ip()
        ]);

        return back()->with('success', 'Checked out successfully at ' . $now->format('H:i'));
    }

    public function teamAttendance(Request $request)
    {
        $user = Auth::user();
        $date = $request->input('date', Carbon::today()->toDateString());
        
        if ($user->hasAnyRole(['admin', 'hr', 'super_admin'])) {
            $team = User::where('is_active', true)->where('id', '!=', $user->id)->with(['attendances' => function($q) use ($date) {
                $q->where('date', $date);
            }, 'department'])->orderBy('name')->get();
        } else {
            $teamIds = User::where('manager_id', $user->id)->pluck('id');
            $team = User::whereIn('id', $teamIds)->with(['attendances' => function($q) use ($date) {
                $q->where('date', $date);
            }, 'department'])->orderBy('name')->get();
        }

        return view('attendance.team', compact('team', 'date'));
    }

    public function adminReport(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        
        $attendances = User::with(['attendances' => function($q) use ($date) {
            $q->where('date', $date);
        }, 'department'])->paginate(20);

        return view('attendance.report', compact('attendances', 'date'));
    }

    public function show($id)
    {
        $attendance = Attendance::findOrFail($id);
        return view('attendance.show', compact('attendance'));
    }   

    public function calendar(Request $request)
    {
        $user = Auth::user();
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $attendances = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year) 
            ->get() 
            ->keyBy(function($item) {
                return \Carbon\Carbon::parse($item->date)->format('Y-m-d');
            });

        $yearHolidays = \App\Models\Holiday::whereYear('date', $year)->orderBy('date')->get();

        // Dynamically add team outings (last Friday of each month)
        for($m = 1; $m <= 12; $m++) {
            $outingDate = \Carbon\Carbon::create($year, $m, 1)->lastOfMonth(\Carbon\Carbon::FRIDAY);
            $yearHolidays->push((object)[
                'name' => 'Team Outing',
                'date' => $outingDate->format('Y-m-d'),
                'type' => 'event'
            ]);
        }          
        
        $yearHolidays = $yearHolidays->sortBy('date')->values();

        $holidays = $yearHolidays->keyBy(function($item) {
            return \Carbon\Carbon::parse($item->date)->format('Y-m-d');
        });

        return view('attendance.calendar', compact('attendances', 'month', 'year', 'yearHolidays', 'holidays'));
    }
}
