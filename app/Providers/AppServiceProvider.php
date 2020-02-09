<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if($this->app->environment('production') && parse_url(env('APP_URL'), PHP_URL_SCHEME) == 'https') {
            $this->app['request']->server->set('HTTPS', true);
        }

        if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $this->app['request']->server->set('HTTPS', true);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Blade::directive('icon', function ($expression) {
            return '<svg class="svg-icon"><use xlink:href="/font-awesome-5.11.2/sprites/solid.svg#<?php echo "'.$expression.'" ?>"></use></svg>';
        });

        Blade::directive('brand_icon', function ($expression) {
            return '<svg class="svg-icon"><use xlink:href="/font-awesome-5.11.2/sprites/brands.svg#<?php echo "'.$expression.'" ?>"></use></svg>';
        });

        Blade::directive('spinning_icon', function ($expression) {
            return '<svg class="svg-icon fa-spin"><use xlink:href="/font-awesome-5.11.2/sprites/solid.svg#<?php echo "'.$expression.'" ?>"></use></svg>';
        });
    }
}
