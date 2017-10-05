<?php

namespace TwoDojo\Test\ModuleManager;

use Illuminate\Support\Facades\Config;
use TwoDojo\ModuleManager\ModuleManager;
use TwoDojo\ModuleManager\Repositories\ModuleRegistryRepository;
use TwoDojo\ModuleManager\Support\ModuleDescriptor;
use TwoDojo\Test\ModuleManager\Stubs\ModuleStub;
use TwoDojo\Test\ModuleManager\Stubs\DisabledModuleStub;
use Mockery as m;

class ModuleManagerTest extends TestCase
{
    public function tearDown()
    {
        parent::tearDown();

        m::close();
    }
    
    /**
     * @covers \TwoDojo\ModuleManager\ModuleManager::__construct()
     * @covers \TwoDojo\ModuleManager\ModuleManager::registerModule()
     */
    public function testCanRegisterModule()
    {
        $manager = $this->getModuleManagerInstance();

        $this->assertTrue($manager->registerModule(ModuleStub::class));
    }

    /**
     * @covers \TwoDojo\ModuleManager\ModuleManager::registerModule()
     */
    public function testCantRegisterModuleMultipleTimes()
    {
        $registryMock = m::mock(ModuleRegistryRepository::class);

        $registryMock->shouldReceive('find')->withAnyArgs()->andReturn(null);

        $module = new ModuleStub();

        $registryMock->shouldReceive('save')->with([
            'uniqueName' => $module->getUniqueName(),
            'is_enabled' => true
        ]);

        $manager = $this->getModuleManagerInstance($registryMock);
        $manager->registerModule(ModuleStub::class);

        $this->assertFalse($manager->registerModule(ModuleStub::class));
    }

    /**
     * @covers \TwoDojo\ModuleManager\ModuleManager::registerModule()
     */
    public function testCantRegisterModuleIfNotEnabled()
    {
        $manager = $this->getModuleManagerInstance();

        $this->assertFalse($manager->registerModule(DisabledModuleStub::class));
        $this->assertNull($manager->getModule('DisabledModuleStub'));
    }

    /**
     * @covers \TwoDojo\ModuleManager\ModuleManager::hasModule()
     */
    public function testHasModule()
    {
        $manager = $this->getModuleManagerInstance();
        $manager->registerModule(ModuleStub::class);

        $this->assertTrue($manager->hasModule('2dojo/module_stub'));
    }

    /**
     * @covers \TwoDojo\ModuleManager\ModuleManager::getModule()
     */
    public function testGetModule()
    {
        $manager = $this->getModuleManagerInstance();
        $manager->registerModule(ModuleStub::class);

        $this->assertCount(1, $manager->getModule());
        $this->assertNotNull($manager->getModule('2dojo/module_stub'));
    }

    /**
     * @covers \TwoDojo\ModuleManager\ModuleManager::initializeModules()
     * @covers \TwoDojo\ModuleManager\ModuleManager::initializeModuleRoutes()
     */
    public function testCanInitializeModules()
    {
        $registryMock = m::mock();
        $registryMock->shouldReceive('find')->with('2dojo/module_stub')->andReturn(true);
        $registryMock->shouldReceive('enabled')->andReturnSelf();
        $registryMock->shouldReceive('all')->andReturn(collect([new ModuleDescriptor(['id' => 1, 'uniqueName' => '2dojo/module_stub', 'is_enabled' => true])]));

        $manager = $this->getModuleManagerInstance($registryMock);
        $manager->registerModule(ModuleStub::class);

        $manager->initializeModules();
    }

    /**
     * @covers \TwoDojo\ModuleManager\ModuleManager::enableModule()
     * @covers \TwoDojo\ModuleManager\ModuleManager::updateModuleRecord()
     * @covers \TwoDojo\ModuleManager\ModuleManager::setModuleState()
     */
    public function testCanEnableModule()
    {
        $registryMock = m::mock();
        $registryMock->shouldReceive('find')->with('2dojo/module_stub')->andReturn(collect([new ModuleDescriptor(['id' => 1, 'uniqueName' => '2dojo/module_stub', 'is_enabled' => true])]));
        $registryMock->shouldReceive('update')->with(1, ['is_enabled' => true])->andReturn(true);

        $manager = $this->getModuleManagerInstance($registryMock);
        $manager->registerModule(ModuleStub::class);

        $manager->enableModule('2dojo/module_stub');
    }

    /**
     * @covers \TwoDojo\ModuleManager\ModuleManager::disableModule()
     * @covers \TwoDojo\ModuleManager\ModuleManager::updateModuleRecord()
     * @covers \TwoDojo\ModuleManager\ModuleManager::setModuleState()
     */
    public function testCanDisableModule()
    {
        $registryMock = m::mock();
        $registryMock->shouldReceive('find')->with('2dojo/module_stub')->andReturn(collect([new ModuleDescriptor(['id' => 1, 'uniqueName' => '2dojo/module_stub', 'is_enabled' => true])]));
        $registryMock->shouldReceive('update')->with(1, ['is_enabled' => false])->andReturn(true);

        $manager = $this->getModuleManagerInstance($registryMock);
        $manager->registerModule(ModuleStub::class);

        $manager->disableModule('2dojo/module_stub');
    }

    /**
     * @covers \TwoDojo\ModuleManager\ModuleManager::updateModuleRecord()
     */
    public function testUpdateCanCreateNewRecordIfItNotExists()
    {
        $registryMock = m::mock();
        $registryMock->shouldReceive('find')->with('2dojo/module_stub')->andReturn(collect([]));
        $registryMock->shouldReceive('update')->with(1, ['is_enabled' => false])->andReturn(true);
        $registryMock->shouldReceive('save')->with([
            'uniqueName' => '2dojo/module_stub',
            'is_enabled' => true,
        ])->andReturn(new ModuleDescriptor(['id' => 1]));

        $manager = $this->getModuleManagerInstance($registryMock);
        $manager->registerModule(ModuleStub::class);

        $manager->enableModule('2dojo/module_stub');
    }

    /**
     * @covers \TwoDojo\ModuleManager\ModuleManager::enableModule()
     * @expectedException \TwoDojo\ModuleManager\Exceptions\ModuleNotFoundException
     */
    public function testEnableModuleThrowExceptionIfModuleNotRegistered()
    {
        $manager = $this->getModuleManagerInstance();
        $manager->enableModule('2dojo/not_exists_module');
    }

    /**
     * @covers \TwoDojo\ModuleManager\ModuleManager::disableModule()
     * @expectedException \TwoDojo\ModuleManager\Exceptions\ModuleNotFoundException
     */
    public function testDisableModuleThrowExceptionIfModuleNotRegistered()
    {
        $manager = $this->getModuleManagerInstance();
        $manager->disableModule('2dojo/not_exists_module');
    }

    private function getModuleManagerInstance($registry = null)
    {
        Config::set('module_manager.registry', 'file');
        return new ModuleManager($this->app, $registry ?? $this->app->make(ModuleRegistryRepository::class));
    }
}
