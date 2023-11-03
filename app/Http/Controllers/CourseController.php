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
            'fees.*' => 'integer|min:0',
            'capacity' => 'integer|min:1',
        ]);

        $offset = array_search("fees", array_keys($validated), true);
        $fees = array_splice($validated, $offset, 1)["fees"];
        array_walk($fees, function (&$value) {
            $value = ["fee" => $value];
        });

        $course = Course::create($validated);
        $course->fees()->attach($fees);

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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'string',
            'fees.*' => 'integer|min:0',
            'capacity' => 'integer|min:1',
            'teams.*' => 'boolean',
            'participants.*' => 'boolean',
            'paid.*' => 'boolean',
        ]);

        $offset = array_search("teams", array_keys($validated), true);
        if ($offset) {
            $teams = array_keys(array_splice($validated, $offset, 1)["teams"]);
        } else {
            $teams = [];
        }

        $offset = array_search("participants", array_keys($validated), true);
        if ($offset) {
            $participants = array_keys(array_splice($validated, $offset, 1)["participants"]);
        } else {
            $participants = [];
        }

        $offset = array_search("paid", array_keys($validated), true);
        if ($offset) {
            $paid = array_keys(array_splice($validated, $offset, 1)["paid"]);
        } else {
            $paid = [];
        }

        $parts = array();
        foreach ($participants as $value) {
            $parts[$value] = ["paid" => in_array($value, $paid)];
        }

        $course->update($validated);
        foreach ($validated["fees"] as $key => $value) {
            $course->fees()->updateExistingPivot($key, ['fee' => $value]);
        }
        $course->teams()->sync($teams);
        $course->participants()->sync($parts);

        foreach ($course->lessons as $lesson) {
            $lesson->participants()->sync($participants);
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

    public function add_participant(Request $request): RedirectResponse
    {
        dd($request);
        return back();
    }

    public function remove_participant(Request $request): RedirectResponse
    {
        dd($request);
        return back();
    }
}
