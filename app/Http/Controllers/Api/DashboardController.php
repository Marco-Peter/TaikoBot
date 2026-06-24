<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Http\Resources\LessonResource;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user()->load([
            'courses.firstLesson',
            'courses.lastLesson',
            'team.courses.firstLesson',
            'team.courses.lastLesson',
        ]);

        $data = $this->dashboardService->getData($user);

        return response()->json([
            'student_lessons'      => LessonResource::collection($data['studentLessons']),
            'teacher_lessons'      => LessonResource::collection($data['teacherLessons']),
            'courses_signed_up'    => CourseResource::collection($data['coursesSignedUp']),
            'courses_not_signed_up' => CourseResource::collection($data['coursesNotSignedUp']),
            'push_server_public_key' => env('VAPID_PUBLIC_KEY'),
        ]);
    }
}
