<?php

namespace App\Services;

use App\Enums\LessonParticipationEnum;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as DbBuilder;
use Illuminate\Database\Eloquent\Collection;

class DashboardService
{
    public function getData(User $user): array
    {
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
                ->oldest('start')->get();

            $studentLessons = $studentLessons->merge($compensationLessons)->sortBy('start')->values();
        }

        $teacherLessons = $user->teacherLessons()
            ->with(['course:id,name'])
            ->withCount(['participants' => function (Builder $query) {
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
        } else {
            $coursesNotSignedUp = new Collection();
        }

        $coursesSignedUp = $user->courses
            ->where('lastLesson.finish', '>', Carbon::now()->toDateString())
            ->values()
            ->load([
                'firstLesson:course_id,start',
                'lastLesson:course_id,finish',
            ]);

        return compact('studentLessons', 'teacherLessons', 'coursesSignedUp', 'coursesNotSignedUp');
    }
}
