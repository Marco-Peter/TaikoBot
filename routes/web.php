<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    /**
     * This is the local API group
     *
     * All data exchange for the provided webapplication
     * happens through this group functions.
    */
    Route::prefix('local-api')->group(function () {
        Route::get('/user', function(Request $request) {
            $user = $request->user();
            return $user;
        });
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
