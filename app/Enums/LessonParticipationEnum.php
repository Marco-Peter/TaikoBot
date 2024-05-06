<?php

namespace App\Enums;

enum LessonParticipationEnum: string
{
    /**
     * Signed out for the lesson manually
     */
    case SIGNED_OUT = 'signed_out';
    /**
     * Signed in for the lesson manually
     */
    case SIGNED_IN = 'signed_in';
    /**
     * Leads the lesson
     */
    case TEACHER = 'teacher';
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
