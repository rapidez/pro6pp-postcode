<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    // We place this in this middleware for the CSRF protection.
    Route::match(['get', 'post'], '/api/pro6pp', function (Request $request) {
        $request->merge([
            'postcode' => strtoupper(str_replace(' ', '', $request->postcode)),
        ])->validate([
            'postcode' => 'required|string|max:6',
            'housenumber' => 'required',
        ]);

        $cacheKey = 'pro6pp-'.$request->postcode.'-'.$request->housenumber;

        return Cache::rememberForever($cacheKey, function () use ($request) {
            return Http::pro6pp()->get('/autocomplete', [
                'nl_sixpp'=> $request->postcode,
                'streetnumber'=> $request->housenumber,
            ])->throw()->json();
        });
    });
});
