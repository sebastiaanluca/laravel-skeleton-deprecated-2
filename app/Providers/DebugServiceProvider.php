<?php

namespace App\Providers;

use Clockwork\Support\Laravel\ClockworkMiddleware;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class DebugServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @param \Illuminate\Contracts\Http\Kernel|\Illuminate\Foundation\Http\Kernel $kernel
     *
     * @return void
     */
    public function boot(Kernel $kernel)
    {
        $kernel->prependMiddleware(ClockworkMiddleware::class);
    }
}
