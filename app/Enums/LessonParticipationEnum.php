<?php

namespace App\Enums;

enum LessonParticipationEnum: string
{
    /**
     * Leads the lesson
     */
    case TEACHER = 'teacher';
    /**
     * Signed in for the lesson manually
     */
    case SIGNED_IN = 'signed_in';
    /**
     * Signed out for the lesson manually
     */
    case SIGNED_OUT = 'signed_out';
    /**
     * Showed up late to the lesson
     */
    case LATE = 'late';
    /**
     * Did not show up to the lesson
     */
    case NO_SHOW = 'no_show';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
};
