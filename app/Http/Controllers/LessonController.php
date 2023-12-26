<?php

namespace App\Http\Controllers;

use App\Enums\LessonParticipationEnum;
use App\Enums\UserRoleEnum;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class LessonController extends Controller
{
    protected $only = [
        'create',
        'store',
        'edit',
        'update',
        'destroy',
    ];

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        Gate::authorize('edit-courses');

        return Inertia::render('Lesson/Create', [
            'course_id' => $request->course_id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('edit-courses');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required',
            'finish' => 'required',
            'notes' => 'string',
            'id' => 'exists:courses',
        ]);

        $course = Course::find($validated['id']);
        $course->lessons()->save(new Lesson($validated));
        return redirect(route('courses.edit', $course));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lesson $lesson): Response
    {
        Gate::authorize('edit-courses');

        $participants = $lesson->participants()
            ->wherePivot('participation', '<>', LessonParticipationEnum::TEACHER->value)
            ->get(['id', 'first_name', 'last_name', 'participation', 'message']);
        $teachers = $lesson->participants()
            ->wherePivot('participation', LessonParticipationEnum::TEACHER->value)
            ->get(['id', 'first_name', 'last_name', 'message']);

        return Inertia::render('Lesson/Edit', [
            'lesson' => $lesson,
            'participants' => $participants,
            'lessonteachers' => $teachers,
            'teachers' => User::where('role', UserRoleEnum::TEACHER->value)
                ->orWhere('role', UserRoleEnum::ADMIN->value)
                ->orderBy('first_name', 'asc')->get([
                    'id',
                    'first_name',
                    'last_name',
                ]),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lesson $lesson): RedirectResponse
    {
        Gate::authorize('edit-courses');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'finish' => 'required|date',
            'notes' => 'required|string',
        ]);

        $lesson->update($validated);
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

    public function signOut(Request $request, Lesson $lesson): RedirectResponse
    {
        Auth::user()->lessons()->updateExistingPivot($lesson->id, [
            'participation' => LessonParticipationEnum::SIGNED_OUT->value,
            'message' => $request->message,
        ]);
        return back();
    }

    public function signIn(Request $request, Lesson $lesson): RedirectResponse
    {
        Auth::user()->lessons()->updateExistingPivot($lesson->id, [
            'participation' => LessonParticipationEnum::SIGNED_IN->value,
            'message' => $request->message,
        ]);
        return back();
    }

    public function sendMessage(Request $request, Lesson $lesson): RedirectResponse
    {
        Auth::user()->lessons()->updateExistingPivot($lesson->id, [
            'message' => $request->message,
        ]);
        return back();
    }

    public function addTeacher(Request $request, Lesson $lesson): RedirectResponse
    {
        $teacher = User::find($request->teacher);
        if ($teacher->hasSignedInToLesson($lesson)) {
            $lesson->participants()->updateExistingPivot($teacher, [
                'participation' => LessonParticipationEnum::TEACHER->value,
            ]);
        } else {
            $lesson->participants()->attach($teacher, [
                'participation' => LessonParticipationEnum::TEACHER->value,
            ]);
        }
        return back();
    }

    public function removeTeacher(Request $request, Lesson $lesson): RedirectResponse
    {
        $teacher = User::find($request->teacher);
        if ($teacher->hasSignedUpToCourse($lesson->course)) {
            $lesson->participants()->updateExistingPivot($teacher, [
                'participation' => LessonParticipationEnum::SIGNED_OUT->value,
            ]);
        } else {
            $lesson->participants()->detach($teacher);
        }

        return back();
    }

    public function setLate(Request $request, Lesson $lesson): RedirectResponse
    {
        $participant = User::find($request->participant);
        $lesson->participants()
            ->updateExistingPivot($participant, ['participation' => LessonParticipationEnum::LATE->value]);
        return back();
    }

    public function setNoShow(Request $request, Lesson $lesson): RedirectResponse
    {
        $participant = User::find($request->participant);
        $lesson->participants()
            ->updateExistingPivot($participant, ['participation' => LessonParticipationEnum::NO_SHOW->value]);
        return back();
    }
}
