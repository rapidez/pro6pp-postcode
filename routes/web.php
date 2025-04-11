<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Rapidez\Pro6ppPostcode\Http\Controllers\Pro6ppController;

Route::middleware('web')->group(function () {
    // We place this in this middleware for the CSRF protection.
    Route::match(['get', 'post'], '/api/pro6pp', Pro6ppController::class);
});
