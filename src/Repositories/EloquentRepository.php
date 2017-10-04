<?php

namespace TwoDojo\ModuleManager\Repositories;

use Illuminate\Database\Eloquent\Model;
use TwoDojo\ModuleManager\Contracts\Repository;

class EloquentRepository implements Repository
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    protected $scopes = [];

    /**
     * EloquentRepository constructor.
     * @param \Illuminate\Database\Eloquent\Model|null $model
     */
    public function __construct($model = null)
    {
        if ($model !== null) {
            $this->setModel($model);
        }
    }

    /**
     * Set the repository model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function setModel(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get the repository model.
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @inheritdoc
     */
    public function save(array $attributes)
    {
        $model = app()->make(get_class($this->model));
        $model->fill($attributes);
        $model->save();

        return $model;
    }

    /**
     * @inheritdoc
     */
    public function update($id, array $attributes)
    {
        $model = $id;
        if (!($id instanceof Model)) {
            $model = $this->find($id);
        }

        return $model->update($attributes);
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        return $this->applyScopes()->find($id);
    }

    /**
     * @inheritdoc
     */
    public function findByField(string $field, $value)
    {
        return $this->applyScopes()->where($field, $value)->get();
    }

    /**
     * @inheritdoc
     */
    public function all()
    {
        return $this->applyScopes()->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function applyScopes()
    {
        $model = $this->model;

        foreach ($this->scopes as $record) {
            list($scope, $arguments) = $record;
            $model = $model->{$scope}(...$arguments);
        }

        $this->scopes = [];

        return $model;
    }

    /**
     * @param $scope
     * @param $arguments
     * @return $this
     */
    public function __call($scope, $arguments)
    {
        $this->scopes[] = [$scope, $arguments];

        return $this;
    }
}
