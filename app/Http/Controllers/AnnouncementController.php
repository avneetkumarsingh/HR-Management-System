<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('author')->latest()->get();
        return view('hr.announcements', compact('announcements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,critical,event',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_active'] = true;

        Announcement::create($validated);
        return back()->with('success', 'Announcement published successfully!');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Announcement removed.');
    }
}
