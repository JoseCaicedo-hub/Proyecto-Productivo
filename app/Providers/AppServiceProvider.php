<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use App\Http\Controllers\HeaderController;
use App\Helpers\PriceHelper;

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

        // Registrar macros de Blade para formateo de precios
        Blade::stringable(function ($value) {
            if (is_numeric($value)) {
                return PriceHelper::formatCOP($value);
            }
            return $value;
        });

        Blade::directive('formatCOP', function ($price) {
            return "<?php echo \App\Helpers\PriceHelper::formatCOP({$price}); ?>";
        });

        Blade::directive('formatCOPNoSymbol', function ($price) {
            return "<?php echo \App\Helpers\PriceHelper::formatCOPWithoutSymbol({$price}); ?>";
        });

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
