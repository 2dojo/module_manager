<?php

namespace TwoDojo\ModuleManager;

use TwoDojo\ModuleManager\Repositories\ModuleRegistryRepository;

class ModuleManagerServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = false;

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/module_manager.php' => config_path('module_manager.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/module_manager.php', 'module_manager');

        $this->app->singleton(ModuleManager::class, function ($app) {
            return new ModuleManager($app, $app->make(ModuleRegistryRepository::class));
        });
    }
    
    public function provides()
    {
        return [ModuleManager::class];
    }
}
