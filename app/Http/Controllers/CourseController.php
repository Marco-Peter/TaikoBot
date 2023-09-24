<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Team;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    protected function teams_not_selected(Course $course)
    {
        return Team::whereNotIn('id', function ($query) use ($course) {
            $query->select('team_id')->from('course_team')->where('course_id', '=', $course->id);
        })->get();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('management.list-courses', [
            'courses' => Course::get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('management.create-course');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //$this->authorize('update', $course);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'string',
            'fee' => 'integer|min:0',
            'capacity' => 'integer|min:1',
        ]);
        $course = Course::create($validated);

        return redirect(route('courses.edit', $course));
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course): View
    {
        //$this->authorize('update', $course);

        return view('management.edit-course', [
            'course' => $course,
            'teams' => Team::all(),
            'teams_not_selected' => $this->teams_not_selected($course),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course): RedirectResponse
    {
        //$this->authorize('update', $course);

        if ($request["changed_item"]) {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'string',
                'fee' => 'integer|min:0',
                'capacity' => 'integer|min:1',
            ]);
            $course->update($validated);
        } elseif ($request["remove_team"]) {
            $course->teams()->detach($request["remove_team"]);
        } elseif ($request["autosign_off"]) {
            $team = $course->teams()->where('team_id', '=', $request["autosign_off"])->first();
            $team->pivot->signed_in = 0;
            $team->pivot->save();
        } elseif ($request["autosign_on"]) {
            $team = $course->teams()->where('team_id', '=', $request["autosign_on"])->first();
            $team->pivot->signed_in = 1;
            $team->pivot->save();
        } elseif ($request["add_team"]) {
            $course->teams()->attach($request["add_team"]);
        }

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course): RedirectResponse
    {
        //$this->authorize('update', $course);

        $course->delete();
        return redirect(route('courses.index'));
    }
}
