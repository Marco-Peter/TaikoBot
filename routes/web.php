<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\MessageChannelController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Jetstream\Jetstream;

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
        'welcometext' => Str::markdown(file_get_contents(Jetstream::localizedMarkdownPath('welcome.md'))),
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
    Route::post('lessons/{lesson}/send-message', [LessonController::class, 'sendMessage'])->name('lessons.sendMessage');
    Route::post('lessons/{lesson}/add-teacher', [LessonController::class, 'addTeacher'])->name('lessons.addTeacher');
    Route::post('lessons/{lesson}/remove-teacher', [LessonController::class, 'removeTeacher'])->name('lessons.removeTeacher');
    Route::post('lessons/{lesson}/set-late', [LessonController::class, 'setLate'])->name('lessons.setLate');
    Route::post('lessons/{lesson}/set-noshow', [LessonController::class, 'setNoShow'])->name('lessons.setNoShow');
    Route::resource('lessons', LessonController::class);

    Route::resource('channels', MessageChannelController::class);
    Route::resource('channels.messages', MessageController::class)->shallow();
});
