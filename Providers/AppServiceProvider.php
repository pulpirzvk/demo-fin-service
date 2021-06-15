<?php

namespace App\Providers;

use App\Services\Api\IApiService;
use App\Services\Api\Tinkoff\TinkoffSandboxService;
use App\Services\Api\Tinkoff\TinkoffService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        foreach (glob(app_path() . '/Helpers/*.php') as $filename) {
            require_once($filename);
        }

        $this->app->bind(
            IApiService::class,
            static function ($app) {
                $sandbox = config('platforms.tinkoff.sandbox');
                return $sandbox === true
                    ? new TinkoffSandboxService(config('platforms.tinkoff.sandbox_key'))
                    : new TinkoffService(config('platforms.tinkoff.key'));
            }
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
