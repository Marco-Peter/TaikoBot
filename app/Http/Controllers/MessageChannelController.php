<?php

namespace App\Http\Controllers;

use App\Models\MessageChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MessageChannelController extends Controller
{
    /**
     * Display a listing of all subscribed message channels.
     */
    public function index()
    {
        return Inertia::render('Message/Channel/Index', [
            'channels' => Auth::user()->subscribedMessageChannels,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(MessageChannel $messageChannel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MessageChannel $messageChannel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MessageChannel $messageChannel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MessageChannel $messageChannel)
    {
        //
    }
}
