<?php

namespace App\Http\Controllers;

use App\Enums\LessonParticipationEnum;
use App\Enums\UserRoleEnum;
use App\Models\Lesson;
use App\Models\User;
use App\Services\LessonService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Inertia\Response;

class LessonController extends Controller
{
    public function __construct(private LessonService $lessonService)
    {
    }

    protected $only = [
        'create',
        'store',
        'show',
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
        $lesson = new Lesson;
        $lesson->title = $validated['title'];
        $lesson->start = Carbon::parse($validated['start'], config('app.timezone_default'))->setTimezone('UTC');
        $lesson->finish = Carbon::parse($validated['finish'], config('app.timezone_default'))->setTimezone('UTC');
        $lesson->notes = $validated['notes'];
        $course->lessons()->save($lesson);

        $lesson->participants()->attach($course->participants);

        return redirect(route('lessons.edit', $lesson));
    }

    /**
     * Show the specified resource.
     */
    public function show(Lesson $lesson): Response
    {
        $lessondata = [
            'id' => $lesson->id,
            'course_id' => $lesson->course_id,
            'title' => $lesson->title,
            'start' => $lesson->start->startOfMinute()->inApplicationTz()->toDateTimeLocalString(),
            'finish' => $lesson->finish->startOfMinute()->inApplicationTz()->toDateTimeLocalString(),
        ];

        if (Gate::check('edit-courses')) {
            $lessondata['notes'] = $lesson->notes;
        }

        $participants = $lesson->participants()
            ->wherePivotIn('participation', [
                LessonParticipationEnum::SIGNED_IN,
                LessonParticipationEnum::LATE,
                LessonParticipationEnum::ASSISTANCE,
            ])
            ->orderBy('first_name', 'asc')
            ->orderBy('last_name', 'asc')
            ->get(['id', 'profile_photo_path', 'first_name', 'last_name', 'participation', 'message']);
        $teachers = $lesson->participants()
            ->wherePivot('participation', LessonParticipationEnum::TEACHER)
            ->orderBy('first_name', 'asc')
            ->orderBy('last_name', 'asc')
            ->get(['id', 'profile_photo_path', 'first_name', 'last_name', 'message']);

        return Inertia::render('Lesson/Show', [
            'lesson' => $lessondata,
            'participants' => $participants,
            'lessonteachers' => $teachers,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lesson $lesson): Response
    {
        Gate::authorize('edit-courses');

        $participants = $lesson->participants()
            ->wherePivot('participation', '<>', LessonParticipationEnum::TEACHER)
            ->get(['id', 'first_name', 'last_name', 'participation', 'message']);
        $lessonTeachers = $lesson->participants()
            ->wherePivot('participation', LessonParticipationEnum::TEACHER)
            ->get(['id', 'first_name', 'last_name', 'message']);

        $users = User::whereDoesntHave('lessons', function (Builder $query) use ($lesson) {
            $query->where('id', '=', $lesson->id);
        })->orderBy('first_name')->get(['id', 'first_name', 'last_name']);

        return Inertia::render('Lesson/Edit', [
            'lesson' => [
                'id' => $lesson->id,
                'course_id' => $lesson->course_id,
                'title' => $lesson->title,
                'start' => $lesson->start->startOfMinute()->inApplicationTz()->toDateTimeLocalString(),
                'finish' => $lesson->finish->startOfMinute()->inApplicationTz()->toDateTimeLocalString(),
                'notes' => $lesson->notes,
            ],
            'participants' => $participants,
            'lessonteachers' => $lessonTeachers,
            'teachers' => User::where('role', UserRoleEnum::TEACHER)
                ->orWhere('role', UserRoleEnum::ADMIN)
                ->orderBy('first_name', 'asc')->get([
                    'id',
                    'first_name',
                    'last_name',
                ]),
            'users' => $users,
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
            'notes' => 'string',
        ]);

        $lesson->title = $validated['title'];
        $lesson->start = Carbon::parse($validated['start'], config('app.timezone_default'))->setTimezone('UTC');
        $lesson->finish = Carbon::parse($validated['finish'], config('app.timezone_default'))->setTimezone('UTC');
        $lesson->notes = $validated['notes'];
        $lesson->save();

        foreach ($lesson->participants as $participant) {
            $participant->pivot->setReminder();
        }

        return redirect(route('courses.edit', $lesson->course));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lesson $lesson): RedirectResponse
    {
        Gate::authorize('edit-courses');

        $lesson->delete();

        return back();
    }

    public function signOut(Request $request, Lesson $lesson): RedirectResponse
    {
        $this->lessonService->signOut(Auth::user(), $lesson, $request->message);
        return back();
    }

    public function signIn(Request $request, Lesson $lesson): RedirectResponse
    {
        $this->lessonService->signIn(Auth::user(), $lesson, $request->message);
        return back();
    }

    public function compensate(Request $request, Lesson $lesson): RedirectResponse
    {
        $this->lessonService->compensate(Auth::user(), $lesson, $request->message);
        return back();
    }

    public function assist(Request $request, Lesson $lesson): RedirectResponse
    {
        Gate::authorize('assist-lessons');
        $this->lessonService->assist(Auth::user(), $lesson, $request->message);
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
        Gate::authorize('edit-courses');
        $this->lessonService->addTeacher(User::find($request->teacher), $lesson);
        return back();
    }

    public function setTeacher(Request $request, Lesson $lesson): RedirectResponse
    {
        Gate::authorize('edit-courses');
        $this->lessonService->setTeacher(User::find($request->teacher), $lesson);
        return back();
    }

    public function removeTeacher(Request $request, Lesson $lesson): RedirectResponse
    {
        Gate::authorize('edit-courses');
        $this->lessonService->removeTeacher(User::find($request->teacher), $lesson);
        return back();
    }

    public function addParticipant(Request $request, Lesson $lesson): RedirectResponse
    {
        Gate::authorize('edit-courses');

        $user = User::find($request->user);
        $lesson->participants()->attach($user, [
            'participation' => LessonParticipationEnum::SIGNED_IN,
        ]);
        return back();
    }

    public function setSignedIn(Request $request, Lesson $lesson): RedirectResponse
    {
        Gate::authorize('edit-courses');
        $this->lessonService->setAttendance(User::find($request->participant), $lesson, LessonParticipationEnum::SIGNED_IN, null);
        return back();
    }

    public function setExcused(Request $request, Lesson $lesson): RedirectResponse
    {
        Gate::authorize('edit-courses');
        $this->lessonService->signOut(User::find($request->participant), $lesson, $request->message);
        return back();
    }

    public function setLate(Request $request, Lesson $lesson): RedirectResponse
    {
        Gate::authorize('edit-courses');
        $this->lessonService->setAttendance(User::find($request->participant), $lesson, LessonParticipationEnum::LATE, null);
        return back();
    }

    public function setNoShow(Request $request, Lesson $lesson): RedirectResponse
    {
        Gate::authorize('edit-courses');
        $this->lessonService->setAttendance(User::find($request->participant), $lesson, LessonParticipationEnum::NO_SHOW, null);
        return back();
    }
}
