<?php

use App\Http\Controllers\LessonController;
use App\Http\Controllers\UserController;
use App\Livewire\CreateCourse;
use App\Livewire\EditCourse;
use App\Livewire\ListCourses;
use App\Livewire\MyCourses;
use App\Livewire\MyLessons;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::resource('users', UserController::class);
    Route::get('courses', ListCourses::class)->name('courses.index');
    Route::get('course/create', CreateCourse::class)->name('course.create');
    Route::get('course/edit/{course?}', EditCourse::class)->name('course.edit');
    Route::resource('lessons', LessonController::class);
    Route::get('/my-courses', MyCourses::class)->name('my-courses');
    Route::get('/my-lessons', MyLessons::class)->name('my-lessons');
});

# Remove this entry before going live!!!
Route::get('/migrate-db', function() {
    Artisan::call('migrate:fresh', ['--seed' => 'true']);
});
