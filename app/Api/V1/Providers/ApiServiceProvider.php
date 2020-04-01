<?php
/**
 * @author thanhnv
 */
namespace App\Api\V1\Providers;

use App\Api\V1\Services\Interfaces\CategoryServiceInterface;
use App\Api\V1\Services\Interfaces\ProductServiceInterface;
use App\Api\V1\Services\Interfaces\ShippingServiceInterface;
use App\Api\V1\Services\Interfaces\ShoppingServiceInterface;
use App\Api\V1\Services\Interfaces\UserServiceInterface;
use App\Api\V1\Services\Production\CategoryService;
use App\Api\V1\Services\Production\ProductService;
use App\Api\V1\Services\Production\ShippingService;
use App\Api\V1\Services\Production\ShoppingService;
use App\Api\V1\Services\Production\UserService;
use Illuminate\Support\ServiceProvider;
class ApiServiceProvider extends ServiceProvider
{
    protected $services = [
        UserServiceInterface::class=>UserService::class,
        ProductServiceInterface::class => ProductService::class,
        CategoryServiceInterface::class => CategoryService::class,
        ShippingServiceInterface::class => ShippingService::class,
        ShoppingServiceInterface::class => ShoppingService::class
    ];
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Register services
        foreach ($this->services as $inteface => $service) {
            $this->app->singleton($inteface, $service);
        }
    }
}
