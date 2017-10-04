<?php

namespace TwoDojo\ModuleManager\Registries;

use TwoDojo\ModuleManager\Contracts\Repository;

abstract class BaseRegistry implements Repository
{
    /**
     * Find the module by unique name
     *
     * @param string $uniqueName
     * @return mixed
     */
    abstract public function findByUniqueName(string $uniqueName);

    /**
     * @param $scope
     * @param $arguments
     * @return $this
     */
    protected function scope($scope, $arguments)
    {
        return $this;
    }

    /**
     * Handle scope call
     *
     * @param $name
     * @param $arguments
     * @return BaseRegistry
     */
    public function __call($name, $arguments)
    {
        return $this->scope($name, $arguments);
    }
}
