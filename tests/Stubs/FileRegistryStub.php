<?php

namespace TwoDojo\Test\ModuleManager\Stubs;

use TwoDojo\ModuleManager\Registries\FileRegistry;

class FileRegistryStub extends FileRegistry
{
    public function getScopes()
    {
        return $this->scopes;
    }
}
