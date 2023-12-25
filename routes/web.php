<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::resource('users', UserController::class);

    Route::post('courses/{course}/signup', [CourseController::class, 'signUp'])->name('courses.signup');
    Route::post('courses/{course}/set-paid', [CourseController::class, 'setPaid'])->name('courses.setPaid');
    Route::post('courses/{course}/removeParticipant', [CourseController::class, 'removeParticipant'])->name('courses.removeParticipant');
    Route::post('courses/{course}/addParticipant', [CourseController::class, 'addParticipant'])->name('courses.addParticipant');
    Route::resource('courses', CourseController::class);

    Route::post('lessons/{lesson}/signout', [LessonController::class, 'signOut'])->name('lessons.signout');
    Route::post('lessons/{lesson}/signin', [LessonController::class, 'signIn'])->name('lessons.signin');
    Route::post('lessons/{lesson}/send-message', [LessonController::class, 'sendMessage'])->name('lessons.sendmessage');
    Route::resource('lessons', LessonController::class);
});
