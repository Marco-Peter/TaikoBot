<?php

namespace App\Http\Controllers;

use App\Enums\LessonParticipationEnum;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\Lesson;
use App\Models\Team;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CourseController extends Controller
{
    protected $only = [
        'index',
        'create',
        'store',
        'show',
        'edit',
        'update',
        'destroy',
    ];

    protected function teams_not_selected(Course $course)
    {
        return Team::whereNotIn('id', function ($query) use ($course) {
            $query->select('team_id')->from('course_team')
                ->where('course_id', '=', $course->id);
        })->get();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        Gate::authorize('edit-courses');

        return Inertia::render('Course/Index', [
            'courses' => Course::withCount('participants')->get(),
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
            'signout_limit' => 'integer|min:0',
            'teams.*' => 'integer',
        ]);

        $course = Course::create($validated);
        $course->teams()->sync($validated['teams'] ?? []);

        return redirect(route('courses.edit', $course));
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course): Response
    {
        $course->description = Str::markdown($course->description);
        $course->load([
            'teams:id',
            'lessons' => function (Builder $query) {
                $query->orderBy('start', 'asc')
                    ->select('id', 'course_id', 'title', 'start', 'finish');
            },
        ])->loadCount('participants');

        $course->lessons->each(function (Lesson $lesson, int $key) {
            $lesson->start->inApplicationTz();
            $lesson->finish->inApplicationTz();
        });

        $signedIn = $course->participants->contains(Auth::user());
        if ($signedIn) {
            $course->load([
                'material:id,course_id,path,name,external,notes',
            ]);
        }

        return Inertia::render('Course/Show', [
            'course' => $course,
            'signedIn' => $signedIn,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course): Response
    {
        Gate::authorize('edit-courses');

        $course->load([
            'teams:id',
            'lessons' => function (Builder $query) {
                $query->orderBy('start', 'asc')
                    ->select('id', 'course_id', 'title', 'start', 'finish');
            },
            'lessons.teachers:id,first_name,last_name',
            'participants:id,first_name,last_name',
            'material:id,course_id,path,name,external,notes',
        ]);

        $teams = Team::with([
            'users:id,first_name,last_name,team_id',
        ])->get(['id', 'name']);

        $compCourses = Course::all([
            'id',
            'name'
        ]);

        $compCoursesSelected = $course->compensations;

        return Inertia::render('Course/Edit', [
            'course' => $course,
            'teams' => $teams,
            'compCourses' => $compCourses,
            'compCoursesSelected' => $compCoursesSelected,
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
            'signout_limit' => 'integer|min:0',
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

    public function uploadMaterial(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'external' => "required|boolean",
        ]);

        if ($validated['external'] === true) {
            $validated = $request->validate([
                'notes' => "string|nullable",
                'external' => "required|boolean",
                'path' => "required|string",
                'name' => "required|string",
            ]);
        } else {
            $validated = $request->validate([
                'notes' => "string|nullable",
                'external' => "required|boolean",
                'path' => "required|file",
            ]);
        }

        if ($validated["external"] !== true) {
            $validated['path'] = $request->file('path')->store('coursematerial', 'public');
            $validated['name'] = $request->file('path')->getClientOriginalName();
        }

        $course->material()->create($validated);

        return back();
    }

    public function downloadMaterial(CourseMaterial $courseMaterial): BinaryFileResponse
    {
        Gate::allowIf(
            Auth::user()->courses()
                ->where('id', $courseMaterial->course->id)->exists() ||
                Gate::check('edit-courses')
        );

        $path = Storage::disk('public')->path($courseMaterial->path);

        return response()->download($path, $courseMaterial->name);
    }

    public function deleteMaterial(Request $request, Course $course): RedirectResponse
    {
        Gate::authorize('edit-courses');

        $validated = $request->validate([
            'material' => "required|integer",
        ]);

        $material = $course->material()->where('id', $validated['material'])->first();
        if (!$material->external) {
            Storage::disk('public')->delete($material->path);
        }
        $material->delete();

        return back();
    }

    public function addParticipant(Request $request, Course $course): RedirectResponse
    {
        $user = User::find($request->user);
        $user->courses()->syncWithoutDetaching($course->id);
        $user->lessons()->syncWithoutDetaching($course->lessons);

        return back();
    }

    public function removeParticipant(Request $request, Course $course): RedirectResponse
    {
        $user = User::find($request->user);
        $user->lessons()->wherePivot(
            'participation',
            '<>',
            LessonParticipationEnum::TEACHER->value
        )->detach($course->lessons);
        $user->courses()->detach($course->id);

        return back();
    }

    public function addCompensationCourse(Request $request, Course $course): RedirectResponse
    {
        $compCourse = Course::find($request->compCourse);
        $course->compensations()->attach($compCourse);
        return back();
    }

    public function removeCompensationCourse(Request $request, Course $course): RedirectResponse
    {
        $compCourse = Course::find($request->compCourse);
        $course->compensations()->detach($compCourse);
        return back();
    }

    public function signUp(Course $course): RedirectResponse
    {
        $user = Auth::user();
        $user->courses()->syncWithoutDetaching($course->id);
        $user->lessons()->syncWithoutDetaching($course->lessons);

        return redirect(route('dashboard'))->with('message', 'Signed up successfully');
    }

    public function setPaid(Request $request, Course $course): RedirectResponse
    {
        $user = User::find($request->user);
        $course->participants()->updateExistingPivot($user, [
            'paid' => $request->paid,
        ]);

        return back();
    }
}
