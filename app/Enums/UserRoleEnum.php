<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    /**
     * Not much to say, can do everything
     */
    case ADMIN = 'admin';
    /**
     * Can create/edit courses and lessons
     */
    case TEACHER = 'teacher';
    /**
     * Can edit personal data, sign in/out of lessons
     */
    case STUDENT = 'student';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
};
