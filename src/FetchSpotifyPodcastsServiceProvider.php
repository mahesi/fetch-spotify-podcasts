<?php

namespace Mahesi\FetchSpotifyPodcasts;

use Illuminate\Support\ServiceProvider;

class FetchSpotifyPodcastsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'fetch-spotify-podcasts');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'fetch-spotify-podcasts');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('fetch-spotify-podcasts.php'),
            ], 'config');

	        $this->publishes([
		        __DIR__.'/../config/spotify.php' => config_path('spotify.php'),
	        ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/fetch-spotify-podcasts'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/fetch-spotify-podcasts'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/fetch-spotify-podcasts'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'fetch-spotify-podcasts');
        $this->mergeConfigFrom(__DIR__.'/../config/spotify.php', 'spotify');

        // Register the main class to use with the facade
        $this->app->singleton('fetch-spotify-podcasts', function () {
            return new FetchSpotifyPodcasts;
        });
    }
}
