<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\IncomeGroup;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    protected $only = [
        'index',
        'create',
        'store',
        'edit',
        'update',
    ];

    protected function teams_not_selected(Course $course)
    {
        return Team::whereNotIn('id', function ($query) use ($course) {
            $query->select('team_id')->from('course_team')->where('course_id', '=', $course->id);
        })->get();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        Gate::authorize('edit-courses');

        return Inertia::render('Course/Index', [
            'courses' => Course::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        Gate::authorize('edit-courses');

        return Inertia::render('Course/Create', [
            'teams' => Team::all(['id', 'name']),
            /*'tarifGroups' => IncomeGroup::all(['id', 'name']),*/
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('edit-courses');

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
        $course->incomeGroups()->attach($fees);

        return redirect(route('courses.edit', $course));
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course): Response
    {
        Gate::authorize('edit-courses');

        return Inertia::render('Course/Edit', [
            'course' => $course->load(['teams:id', 'incomeGroups:id']),
            'teams' => Team::all(['id', 'name']),
            'tarifGroups' => IncomeGroup::all(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course): RedirectResponse
    {
        Gate::authorize('edit-courses');

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
        foreach ($participants as $participant) {
            $parts[$participant] = ["paid" => in_array($participant, $paid)];
        }

        $course->update($validated);
        foreach ($validated["fees"] as $key => $value) {
            $course->incomeGroups()->updateExistingPivot($key, ['fee' => $value]);
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
        Gate::authorize('edit-courses');

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
