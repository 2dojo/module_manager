<?php

namespace TwoDojo\Test\ModuleManager\Stubs;

use TwoDojo\Module\AbstractModule;

class ModuleStub extends AbstractModule
{
    protected $name = 'ModuleStub';

    protected $description = 'Module Stub Description';

    protected $major = 1;

    protected $minor = 2;

    protected $patch = 25;

    public $isInitialized = false;

    protected $uniqueName = '2dojo/module_stub';

    public function __construct()
    {
        $this->modulePath = realpath(__DIR__.'/../data');
    }

    public function onInitialized()
    {
        $this->isInitialized = true;
    }
}
