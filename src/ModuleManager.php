<?php

namespace TwoDojo\ModuleManager;

use Illuminate\Support\Facades\Route;
use TwoDojo\Module\AbstractModule;
use TwoDojo\ModuleManager\Exceptions\ModuleNotFoundException;
use TwoDojo\ModuleManager\Support\EventDispatcher;
use TwoDojo\ModuleManager\Support\Requester;

class ModuleManager
{
    /**
     * @var \Illuminate\Support\Collection
     */
    private $modules;

    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    private $app;

    /**
     * @var \TwoDojo\ModuleManager\Repositories\ModuleRegistryRepository
     */
    private $registry;

    /**
     * @var \TwoDojo\ModuleManager\Support\EventDispatcher
     */
    private $dispatcher;

    /**
     * @var array
     */
    private $resolvedModules = [];

    /**
     * ModuleManager constructor.
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \TwoDojo\ModuleManager\Repositories\ModuleRegistryRepository $registry
     */
    public function __construct($app, $registry)
    {
        $this->modules = collect([]);
        $this->app = $app;
        $this->registry = $registry;
        $this->dispatcher = $this->app->make(EventDispatcher::class);
    }

    /**
     * @param string $module
     * @return bool
     */
    public function registerModule($module) : bool
    {
        $module = $this->app->make($module);

        // If the module is already registered we skip it
        if ($this->hasModule($module->getUniqueName())) {
            return false;
        }

        // If the module not enabled we skip it
        if (!$module->isEnabled()) {
            return false;
        }

        $this->dispatcher->registerListener($module);

        $this->modules->put($module->getUniqueName(), $module);

        $record = $this->registry->find($module->getUniqueName());
        // If we not found the record for this module then we assumed the module is just installed
        if ($record === null) {
            $this->registry->save([
                'uniqueName' => $module->getUniqueName(),
                'is_enabled' => true,
            ]);
        }

        return true;
    }

    /**
     * @param string $uniqueName
     * @return bool
     */
    public function hasModule(string $uniqueName) : bool
    {
        return $this->modules->has($uniqueName);
    }

    /**
     * Get the requested module or all registered modules
     *
     * @param null|string $uniqueName
     * @return \Illuminate\Support\Collection|\TwoDojo\Module\AbstractModule|null
     */
    public function getModule($uniqueName = null)
    {
        if ($uniqueName === null) {
            return $this->modules;
        }

        return $this->modules->get($uniqueName, null);
    }

    /**
     * Initialize the modules
     */
    public function initializeModules()
    {
        $modules = $this->registry->enabled()->all();
        foreach ($modules as $model) {
            $module = $this->modules->first(function (AbstractModule $module) use ($model) {
                return $module->getUniqueName() === $model->uniqueName;
            });

            if ($module === null) {
                continue;
            }

            if (isset($this->resolvedModules[$module->getUniqueName()])) {
                continue;
            }

            $requester = Requester::make()->setBasePath($module->getModulePath());
            if ($requester->exists('routes')) {
                $this->initializeModuleRoutes($module, $requester);
            }

            $this->dispatcher->dispatchEvent($module->getUniqueName(), 'initialized');
            $this->resolvedModules[$module->getUniqueName()] = true;
        }
    }

    protected function initializeModuleRoutes(AbstractModule $module, Requester $requester)
    {
        foreach ($requester->getFiles('routes') as $routeFile) {
            $name = basename($routeFile->getFilename(), '.php');
            $namespace = (new \ReflectionClass($module))->getNamespaceName();

            $route = Route::middleware($name)->namespace($namespace.'\\Http\\Controllers');
            switch ($name) {
                case 'api':
                    $route = $route->prefix($name);
                    break;
            }

            $route->group($requester->path('routes/'.$routeFile->getFilename()));
        }
    }

    /**
     * Enable a module
     *
     * @param string $uniqueName
     * @return bool
     * @throws ModuleNotFoundException
     */
    public function enableModule($uniqueName) : bool
    {
        if (!$this->hasModule($uniqueName)) {
            throw new ModuleNotFoundException();
        }

        return $this->setModuleState($uniqueName, true);
    }

    /**
     * Disable a module
     *
     * @param string $uniqueName
     * @return bool
     * @throws ModuleNotFoundException
     */
    public function disableModule($uniqueName) : bool
    {
        if (!$this->hasModule($uniqueName)) {
            throw new ModuleNotFoundException();
        }

        return $this->setModuleState($uniqueName, false);
    }

    /**
     * @param $uniqueName
     * @param bool $isEnabled
     * @return bool
     */
    protected function setModuleState($uniqueName, bool $isEnabled) : bool
    {
        $module = $this->getModule($uniqueName);

        $saved = $this->updateModuleRecord($module, ['is_enabled' => $isEnabled]);
        if ($saved) {
            $this->dispatcher->dispatchEvent($module->getUniqueName(), $isEnabled ? 'enabled' : 'disabled');
        }

        return $saved;
    }

    /**
     * @param \TwoDojo\Module\AbstractModule $module
     * @param array $data
     * @return bool
     */
    protected function updateModuleRecord($module, array $data) : bool
    {
        $record = $this->registry->find($module->getUniqueName())->first();
        if ($record === null) {
            $record = $this->registry->save(array_merge_recursive([
                'uniqueName' => $module->getUniqueName()
            ], $data));

            return $record->id !== null;
        }

        return $this->registry->update($record->id, $data);
    }
}
