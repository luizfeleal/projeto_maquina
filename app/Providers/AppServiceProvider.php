<?php

namespace App\Providers;

use App\Support\ApiMock;
use Illuminate\Support\Facades\Log;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (ApiMock::enabled()) {
            Log::warning('API_MOCK_ENABLED=true — a aplicação está usando dados mock (sem API externa).');
        }
    }
}
