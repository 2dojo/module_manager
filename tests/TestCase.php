<?php

namespace TwoDojo\Test\ModuleManager;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \TwoDojo\ModuleManager\ModuleManagerServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'ModuleManager' => \TwoDojo\ModuleManager\Facades::class,
        ];
    }
}