<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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
        Gate::authorize('edit-courses');

        $validated = $request->validate([
            'start' => 'required',
            'finish' => 'required',
            'course_id' => 'required|integer',
            'title' => 'required|string|max:255',
        ]);
        $validated['notes'] = '';

        $lesson = Lesson::create($validated);
        $participants = Course::find($validated['course_id'])->participants()->get()->modelKeys();
        $lesson->participants()->attach($participants);
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
        Gate::authorize('edit-courses');

        return view('management.edit-lesson', [
            'lesson' => $lesson,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lesson $lesson): RedirectResponse
    {
        Gate::authorize('edit-courses');

        if ($request["changed_item"]) {
            $validated = $request->validate([
                'start' => 'required|date',
                'finish' => 'required|date',
                'title' => 'required|string|max:255',
                'notes' => 'required|string',
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
        Gate::authorize('edit-courses');

        $lesson->delete();
        return back();
    }
}
