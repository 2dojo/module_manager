<?php

namespace TwoDojo\Test\ModuleManager\Stubs;

use TwoDojo\ModuleManager\Registries\BaseRegistry;

class RegistryStub extends BaseRegistry
{
    public $scopes = [];

    protected function scope($scope, $arguments)
    {
        $this->scopes[$scope] = $arguments;
        return parent::scope($scope, $arguments);
    }

    /**
     * @inheritdoc
     */
    public function findByUniqueName(string $uniqueName)
    {
    }

    /**
     * @inheritdoc
     */
    public function save(array $attributes)
    {
    }

    /**
     * @inheritdoc
     */
    public function update($id, array $attributes)
    {
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
    }

    /**
     * @inheritdoc
     */
    public function findByField(string $field, $value)
    {
    }

    /**
     * @inheritdoc
     */
    public function all()
    {
    }
}