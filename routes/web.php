<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserSettingsController;
use Illuminate\Support\Facades\Artisan;
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
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'welcometext' => Str::markdown(file_get_contents(Jetstream::localizedMarkdownPath('welcome.md'))),
    ]);
});

Route::get('/scheduler', function () {
    $rc = Artisan::call('schedule:run');
    $res = $rc == 0 ? "Success" : "Failed";
    return response("Calling Scheduler: $res");
});

Route::get('/remindUsers', function () {
    $rc = Artisan::call('app:remind-users');
    $res = $rc == 0 ? "Success" : "Failed";
    return response("Reminding users: $res");
});

Route::get('/cancelWaitlist', function () {
    $rc = Artisan::call('app:cancel-waitlist');
    $res = $rc == 0 ? "Success" : "Failed";
    return response("Clearing waitlists: $res");
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::post('users/do-migrations', [UserController::class, 'doMigrations'])->name('users.doMigrations');
    Route::post('users/{user}/updatePushSubscription', [UserController::class, 'updatePushSubscription'])->name('users.updatePushSubscription');
    Route::post('users/{user}/deletePushSubscription', [UserController::class, 'deletePushSubscription'])->name('users.deletePushSubscription');
    Route::post('users/{user}/updatePayment', [UserController::class, 'updatePayment'])->name('users.updatePayment');
    Route::resource('users', UserController::class);

    Route::post('courses/{course}/signup', [CourseController::class, 'signUp'])->name('courses.signup');
    Route::post('courses/{course}/set-paid', [CourseController::class, 'setPaid'])->name('courses.setPaid');
    Route::post('courses/{course}/removeParticipant', [CourseController::class, 'removeParticipant'])->name('courses.removeParticipant');
    Route::post('courses/{course}/addParticipant', [CourseController::class, 'addParticipant'])->name('courses.addParticipant');
    Route::post('courses/{course}/upload-material', [CourseController::class, 'uploadMaterial'])->name('courses.uploadMaterial');
    Route::post('courses/{course}/removeCompCourse', [CourseController::class, 'removeCompensationCourse'])->name('courses.removeCompCourse');
    Route::post('courses/{course}/addCompCourse', [CourseController::class, 'addCompensationCourse'])->name('courses.addCompCourse');
    Route::get('courses/download-material/{courseMaterial}', [CourseController::class, 'downloadMaterial'])->name('courses.downloadMaterial');
    Route::post('courses/{course}/delete-material', [CourseController::class, 'deleteMaterial'])->name('courses.deleteMaterial');
    Route::resource('courses', CourseController::class);

    Route::post('lessons/{lesson}/signout', [LessonController::class, 'signOut'])->name('lessons.signout');
    Route::post('lessons/{lesson}/signin', [LessonController::class, 'signIn'])->name('lessons.signin');
    Route::post('lessons/{lesson}/compensate', [LessonController::class, 'compensate'])->name('lessons.compensate');
    Route::post('lessons/{lesson}/assist', [LessonController::class, 'assist'])->name('lessons.assist');
    Route::post('lessons/{lesson}/send-message', [LessonController::class, 'sendMessage'])->name('lessons.sendMessage');
    Route::post('lessons/{lesson}/add-teacher', [LessonController::class, 'addTeacher'])->name('lessons.addTeacher');
    Route::post('lessons/{lesson}/set-teacher', [LessonController::class, 'setTeacher'])->name('lessons.setTeacher');
    Route::post('lessons/{lesson}/remove-teacher', [LessonController::class, 'removeTeacher'])->name('lessons.removeTeacher');
    Route::post('lessons/{lesson}/set-signed-in', [LessonController::class, 'setSignedIn'])->name('lessons.setSignedIn');
    Route::post('lessons/{lesson}/set-excused', [LessonController::class, 'setExcused'])->name('lessons.setExcused');
    Route::post('lessons/{lesson}/set-late', [LessonController::class, 'setLate'])->name('lessons.setLate');
    Route::post('lessons/{lesson}/set-noshow', [LessonController::class, 'setNoShow'])->name('lessons.setNoShow');
    Route::resource('lessons', LessonController::class);

    Route::put('/user/settings', [UserSettingsController::class, 'update'])->name('user-settings.update');
});
