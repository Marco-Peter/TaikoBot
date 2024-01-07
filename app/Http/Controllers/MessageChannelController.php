<?php

namespace App\Http\Controllers;

use App\Models\MessageChannel;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class MessageChannelController extends Controller
{
    /**
     * Display a listing of all subscribed message channels.
     */
    public function index(): Response
    {
        if (Gate::allows('edit-messageChannels')) {
            $channels = MessageChannel::all();
        } else {
            $channels = Auth::user()->subscribedMessageChannels;
        }

        return Inertia::render('Message/Channel/Index', [
            'channels' => $channels,
            'pushServerPublicKey' => Env('VAPID_PUBLIC_KEY'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        Gate::authorize('edit-messageChannels');

        return Inertia::render('Message/Channel/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('edit-messageChannels');

        $validated = $request->validate([
            'name' => 'required|string',
        ]);

        $messageChannel = MessageChannel::create($validated);
        return redirect(route('channels.edit', $messageChannel->id));
    }

    /**
     * Display the specified resource.
     */
    public function show(MessageChannel $channel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MessageChannel $channel): Response
    {
        Gate::authorize('edit-messageChannels');

        $channel->load('recipients:id,first_name,last_name');
        return Inertia::render('Message/Channel/Edit', [
            'messageChannel' => $channel,
            'users' => User::all(['id', 'first_name', 'last_name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MessageChannel $channel): RedirectResponse
    {
        Gate::authorize('edit-messageChannels');

        $validated = $request->validate([
            'name' => 'required|string',
        ]);

        $channel->update($validated);
        return redirect(route('channels.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MessageChannel $channel): RedirectResponse
    {
        Gate::authorize('edit-messageChannels');

        $channel->delete();
        return back();
    }

    public function addRecipient(Request $request, MessageChannel $channel): RedirectResponse
    {
        $validated = $request->validate([
            'can_post' => 'boolean',
        ]);

        $recipient = User::find($request->recipient);
        $rc = $channel->recipients()->syncWithoutDetaching($recipient, [
            'can_post' => $validated['can_post'],
        ]);

        if (!$rc["attached"]) {
            $channel->recipients()->updateExistingPivot($recipient, [
                'can_post' => $validated['can_post'],
            ]);
        }

        return back();
    }

    public function removeRecipient(Request $request, MessageChannel $channel): RedirectResponse
    {
        $recipient = User::find($request->recipient);
        $channel->recipients()->detach($recipient);

        return back();
    }

    public function setCanPost(Request $request, MessageChannel $channel): RedirectResponse
    {
        $recipient = User::find($request->recipient);
        $channel->recipients()->updateExistingPivot($recipient, [
            'can_post' => $request->can_post,
        ]);

        return back();
    }
}
