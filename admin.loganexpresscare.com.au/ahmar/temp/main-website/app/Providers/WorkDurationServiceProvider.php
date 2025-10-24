<?php

namespace App\Providers;

use App\Models\WorkDuration;
use App\Observers\WorkDurationObserver;
use Illuminate\Support\ServiceProvider;

class WorkDurationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        WorkDuration::observe(WorkDurationObserver::class);
    }
}
