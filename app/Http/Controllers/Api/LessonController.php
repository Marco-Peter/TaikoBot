<?php

namespace App\Http\Controllers\Api;

use App\Enums\LessonParticipationEnum;
use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\LessonResource;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Services\LessonService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class LessonController extends Controller
{
    public function __construct(private LessonService $lessonService)
    {
    }

    public function show(Lesson $lesson): LessonResource
    {
        $lesson->load([
            'participants' => fn(Builder $q) => $q->orderBy('first_name')->orderBy('last_name'),
            'teachers'     => fn(Builder $q) => $q->orderBy('first_name')->orderBy('last_name'),
            'course:id,name,capacity,signout_limit',
        ]);

        return new LessonResource($lesson);
    }

    public function store(Request $request): LessonResource
    {
        Gate::authorize('edit-courses');

        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'start'     => 'required',
            'finish'    => 'required',
            'notes'     => 'string|nullable',
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::find($validated['course_id']);
        $lesson = new Lesson();
        $lesson->title = $validated['title'];
        $lesson->start = Carbon::parse($validated['start'], config('app.timezone_default'))->setTimezone('UTC');
        $lesson->finish = Carbon::parse($validated['finish'], config('app.timezone_default'))->setTimezone('UTC');
        $lesson->notes = $validated['notes'] ?? '';
        $course->lessons()->save($lesson);
        $lesson->participants()->attach($course->participants);

        return new LessonResource($lesson);
    }

    public function update(Request $request, Lesson $lesson): LessonResource
    {
        Gate::authorize('edit-courses');

        $validated = $request->validate([
            'title'  => 'required|string|max:255',
            'start'  => 'required|date',
            'finish' => 'required|date',
            'notes'  => 'string|nullable',
        ]);

        $lesson->title = $validated['title'];
        $lesson->start = Carbon::parse($validated['start'], config('app.timezone_default'))->setTimezone('UTC');
        $lesson->finish = Carbon::parse($validated['finish'], config('app.timezone_default'))->setTimezone('UTC');
        $lesson->notes = $validated['notes'] ?? '';
        $lesson->save();

        foreach ($lesson->participants as $participant) {
            $participant->pivot->setReminder();
        }

        return new LessonResource($lesson);
    }

    public function destroy(Lesson $lesson): JsonResponse
    {
        Gate::authorize('edit-courses');
        $lesson->delete();
        return response()->json(null, 204);
    }

    public function signIn(Request $request, Lesson $lesson): JsonResponse
    {
        $participation = $this->lessonService->signIn(Auth::user(), $lesson, $request->input('message'));
        return response()->json(['participation' => $participation->value]);
    }

    public function signOut(Request $request, Lesson $lesson): JsonResponse
    {
        $this->lessonService->signOut(Auth::user(), $lesson, $request->input('message'));
        return response()->json(['participation' => LessonParticipationEnum::SIGNED_OUT->value]);
    }

    public function compensate(Request $request, Lesson $lesson): JsonResponse
    {
        $participation = $this->lessonService->compensate(Auth::user(), $lesson, $request->input('message'));
        return response()->json(['participation' => $participation->value]);
    }

    public function assist(Request $request, Lesson $lesson): JsonResponse
    {
        Gate::authorize('assist-lessons');
        $this->lessonService->assist(Auth::user(), $lesson, $request->input('message'));
        return response()->json(['participation' => LessonParticipationEnum::ASSISTANCE->value]);
    }

    public function sendMessage(Request $request, Lesson $lesson): JsonResponse
    {
        Auth::user()->lessons()->updateExistingPivot($lesson->id, [
            'message' => $request->input('message'),
        ]);
        return response()->json(['message' => 'Message updated']);
    }

    public function addTeacher(Request $request, Lesson $lesson): JsonResponse
    {
        Gate::authorize('edit-courses');
        $teacher = User::findOrFail($request->input('user_id'));
        $this->lessonService->addTeacher($teacher, $lesson);
        return response()->json(['message' => 'Teacher added']);
    }

    public function setTeacher(Request $request, Lesson $lesson): JsonResponse
    {
        Gate::authorize('edit-courses');
        $teacher = User::findOrFail($request->input('user_id'));
        $this->lessonService->setTeacher($teacher, $lesson);
        return response()->json(['message' => 'Teacher set']);
    }

    public function removeTeacher(Lesson $lesson, User $user): JsonResponse
    {
        Gate::authorize('edit-courses');
        $this->lessonService->removeTeacher($user, $lesson);
        return response()->json(null, 204);
    }

    public function addParticipant(Request $request, Lesson $lesson): JsonResponse
    {
        Gate::authorize('edit-courses');
        $user = User::findOrFail($request->input('user_id'));
        $lesson->participants()->attach($user, ['participation' => LessonParticipationEnum::SIGNED_IN]);
        return response()->json(['message' => 'Participant added']);
    }

    public function setAttendance(Request $request, Lesson $lesson, User $user): JsonResponse
    {
        Gate::authorize('edit-courses');

        $validated = $request->validate([
            'attendance' => 'required|in:signed_in,signed_out,late,no_show',
            'message'    => 'string|nullable',
        ]);

        $state = LessonParticipationEnum::from($validated['attendance']);
        $this->lessonService->setAttendance($user, $lesson, $state, $validated['message'] ?? null);

        return response()->json(['participation' => $state->value]);
    }
}
