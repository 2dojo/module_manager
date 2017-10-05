
# Module manager

[![Latest Stable Version](https://poser.pugx.org/2dojo/module_manager/v/stable)](https://packagist.org/packages/2dojo/module_manager)
[![Build Status](https://travis-ci.org/2dojo/module_manager.svg?branch=master)](https://travis-ci.org/2dojo/module_manager)
[![codecov](https://codecov.io/gh/2dojo/module_manager/branch/master/graph/badge.svg)](https://codecov.io/gh/2dojo/module_manager)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/2dojo/module_manager/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/2dojo/module_manager/?branch=master)
[![License](https://poser.pugx.org/2dojo/module_manager/license)](https://packagist.org/packages/2dojo/module_manager)

## Table of contents
- [Installation](#installation)
- [Module Development](#module-development)
- [Module Manager Methods](#module-manager-methods)

## Installation
The Module Manager can be installed via composer:
```
composer require 2dojo/module_manager
```

This package uses Laravel auto-discovery so the ServiceProvider and the Facade automatically register itself.

After you installed this package you have to call the **ModuleManager** facade **initializeModules** method in you **AppServiceProvider boot** method.

```php
<?php

namespace App\Providers;

...

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        ModuleManager::initializeModules();
    }
}
```

If you want to store the module options in database you need to publish the migrations and config
```
php artisan vendor:publish --provider="TwoDojo\ModuleManager\ModuleManagerServiceProvider"
```
Run the migrations
```
php artisan migrate
```
And finally change the registry entry in the configuration to **database**
```php
// config/module_manager.php
'registry' => 'database'
```

## Module Development
You can create your modules in a separated composer package or in your laravel project for example in the app/Modules directory.

```php
<?php

namespace App\Modules;

use TwoDojo\Module\AbstractModule;

class ExampleModule extends AbstractModule
{
    /**
    * @var string The module display name
    */
    protected $name = 'ExampleModule';
}
```

After that you have to register the module in the ModuleManager for example in your **AppServiceProvider register method** or if you create a separated composer package you can register it in your package ServiceProvider boot method.
```php
<?php

namespace App\Providers;

...

class PackageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        ModuleManager::registerModule(ExampleModule::class);
        
        ...
    }
}
```

## Module Manager Methods
### registerModule
```php
/**
* Register a module to the module manager.
*
* @param string $moduleClass The module class
* @return bool
*/
public function registerModule(string $moduleClass) : bool
```

### initializeModules
```php
/**
* Initialize the registered modules
*/
public function initializeModules()
```

### enableModule
```php
/**
 * Enable a module
 *
 * @param $uniqueName The module unique name
 * @return bool
 */
public function enableModule($uniqueName) : bool
```

### disableModule
```php
/**
 * Disable a module
 *
 * @param string $uniqueName The module unique name
 * @return bool
 */
public function disableModule($uniqueName) : bool
```
