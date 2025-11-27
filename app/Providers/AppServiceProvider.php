<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
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

        // Compartir los 5 productos más pedidos con el partial del header
        View::composer('web.partials.header', function ($view) {
            $top = HeaderController::topProductos(5);
            $view->with('topProducts', $top);
        });
    }
}
