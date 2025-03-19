<?php

namespace Rapidez\Pro6ppPostcode;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class Pro6ppPostcodeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/rapidez/pro6pp-postcode.php', 'rapidez.pro6pp-postcode');
    }

    public function boot()
    {
        $this
            ->bootRoutes()
            ->bootPublishables()
            ->bootMacros();
    }

    public function bootRoutes() : self
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        return $this;
    }

    public function bootPublishables() : self
    {
        $this->publishes([
            __DIR__.'/../config/rapidez/pro6pp-postcode.php' => config_path('rapidez/pro6pp-postcode.php'),
        ], 'rapidez-pro6pp-postcode-config');

        return $this;
    }

    public function bootMacros() : self
    {
        Http::macro('pro6pp', function () {
            return Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->withQueryParameters([
                'auth_key' => config('rapidez.pro6pp-postcode.api_key'),
            ])
            ->baseUrl( config('rapidez.pro6pp-postcode.api_url'));
        });

        return $this;
    }
}
