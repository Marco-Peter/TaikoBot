<?php

namespace App\Http\Controllers;

use App\Enums\LessonParticipationEnum;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'start' => 'required',
            'finish' => 'required',
            'course_id' => 'required|integer',
        ]);

        $lesson = Lesson::create($validated);
        $teams_signed_in = Course::find($validated['course_id'])->teams()->wherePivot('signed_in', 1)->get()->modelKeys();
        $teams_signed_out = Course::find($validated['course_id'])->teams()->wherePivot('signed_in', 0)->get()->modelKeys();
        $users_signed_in = User::wherein('team_id', $teams_signed_in)->get()->modelKeys();
        $users_signed_out = User::wherein('team_id', $teams_signed_out)->get()->modelKeys();

        $lesson->participants()->attach($users_signed_in, ['participation' => LessonParticipationEnum::SIGNED_IN->value]);
        $lesson->participants()->attach($users_signed_out, ['participation' => LessonParticipationEnum::SIGNED_OUT->value]);
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Lesson $lesson)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lesson $lesson): View
    {
        return view('management.edit-lesson', [
            'lesson' => $lesson,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lesson $lesson): RedirectResponse
    {
        dd($request);
        if ($request["changed_item"]) {
            $validated = $request->validate([
                'start' => 'required|date',
                'finish' => 'required|date',
            ]);
            $lesson->update($validated);
        } else if ($request["update_participation"]) {
            $validated = $request->validate([
                'participant' => 'required',
                'participation' => 'required',
            ]);
            $lesson->participants()->updateExistingPivot($validated['participant'], ['participation' => $validated['participation']]);
            $lesson->participants()->pivot()->save();
        }

        return redirect(route('courses.edit', $lesson->course));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return back();
    }
}
