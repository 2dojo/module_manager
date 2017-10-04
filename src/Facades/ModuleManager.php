<?php

namespace TwoDojo\ModuleManager\Facades;

use TwoDojo\ModuleManager\ModuleManager as ModuleManagerClass;
use Illuminate\Support\Facades\Facade;

class ModuleManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ModuleManagerClass::class;
    }
}
