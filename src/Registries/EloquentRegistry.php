<?php

namespace TwoDojo\ModuleManager\Registries;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use TwoDojo\ModuleManager\Repositories\EloquentRepository;
use TwoDojo\ModuleManager\Support\ModuleDescriptor;

class EloquentRegistry extends BaseRegistry
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * @var \TwoDojo\ModuleManager\Repositories\EloquentRepository
     */
    protected $repository;

    /**
     * EloquentRegistry constructor.
     * @param Application $app
     * @param EloquentRepository $repository
     */
    public function __construct(Application $app, EloquentRepository $repository)
    {
        $this->app = $app;
        $this->repository = $repository;
        $this->repository->setModel($this->app->make(config('module_manager.models.module')));
    }

    /**
     * @inheritdoc
     */
    public function save(array $attributes)
    {
        return $this->repository->save($attributes);
    }

    /**
     * @inheritdoc
     */
    public function update($id, array $attributes)
    {
        $model = $this->repository->find($id);
        if ($model === null) {
            return false;
        }

        return $this->repository->update($model, $attributes);
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        return $this->mergeDescriptor($this->repository->find($id));
    }

    /**
     * @inheritdoc
     */
    public function findByUniqueName(string $uniqueName)
    {
        return $this->findByField('uniqueName', $uniqueName);
    }

    /**
     * @inheritdoc
     */
    public function findByField(string $field, $value)
    {
        return $this->mergeDescriptor($this->repository->findByField($field, $value));
    }

    /**
     * @inheritdoc
     */
    public function all()
    {
        return $this->mergeDescriptor($this->repository->all());
    }

    protected function mergeDescriptor($value)
    {
        $collection = collect([]);

        if ($value === null) {
            return $collection;
        }

        if (is_array($value) || $value instanceof Collection) {
            foreach ($value as $model) {
                $collection->put($model->id, new ModuleDescriptor($model->toArray()));
            }

            return $collection;
        }

        return $collection->put($value->id, new ModuleDescriptor($value->toArray()));
    }

    protected function scope($scope, $arguments)
    {
        $this->repository->{$scope}(...$arguments);

        return parent::scope($scope, $arguments);
    }
}
