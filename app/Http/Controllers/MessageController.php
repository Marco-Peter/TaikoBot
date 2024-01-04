<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnum;
use App\Models\Message;
use App\Models\MessageChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(MessageChannel $channel): Response
    {
        Gate::allowIf(Auth::user()->subscribedMessageChannels()
            ->where('id', $channel->id)->exists() ||
            Auth::user()->role === UserRoleEnum::ADMIN);

        if ($channel->messages()->exists()) {
            Auth::user()->subscribedMessageChannels()->updateExistingPivot($channel, [
                'read_until' => $channel->messages()->latest()->first()->created_at,
            ]);
        }

        return Inertia::render('Message/Index', [
            'channel' => $channel,
            'messages' => Message::with('user:id,first_name,last_name')
                ->where('message_channel_id', $channel->id)->latest()->paginate(10),
            'post_message' => Gate::allows('post-message', $channel),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(MessageChannel $channel): Response
    {
        Gate::authorize('post-message', [$channel]);

        return Inertia::render('Message/Create', [
            'channel' => $channel,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, MessageChannel $channel)
    {
        Gate::authorize('post-message', [$channel]);

        $validated = $request->validate([
            'content' => 'required|string',
        ]);
        $validated['user_id'] = Auth::user()->id;

        $channel->messages()->create($validated);
        return redirect(route('channels.messages.index', $channel->id));
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        //
    }
}
