<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Holiday;
use Carbon\Carbon;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        
        $holidays = Holiday::where('year', $year)
            ->orderBy('date')
            ->get();
            
        $holidaysByDate = $holidays->keyBy(function($item) {
            return Carbon::parse($item->date)->format('Y-m-d');
        });

        return view('holidays.index', compact('holidays', 'holidaysByDate', 'month', 'year'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'date' => 'required|date',
            'type' => 'required|in:national,regional,optional,company',
            'description' => 'nullable|string',
        ]);

        $validated['year'] = Carbon::parse($validated['date'])->year;
        
        Holiday::create($validated);
        return back()->with('success', 'Holiday added.');
    }

    public function update(Request $request, $id)
    {
        $holiday = Holiday::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string',
            'date' => 'required|date',
            'type' => 'required|in:national,regional,optional,company',
        ]);
        
        $validated['year'] = Carbon::parse($validated['date'])->year;
        $holiday->update($validated);
        
        return back()->with('success', 'Holiday updated.');
    }

    public function destroy($id)
    {
        Holiday::findOrFail($id)->delete();
        return back()->with('success', 'Holiday removed.');
    }
}
