<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Team;
use Illuminate\Contracts\Database\Eloquent\Builder;
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
        'destroy',
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
            'capacity' => 'integer|min:1',
            'teams.*' => 'integer',
        ]);

        $course = Course::create($validated);
        $course->teams()->sync($validated['teams'] ?? []);
        return redirect(route('courses.index'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course): Response
    {
        Gate::authorize('edit-courses');

        return Inertia::render('Course/Edit', [
            'course' => $course->load([
                'teams:id',
                'lessons' => function (Builder $query) {
                    $query->orderBy('start', 'asc')
                        ->select('id', 'course_id', 'title', 'start', 'finish');
                },
            ]),
            'teams' => Team::all(['id', 'name']),
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
            'capacity' => 'integer|min:1',
            'teams.*' => 'integer',
        ]);


        $course->update($validated);
        $course->teams()->sync($validated['teams'] ?? []);
        return redirect(route('courses.index'));
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
