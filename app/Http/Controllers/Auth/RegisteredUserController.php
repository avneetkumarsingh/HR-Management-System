<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $departments = \App\Models\Department::where('is_active', true)->where('code', '!=', 'HR')->get();
        return view('auth.register', compact('departments'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'department_id' => ['required', 'exists:departments,id'],
            'gender' => ['required', 'string', 'in:male,female,other'],
            'marital_status' => ['required', 'string', 'in:single,married,divorced,widowed'],
            'children_count' => ['required', 'integer', 'min:0'],
            'date_of_joining' => ['required', 'date'],
            'date_of_birth' => ['required', 'date'],
        ]);

        $manager = User::where('department_id', $request->department_id)->where('role', 'manager')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department_id' => $request->department_id,
            'role' => 'employee',
            'manager_id' => $manager ? $manager->id : null,
            'gender' => $request->gender,
            'marital_status' => $request->marital_status,
            'children_count' => $request->children_count,
            'date_of_joining' => $request->date_of_joining,
            'date_of_birth' => $request->date_of_birth,
        ]);

        event(new Registered($user));

        // Create Notifications
        $hrUsers = User::where('role', 'hr')->get();
        $notifiableUsers = $hrUsers;
        if ($manager) {
            $notifiableUsers->push($manager);
        }

        foreach($notifiableUsers as $notifiable) {
            \Illuminate\Support\Facades\DB::table('notifications')->insert([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'type' => 'App\Notifications\NewTeamMember',
                'notifiable_type' => User::class,
                'notifiable_id' => $notifiable->id,
                'data' => json_encode(['message' => $user->name . ' has joined your team!']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
