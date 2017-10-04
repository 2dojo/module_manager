<?php

namespace TwoDojo\Test\ModuleManager\Stubs;

use TwoDojo\ModuleManager\Repositories\ModuleRegistryRepository;

class ModuleRegistryRepositoryStub extends ModuleRegistryRepository
{
    public function getRegistry()
    {
        return $this->registry;
    }

    public function setRegistry($registry)
    {
        $this->registry = $registry;
    }
}
