<?php
/**
 * @author thanhnv
 */
namespace App\Backend\Providers;

use Illuminate\Support\ServiceProvider;
use App\Core\Services\Interfaces As CoreInterface;
use App\Core\Services\Production As CoreProduction;
use App\Backend\Services\Interfaces As BackendInterface;
use App\Backend\Services\Production As BackendProduction;
class BackendServiceProvider extends ServiceProvider
{
    protected $services = [
        CoreInterface\UploadServiceInterface::class => CoreProduction\UploadService ::class,
        BackendInterface\UserServiceInterface::class=>BackendProduction\UserService::class,
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
