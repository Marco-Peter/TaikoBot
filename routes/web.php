<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    /**
     * This is the local API group
     *
     * All data exchange for the provided webapplication
     * happens through this group functions.
     */
    Route::prefix('local-api')->group(function () {
        Route::get('/user', DashboardController::class);
        Route::resource('/courses', CourseController::class);
    });

    /**
     * Catch all
     *
     * This route catches all request, therefore it must
     * stay at the end of the file.
     */
    Route::get('/{vue_capture?}', function () {
        return view('app');
    })->where('vue_capture', '[\/\w\.-]*');
});
