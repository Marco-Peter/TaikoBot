<?php

namespace App\Http\Controllers\Api;

use App\Enums\LessonParticipationEnum;
use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\Team;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CourseController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('edit-courses');

        return CourseResource::collection(
            Course::withCount('participants')
                ->with(['firstLesson:id,course_id,start', 'lastLesson:id,course_id,finish'])
                ->get()
        );
    }

    public function store(Request $request): CourseResource
    {
        Gate::authorize('edit-courses');

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'string',
            'capacity'     => 'integer|min:1',
            'signout_limit' => 'integer|min:0',
            'teams.*'      => 'integer',
        ]);

        $course = Course::create($validated);
        $course->teams()->sync($validated['teams'] ?? []);

        return new CourseResource($course->load('teams'));
    }

    public function show(Course $course): CourseResource
    {
        $course->load([
            'teams:id,name',
            'lessons' => fn(Builder $q) => $q->orderBy('start')->select('id', 'course_id', 'title', 'start', 'finish'),
            'lessons.teachers' => fn(Builder $q) => $q->select('id', 'first_name', 'last_name'),
            'compensations:id,name',
        ])->loadCount('participants');

        $signedIn = $course->participants->contains(Auth::user());
        if ($signedIn || Gate::check('edit-courses')) {
            $course->load('material');
        }

        return new CourseResource($course);
    }

    public function update(Request $request, Course $course): CourseResource
    {
        Gate::authorize('edit-courses');

        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'description'     => 'string',
            'capacity'        => 'integer|min:1',
            'signout_limit'   => 'integer|min:0',
            'teacher_payment' => 'integer|min:0',
            'assist_payment'  => 'integer|min:0',
            'teams.*'         => 'integer',
        ]);

        $course->update($validated);
        $course->teams()->sync($validated['teams'] ?? []);

        return new CourseResource($course->fresh()->load('teams'));
    }

    public function destroy(Course $course): JsonResponse
    {
        Gate::authorize('edit-courses');
        $course->delete();
        return response()->json(null, 204);
    }

    public function signUp(Course $course): JsonResponse
    {
        $user = Auth::user();
        $user->courses()->syncWithoutDetaching($course->id);
        $user->lessons()->syncWithoutDetaching($course->lessons);
        return response()->json(['message' => 'Signed up successfully']);
    }

    public function addParticipant(Request $request, Course $course): JsonResponse
    {
        Gate::authorize('edit-courses');
        $user = User::findOrFail($request->input('user_id'));
        $user->courses()->syncWithoutDetaching($course->id);
        $user->lessons()->syncWithoutDetaching($course->lessons);
        return response()->json(['message' => 'Participant added']);
    }

    public function removeParticipant(Course $course, User $user): JsonResponse
    {
        Gate::authorize('edit-courses');
        $user->lessons()->wherePivot('participation', '<>', LessonParticipationEnum::TEACHER->value)
            ->detach($course->lessons);
        $user->courses()->detach($course->id);
        return response()->json(null, 204);
    }

    public function setPaid(Request $request, Course $course, User $user): JsonResponse
    {
        Gate::authorize('edit-courses');
        $validated = $request->validate(['paid' => 'required|boolean']);
        $course->participants()->updateExistingPivot($user, ['paid' => $validated['paid']]);
        return response()->json(['message' => 'Payment status updated']);
    }

    public function uploadMaterial(Request $request, Course $course): JsonResponse
    {
        Gate::authorize('edit-courses');

        $validated = $request->validate(['external' => 'required|boolean']);

        if ($validated['external']) {
            $validated = $request->validate([
                'notes'    => 'string|nullable',
                'external' => 'required|boolean',
                'path'     => 'required|string',
                'name'     => 'required|string',
            ]);
        } else {
            $validated = $request->validate([
                'notes'    => 'string|nullable',
                'external' => 'required|boolean',
                'path'     => 'required|file',
            ]);
            $validated['path'] = $request->file('path')->store('coursematerial', 'public');
            $validated['name'] = $request->file('path')->getClientOriginalName();
        }

        $material = $course->material()->create($validated);
        return response()->json(['id' => $material->id, 'name' => $material->name], 201);
    }

    public function deleteMaterial(Course $course, CourseMaterial $material): JsonResponse
    {
        Gate::authorize('edit-courses');

        if (!$material->external) {
            Storage::disk('public')->delete($material->path);
        }
        $material->delete();
        return response()->json(null, 204);
    }

    public function downloadMaterial(Course $course, CourseMaterial $material): BinaryFileResponse
    {
        Gate::allowIf(
            Auth::user()->courses()->where('id', $course->id)->exists() ||
                Gate::check('edit-courses')
        );

        $path = Storage::disk('public')->path($material->path);
        return response()->download($path, $material->name);
    }

    public function addCompensationCourse(Request $request, Course $course): JsonResponse
    {
        Gate::authorize('edit-courses');
        $comp = Course::findOrFail($request->input('compensation_id'));
        $course->compensations()->attach($comp);
        return response()->json(['message' => 'Compensation course added']);
    }

    public function removeCompensationCourse(Course $course, Course $compensation): JsonResponse
    {
        Gate::authorize('edit-courses');
        $course->compensations()->detach($compensation);
        return response()->json(null, 204);
    }
}
