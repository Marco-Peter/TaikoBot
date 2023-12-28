<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnum;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    protected $only = ['index', 'create', 'store', 'edit', 'update', 'destroy'];

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        Gate::authorize('edit-users');

        $users = User::with(['team'])->get();

        return Inertia::render('User/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        Gate::authorize('edit-users');

        return Inertia::render('User/Create', [
            'roles' => UserRoleEnum::values(),
            'teams' => Team::all(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('edit-users');


        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:App\Models\User',
            'role' => [
                'required',
                Rule::enum(UserRoleEnum::class),
            ],
            'team_id' => 'required',
        ]);

        $validated['password'] = Hash::make('password');
        User::create($validated);

        return to_route('users.index')->with('message', 'User created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): Response
    {
        Gate::authorize('edit-users');

        return Inertia::render('User/Edit', [
            'user' => $user,
            'roles' => UserRoleEnum::values(),
            'teams' => Team::all(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('edit-users');

        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => [
                'required',
                'email',
                Rule::unique('App\Models\User')->ignore($user->id),
            ],
            'role' => [
                'required',
                Rule::enum(UserRoleEnum::class),
            ],
            'team_id' => 'required|exists:teams,id',
        ]);

        return to_route('users.index')->with('message', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('edit-users');

        $user->delete();
        return to_route('users.index')->with('message', 'User deleted successfully');
    }
}
