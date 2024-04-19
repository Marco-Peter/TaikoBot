<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSettingsController extends Controller
{
    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Fortify\Contracts\UpdatesUserProfileInformation  $updater
     * @return \Laravel\Fortify\Contracts\ProfileInformationUpdatedResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'lessonNotificationTime' => 'required|numeric',
        ]);

        $settings = $user->settings;
        $settings['lessonNotificationTime'] = $validated['lessonNotificationTime'];
        $user->settings = $settings;
        $user->save();

        foreach ($user->lessons as $lesson) {
            $lesson->pivot->setReminder();
        }

        return back();
    }
}
