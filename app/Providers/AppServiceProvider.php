<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use App\Http\Controllers\HeaderController;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        RateLimiter::for('chatbot', function (Request $request) {
            $key = $request->user()
                ? 'chatbot:user:' . $request->user()->id
                : 'chatbot:ip:' . $request->ip();

            return [
                Limit::perMinute(30)->by($key),
            ];
        });

        // Compartir los 5 productos más pedidos con el partial del header
        View::composer('web.partials.header', function ($view) {
            $top = HeaderController::topProductos(5);
            $view->with('topProducts', $top);
        });
    }
}
