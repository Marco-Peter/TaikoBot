<?php

namespace App\Http\Controllers;

use App\Enums\LessonParticipationEnum;
use App\Models\Course;
use App\Models\Lesson;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as DbBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Jetstream\Jetstream;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = Auth::user()->load([
            'lessons' => function (Builder $query) {
                $query->select('id', 'course_id', 'title', 'start')
                    ->where('start', '>', Carbon::now()->toDateString())
                    ->oldest('start');
            },
            'lessons.course:id,name',
            'lessons.teachers:id,first_name,last_name',
        ]);

        $studentLessons = $user->studentLessons()
            ->with([
                'course:id,name,capacity',
                'teachers:id,first_name,last_name',
            ])->withCount(['participants' => function (Builder $query) {
                $query->where('participation', LessonParticipationEnum::SIGNED_IN)
                    ->orWhere('participation', LessonParticipationEnum::LATE);
            }])
            ->where('start', '>', Carbon::now()->toDateString())
            ->oldest('start')->get();

        if ($user->karma !== 0) {
            $compensationLessons = Lesson::with([
                'course:id,name,capacity',
                'teachers:id,first_name,last_name',
            ])->withCount(['participants' => function (Builder $query) {
                $query->where('participation', LessonParticipationEnum::SIGNED_IN)
                    ->orWhere('participation', LessonParticipationEnum::LATE);
            }])
                ->whereNotIn('id', function (DbBuilder $query) use ($user) {
                    $query->select('lesson_id')->from('lesson_user')
                        ->where('user_id', $user->id);
                })
                ->whereIn('course_id', function (DbBuilder $query) use ($user) {
                    $query->select('compensation_id')->from('compensations')
                        ->whereIn('original_id', function (DbBuilder $query) use ($user) {
                            $query->select('course_id')->from('course_user')->where('user_id', '=', $user->id);
                        });
                })
                ->where('start', '>', Carbon::now()->toDateString())
                ->oldest('start')->limit(5)->get();
            $studentLessons = $studentLessons->merge($compensationLessons)->sortBy('start')->values();
        }

        $studentLessons->each(function (Lesson $lesson, int $key) {
            $lesson->start->inApplicationTz();
            $lesson->finish->inApplicationTz();
        });

        $teacherLessons = $user->teacherLessons()
            ->with([
                'course:id,name',
            ])->withCount(['participants' => function (Builder $query) {
                $query->where('participation', LessonParticipationEnum::SIGNED_IN)
                    ->orWhere('participation', LessonParticipationEnum::LATE);
            }])
            ->where('start', '>', Carbon::now()->subDays(3)->toDateString())
            ->oldest('start')->get();

        $teacherLessons->each(function (Lesson $lesson, int $key) {
            $lesson->start->inApplicationTz();
            $lesson->finish->inApplicationTz();
        });

        if ($user->team) {
            $coursesNotSignedUp = $user->team->courses->diff($user->courses)
                ->where('firstLesson.start', '>', Carbon::now()->toDateString())
                ->values()
                ->load([
                    'firstLesson:course_id,start',
                    'lastLesson:course_id,finish',
                ])->loadCount('participants');

            $coursesNotSignedUp->each(function (Course $course, int $key) {
                $course->firstLesson->start->inApplicationTz();
                $course->lastLesson->finish->inApplicationTz();
            });
            $greeting = '';
        } else {
            $coursesNotSignedUp = new Collection();
            $greeting = Str::markdown(file_get_contents(Jetstream::localizedMarkdownPath('dashboard_greeting.md')));
        }

        $coursesSignedUp = $user->courses
            ->where('lastLesson.finish', '>', Carbon::now()->toDateString())
            ->values()
            ->load([
                'firstLesson:course_id,start',
                'lastLesson:course_id,finish',
            ]);

        $coursesSignedUp->each(function (Course $course, int $key) {
            $course->firstLesson->start->inApplicationTz();
            $course->lastLesson->finish->inApplicationTz();
        });

        $coursesSignedUp->setHidden(['description', 'created_at', 'updated_at', 'pivot']);
        $coursesNotSignedUp->setHidden(['description', 'created_at', 'updated_at', 'pivot']);

        return Inertia::render('Dashboard', [
            'user' => $user->only('id', 'first_name', 'karma'),
            'studentLessons' => $studentLessons,
            'teacherLessons' => $teacherLessons,
            'coursesSignedUp' => $coursesSignedUp,
            'coursesNotSignedUp' => $coursesNotSignedUp,
            'dashboardGreeting' => $greeting,
            'pushServerPublicKey' => env('VAPID_PUBLIC_KEY'),
        ]);
    }
}
