<?php

namespace App\Providers;

use App\Models\Distribusi;
use App\Models\Fileexcel;
use App\Models\group_fileexcel;
use App\Models\Jmo;
use App\Models\Mutasi;
use App\Models\Mutasi_list;
use App\Models\Statuscall;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    // public const HOME = '/home';
    public const HOME = '/';
    public const ADMIN = '/admin';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        Route::bind('jmoid', function (string $value) {
            return Jmo::where('id', decrypt($value))->firstOrFail();
        });
        Route::bind('mutasiid', function (string $value) {
            return Mutasi::where('id', decrypt($value))->firstOrFail();
        });
        Route::bind('mutasilistid', function (string $value) {
            return Mutasi_list::where('id', decrypt($value))->firstOrFail();
        });
        Route::bind('callhistoryid', function (string $value) {
            return Distribusi::where('id', decrypt($value))->firstOrFail();
        });
        Route::bind('fileexcel_id', function (string $value) {
            return Fileexcel::where('id', decrypt($value))->firstOrFail();
        });
        Route::bind('statuscallid', function (string $value) {
            return Statuscall::where('id', decrypt($value))->firstOrFail();
        });
        Route::bind('campaigngroup_id', function (string $value) {
            return group_fileexcel::where('id', decrypt($value))->firstOrFail();
        });


        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
