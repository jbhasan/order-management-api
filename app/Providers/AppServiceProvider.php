<?php

namespace App\Providers;

use App\Events\OrderCreated;
use App\Events\OrderStatusChanged;
use App\Listeners\SendOrderConfirmationEmail;
use App\Listeners\SendOrderStatusUpdateEmail;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        OrderCreated::class => [
            SendOrderConfirmationEmail::class,
        ],
        OrderStatusChanged::class => [
            SendOrderStatusUpdateEmail::class,
        ],
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register repositories
        $this->app->singleton(\App\Repositories\ProductRepository::class);
        $this->app->singleton(\App\Repositories\OrderRepository::class);
        $this->app->singleton(\App\Repositories\InventoryRepository::class);

        // Register services
        $this->app->singleton(\App\Services\ProductService::class);
        $this->app->singleton(\App\Services\OrderService::class);
        $this->app->singleton(\App\Services\InventoryService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
