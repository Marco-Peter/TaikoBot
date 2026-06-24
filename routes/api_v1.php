<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\LessonController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {

    // Auth
    Route::get('auth/user', [AuthController::class, 'user']);
    Route::post('auth/logout', [AuthController::class, 'logout']);

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Courses
    Route::apiResource('courses', CourseController::class);
    Route::post('courses/{course}/signup', [CourseController::class, 'signUp']);
    Route::post('courses/{course}/participants', [CourseController::class, 'addParticipant']);
    Route::delete('courses/{course}/participants/{user}', [CourseController::class, 'removeParticipant']);
    Route::put('courses/{course}/participants/{user}/paid', [CourseController::class, 'setPaid']);
    Route::post('courses/{course}/materials', [CourseController::class, 'uploadMaterial']);
    Route::delete('courses/{course}/materials/{material}', [CourseController::class, 'deleteMaterial']);
    Route::get('courses/{course}/materials/{material}/download', [CourseController::class, 'downloadMaterial']);
    Route::post('courses/{course}/compensations', [CourseController::class, 'addCompensationCourse']);
    Route::delete('courses/{course}/compensations/{compensation}', [CourseController::class, 'removeCompensationCourse']);

    // Lessons
    Route::apiResource('lessons', LessonController::class)->except(['index']);
    Route::post('lessons/{lesson}/signin', [LessonController::class, 'signIn']);
    Route::post('lessons/{lesson}/signout', [LessonController::class, 'signOut']);
    Route::post('lessons/{lesson}/compensate', [LessonController::class, 'compensate']);
    Route::post('lessons/{lesson}/assist', [LessonController::class, 'assist']);
    Route::post('lessons/{lesson}/message', [LessonController::class, 'sendMessage']);
    Route::post('lessons/{lesson}/teachers', [LessonController::class, 'addTeacher']);
    Route::put('lessons/{lesson}/teachers', [LessonController::class, 'setTeacher']);
    Route::delete('lessons/{lesson}/teachers/{user}', [LessonController::class, 'removeTeacher']);
    Route::post('lessons/{lesson}/participants', [LessonController::class, 'addParticipant']);
    Route::put('lessons/{lesson}/participants/{user}/attendance', [LessonController::class, 'setAttendance']);

    // Users
    Route::apiResource('users', UserController::class);
    Route::post('users/{user}/payment', [UserController::class, 'updatePayment']);
    Route::put('user/settings', [UserController::class, 'updateSettings']);
    Route::post('users/{user}/push-subscription', [UserController::class, 'updatePushSubscription']);
    Route::delete('users/{user}/push-subscription', [UserController::class, 'deletePushSubscription']);
});
