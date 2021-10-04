<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        Route::pattern('year', '[0-9]{4}');
        Route::pattern('month', '[0-9]{2}');
        Route::pattern('day', '[0-9]{2}');
        Route::pattern('key', '[0-9a-zA-Z]{12}');
        Route::pattern('slug', '[0-9a-zA-Zà-öø-ÿāăąćĉċčŏœ\-]+');

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapICSRoutes();

        $this->mapEmailRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }

    protected function mapICSRoutes()
    {
        Route::middleware('ics')
             ->namespace($this->namespace)
             ->group(base_path('routes/ics.php'));
    }

    protected function mapEmailRoutes()
    {
        Route::middleware('email')
             ->namespace($this->namespace)
             ->group(base_path('routes/email.php'));
    }
}
