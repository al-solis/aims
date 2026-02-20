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
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'lname' => ['required', 'string', 'max:30'],
            'fname' => ['required', 'string', 'max:30'],
            'mname' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['integer'],
            'is_active' => ['boolean'],
        ]);

        $user = User::create([
            'lname' => $request->lname,
            'fname' => $request->fname,
            'mname' => $request->mname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 0,
            'is_active' => $request->is_active ?? false,
        ]);

        // event(new Registered($user));

        // Auth::login($user);

        // return redirect(route('dashboard', absolute: false));
        return redirect()->route('user.index')->with('success', 'User created successfully.');
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = User::where('is_active', false)->count();

        $users = User::latest();

        if ($search) {
            $users = $users->where(function ($query) use ($search) {
                $query->where('lname', 'like', "%{$search}%")
                    ->orWhere('fname', 'like', "%{$search}%")
                    ->orWhere('mname', 'like', "%{$search}%");
            });
        }

        if ($status !== null && $status !== '') {
            $users = $users->where('is_active', (bool) $status);
        }

        $users = $users->paginate(config('app.paginate'))
            ->appends([
                'search' => $search,
                'status' => $status,
            ]);

        return view('setup.user.index', compact('users', 'totalUsers', 'activeUsers', 'inactiveUsers'));
    }

    public function update($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'lname' => request('edit_lname'),
            'fname' => request('edit_fname'),
            'mname' => request('edit_mname'),
            'email' => request('edit_email'),
            'role' => request('edit_role'),
            'is_active' => request('edit_is_active'),
        ]);

        return redirect()->route('user.index')->with('success', 'User updated successfully.');
    }
}
