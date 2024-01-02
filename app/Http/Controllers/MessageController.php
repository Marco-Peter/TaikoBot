<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(MessageChannel $channel): Response
    {
        return Inertia::render('Message/Index', [
            'channel' => $channel->load([
                'messages',
                'messages.user:id,first_name,last_name',
            ]),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(MessageChannel $channel): Response
    {
        return Inertia::render('Message/Create', [
            'channel' => $channel,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, MessageChannel $channel)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);
        $validated['user_id'] = Auth::user()->id;

        //dd($validated);
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
