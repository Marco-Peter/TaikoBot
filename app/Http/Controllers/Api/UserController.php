<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('edit-users');
        return UserResource::collection(User::with('team')->orderBy('first_name')->get());
    }

    public function store(Request $request): UserResource
    {
        Gate::authorize('edit-users');

        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'email'      => 'required|email|unique:App\Models\User',
            'role'       => ['required', Rule::enum(UserRoleEnum::class)],
            'karma'      => 'nullable|integer|min:0',
            'team_id'    => 'required|exists:teams,id',
        ]);

        $validated['password'] = Hash::make('password');
        $user = User::create($validated);

        return new UserResource($user);
    }

    public function show(User $user): UserResource
    {
        Gate::authorize('edit-users');
        return new UserResource($user->load('team'));
    }

    public function update(Request $request, User $user): UserResource
    {
        Gate::authorize('edit-users');

        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'email'      => ['required', 'email', Rule::unique('App\Models\User')->ignore($user->id)],
            'role'       => ['required', Rule::enum(UserRoleEnum::class)],
            'karma'      => 'nullable|integer|min:0',
            'team_id'    => 'required|exists:teams,id',
        ]);

        $user->update($validated);
        return new UserResource($user->fresh());
    }

    public function destroy(User $user): JsonResponse
    {
        Gate::authorize('edit-users');
        $user->delete();
        return response()->json(null, 204);
    }

    public function updatePayment(User $user): JsonResponse
    {
        Gate::authorize('edit-users');
        $user->last_payment = now();
        $user->save();
        return response()->json(['last_payment' => $user->last_payment->toIso8601String()]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'lessonNotificationTime' => 'required|numeric|min:0',
            'waitlistCancelTime'     => 'required|numeric|min:0',
        ]);

        $settings = $user->settings ?? [];
        $settings['lessonNotificationTime'] = $validated['lessonNotificationTime'];
        $settings['waitlistCancelTime'] = $validated['waitlistCancelTime'];
        $user->settings = $settings;
        $user->save();

        foreach ($user->lessons as $lesson) {
            $lesson->pivot->setReminder();
        }

        return response()->json(['settings' => $user->settings]);
    }

    public function updatePushSubscription(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'endpoint'    => 'required|url:https',
            'keys.auth'   => 'required|string',
            'keys.p256dh' => 'required|string',
        ]);

        $user->updatePushSubscription(
            $validated['endpoint'],
            $validated['keys']['p256dh'],
            $validated['keys']['auth']
        );

        return response()->json(['message' => 'Push subscription updated']);
    }

    public function deletePushSubscription(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate(['endpoint' => 'required|url:https']);
        $user->deletePushSubscription($validated['endpoint']);
        return response()->json(null, 204);
    }
}
