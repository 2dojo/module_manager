<?php

namespace TwoDojo\ModuleManager\Repositories;

use Illuminate\Contracts\Foundation\Application;
use TwoDojo\ModuleManager\Contracts\Repository;
use TwoDojo\ModuleManager\Exceptions\UnknownRegistryTypeException;
use TwoDojo\ModuleManager\Registries\EloquentRegistry;
use TwoDojo\ModuleManager\Registries\FileRegistry;

/**
 * Class ModuleRegistryRepository
 *
 * @method \TwoDojo\ModuleManager\Repositories\ModuleRegistryRepository enabled()
 */
class ModuleRegistryRepository implements Repository
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * @var string
     */
    protected $registryType;

    /**
     * @var \TwoDojo\ModuleManager\Registries\BaseRegistry
     */
    protected $registry;

    /**
     * ModuleRegistry constructor.
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->registryType = config('module_manager.registry');

        $this->initializeRegistryRepository();
    }

    /**
     * @inheritdoc
     */
    public function save(array $attributes)
    {
        return $this->registry->save($attributes);
    }

    /**
     * @inheritdoc
     */
    public function update($id, array $attributes)
    {
        return $this->registry->update($id, $attributes);
    }

    private function initializeRegistryRepository()
    {
        switch ($this->registryType) {
            case 'database':
                $this->registry = $this->app->make(EloquentRegistry::class);
                break;
            case 'file':
                $this->registry = $this->app->make(FileRegistry::class);
                break;
            default:
                throw new UnknownRegistryTypeException($this->registryType);
        }
    }

    /**
     * @inheritdoc
     */
    public function find($uniqueName)
    {
        return $this->registry->findByUniqueName($uniqueName);
    }

    /**
     * @inheritdoc
     */
    public function findByField(string $field, $value)
    {
        return $this->registry->findByField($field, $value);
    }

    /**
     * @inheritdoc
     */
    public function all()
    {
        return $this->registry->all();
    }

    public function __call($scope, $arguments)
    {
        $this->registry->{$scope}(...$arguments);

        return $this;
    }
}
