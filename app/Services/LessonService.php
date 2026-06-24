<?php

namespace App\Services;

use App\Enums\LessonParticipationEnum;
use App\Models\Lesson;
use App\Models\User;
use App\Notifications\LessonConfirmed;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LessonService
{
    public function signIn(User $user, Lesson $lesson, ?string $message): LessonParticipationEnum
    {
        return DB::transaction(function () use ($user, $lesson, $message) {
            $course = $lesson->course()->lockForUpdate()->first();
            $activeCount = $lesson->students()
                ->wherePivotNotIn('participation', [LessonParticipationEnum::SIGNED_OUT])
                ->count();

            $participation = ($user->karma !== 0 && $course->capacity - $activeCount > 0)
                ? LessonParticipationEnum::SIGNED_IN
                : LessonParticipationEnum::WAITLIST;

            $user->lessons()->updateExistingPivot($lesson->id, [
                'participation' => $participation,
                'message' => $message,
            ]);

            if ($user->karma !== null) {
                $user->karma--;
                $user->save();
            }

            return $participation;
        });
    }

    public function compensate(User $user, Lesson $lesson, ?string $message): LessonParticipationEnum
    {
        return DB::transaction(function () use ($user, $lesson, $message) {
            $course = $lesson->course()->lockForUpdate()->first();
            $activeCount = $lesson->students()
                ->wherePivotNotIn('participation', [LessonParticipationEnum::SIGNED_OUT])
                ->count();

            $participation = ($user->karma !== 0 && $course->capacity - $activeCount > 0)
                ? LessonParticipationEnum::SIGNED_IN
                : LessonParticipationEnum::WAITLIST;

            $user->lessons()->attach($lesson, [
                'message' => $message,
                'participation' => $participation,
            ]);

            if ($user->karma !== null) {
                $user->karma--;
                $user->save();
            }

            return $participation;
        });
    }

    public function signOut(User $user, Lesson $lesson, ?string $message): void
    {
        if (
            $user->karma !== null &&
            ($user->lessons()->where('id', $lesson->id)->first()->pivot->participation === LessonParticipationEnum::WAITLIST ||
                $lesson->start > Carbon::now()->addHours($lesson->course->signout_limit))
        ) {
            $user->karma++;
            $user->save();
        }

        $updateData = ['participation' => LessonParticipationEnum::SIGNED_OUT];

        if ($message !== null) {
            $updateData['message'] = $message;
        }

        $user->lessons()->updateExistingPivot($lesson->id, $updateData);
        $this->promoteWaitlist($lesson);
    }

    public function assist(User $user, Lesson $lesson, ?string $message): void
    {
        if ($user->hasSignedInToLesson($lesson)) {
            $lesson->participants()->updateExistingPivot($user, [
                'participation' => LessonParticipationEnum::ASSISTANCE,
                'message' => $message,
            ]);
        } else {
            $lesson->participants()->attach($user, [
                'participation' => LessonParticipationEnum::ASSISTANCE,
                'message' => $message,
            ]);
        }
    }

    public function addTeacher(User $teacher, Lesson $lesson): void
    {
        if ($teacher->hasSignedInToLesson($lesson)) {
            $lesson->participants()->updateExistingPivot($teacher, [
                'participation' => LessonParticipationEnum::TEACHER,
            ]);
        } else {
            $lesson->participants()->attach($teacher, [
                'participation' => LessonParticipationEnum::TEACHER,
            ]);
        }
    }

    public function setTeacher(User $teacher, Lesson $lesson): void
    {
        $currentTeachers = $lesson->participants()
            ->wherePivot('participation', LessonParticipationEnum::TEACHER)
            ->get();

        foreach ($currentTeachers as $currentTeacher) {
            if ($currentTeacher->hasSignedUpToCourse($lesson->course)) {
                $lesson->participants()->updateExistingPivot($currentTeacher, [
                    'participation' => LessonParticipationEnum::SIGNED_OUT,
                ]);
            } else {
                $lesson->participants()->detach($currentTeacher);
            }
        }

        $this->addTeacher($teacher, $lesson);
    }

    public function removeTeacher(User $teacher, Lesson $lesson): void
    {
        if ($teacher->hasSignedUpToCourse($lesson->course)) {
            $lesson->participants()->updateExistingPivot($teacher, [
                'participation' => LessonParticipationEnum::SIGNED_OUT,
            ]);
        } else {
            $lesson->participants()->detach($teacher);
        }
    }

    public function setAttendance(User $participant, Lesson $lesson, LessonParticipationEnum $state, ?string $message): void
    {
        if ($state === LessonParticipationEnum::SIGNED_OUT) {
            $this->signOut($participant, $lesson, $message);
            return;
        }

        $lesson->participants()->updateExistingPivot($participant, ['participation' => $state]);
    }

    public function promoteWaitlist(Lesson $lesson): void
    {
        $nextInLine = $lesson->participants()
            ->wherePivot('participation', LessonParticipationEnum::WAITLIST)
            ->orderByPivot('created_at', 'asc')
            ->first();

        if ($nextInLine) {
            $lesson->participants()->updateExistingPivot($nextInLine->id, [
                'participation' => LessonParticipationEnum::SIGNED_IN,
            ]);
            $nextInLine->notify(new LessonConfirmed($lesson));
        }
    }
}
