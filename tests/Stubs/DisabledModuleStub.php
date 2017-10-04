<?php

namespace TwoDojo\Test\ModuleManager\Stubs;

use TwoDojo\Module\AbstractModule;

class DisabledModuleStub extends AbstractModule
{
    protected $name = 'DisabledModuleStub';

    protected $uniqueName = '2dojo/disabled_module_stub';

    public function isEnabled(): bool
    {
        return false;
    }
}
