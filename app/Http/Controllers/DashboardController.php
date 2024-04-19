<?php

namespace App\Http\Controllers;

use App\Enums\LessonParticipationEnum;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
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
                'course:id,name',
                'teachers:id,first_name,last_name',
            ])->withCount(['participants' => function (Builder $query) {
                $query->where('participation', LessonParticipationEnum::SIGNED_IN)
                    ->orWhere('participation', LessonParticipationEnum::LATE);
            }])
            ->where('start', '>', Carbon::now()->toDateString())
            ->oldest('start')->get();

        $teacherLessons = $user->teacherLessons()
            ->with([
                'course:id,name',
            ])->withCount(['participants' => function (Builder $query) {
                $query->where('participation', LessonParticipationEnum::SIGNED_IN)
                    ->orWhere('participation', LessonParticipationEnum::LATE);
            }])
            ->where('start', '>', Carbon::now()->subDays(3)->toDateString())
            ->oldest('start')->get();

        if ($user->team) {
            $coursesNotSignedUp = $user->team->courses->diff($user->courses)
                ->where('firstLesson.start', '>', Carbon::now()->toDateString())
                ->values()
                ->load([
                    'firstLesson:course_id,start',
                    'lastLesson:course_id,finish',
                ])->loadCount('participants');
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

        $coursesSignedUp->setHidden(['description', 'created_at', 'updated_at', 'pivot']);
        $coursesNotSignedUp->setHidden(['description', 'created_at', 'updated_at', 'pivot']);

        return Inertia::render('Dashboard', [
            'user' => $user->only('id', 'first_name'),
            'studentLessons' => $studentLessons,
            'teacherLessons' => $teacherLessons,
            'coursesSignedUp' => $coursesSignedUp,
            'coursesNotSignedUp' => $coursesNotSignedUp,
            'dashboardGreeting' => $greeting,
            'pushServerPublicKey' => env('VAPID_PUBLIC_KEY'),
        ]);
    }
}
