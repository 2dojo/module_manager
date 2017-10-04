<?php

namespace TwoDojo\Test\ModuleManager\Repositories;

use Illuminate\Support\Facades\Config;
use TwoDojo\ModuleManager\Registries\BaseRegistry;
use TwoDojo\ModuleManager\Registries\EloquentRegistry;
use TwoDojo\ModuleManager\Registries\FileRegistry;
use TwoDojo\Test\ModuleManager\Stubs\ModuleRegistryRepositoryStub;
use TwoDojo\Test\ModuleManager\TestCase;
use Mockery as m;

class ModuleRegistryRepositoryTest extends TestCase
{
    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /**
     * @covers \TwoDojo\ModuleManager\Repositories\ModuleRegistryRepository::__construct()
     * @covers \TwoDojo\ModuleManager\Repositories\ModuleRegistryRepository::initializeRegistryRepository()
     */
    public function testConstruct()
    {
        Config::set('module_manager.registry', 'file');
        $registry = new ModuleRegistryRepositoryStub($this->app);

        Config::set('module_manager.registry', 'database');
        $registry2 = new ModuleRegistryRepositoryStub($this->app);

        $this->assertInstanceOf(FileRegistry::class, $registry->getRegistry());
        $this->assertInstanceOf(EloquentRegistry::class, $registry2->getRegistry());
    }

    /**
     * @covers \TwoDojo\ModuleManager\Repositories\ModuleRegistryRepository::__construct()
     * @covers \TwoDojo\ModuleManager\Repositories\ModuleRegistryRepository::initializeRegistryRepository()
     * @expectedException \TwoDojo\ModuleManager\Exceptions\UnknownRegistryTypeException
     */
    public function testConstructWithInvalidRegistryTypeThrowException()
    {
        Config::set('module_manager.registry', 'unknown');
        $registry = new ModuleRegistryRepositoryStub($this->app);
    }

    /**
     * @covers \TwoDojo\ModuleManager\Repositories\ModuleRegistryRepository::save()
     */
    public function testCanSave()
    {
        $attributes = ['test_key' => 'test_value'];
        $registryMock = m::mock(BaseRegistry::class);
        $registryMock->shouldReceive('save')->with($attributes);

        $registry = new ModuleRegistryRepositoryStub($this->app);
        $registry->setRegistry($registryMock);

        $registry->save($attributes);
    }

    /**
     * @covers \TwoDojo\ModuleManager\Repositories\ModuleRegistryRepository::update()
     */
    public function testCanUpdate()
    {
        $attributes = ['test_key' => 'test_value'];
        $registryMock = m::mock(BaseRegistry::class);
        $registryMock->shouldReceive('update')->with(2, $attributes);

        $registry = new ModuleRegistryRepositoryStub($this->app);
        $registry->setRegistry($registryMock);

        $registry->update(2, $attributes);
    }

    /**
     * @covers \TwoDojo\ModuleManager\Repositories\ModuleRegistryRepository::find()
     */
    public function testCanFind()
    {
        $registryMock = m::mock(BaseRegistry::class);
        $registryMock->shouldReceive('findByUniqueName')->with('test_module');

        $registry = new ModuleRegistryRepositoryStub($this->app);
        $registry->setRegistry($registryMock);

        $registry->find('test_module');
    }

    /**
     * @covers \TwoDojo\ModuleManager\Repositories\ModuleRegistryRepository::findByField()
     */
    public function testCanFindByField()
    {
        $registryMock = m::mock(BaseRegistry::class);
        $registryMock->shouldReceive('findByField')->with('uniqueName', 'test_module');

        $registry = new ModuleRegistryRepositoryStub($this->app);
        $registry->setRegistry($registryMock);

        $registry->findByField('uniqueName', 'test_module');
    }

    /**
     * @covers \TwoDojo\ModuleManager\Repositories\ModuleRegistryRepository::all()
     */
    public function testCanGetAll()
    {
        $registryMock = m::mock(BaseRegistry::class);
        $registryMock->shouldReceive('all')->withNoArgs();

        $registry = new ModuleRegistryRepositoryStub($this->app);
        $registry->setRegistry($registryMock);

        $registry->all();
    }

    /**
     * @covers \TwoDojo\ModuleManager\Repositories\ModuleRegistryRepository::__call()
     */
    public function testCanRegisterScope()
    {
        $parameters = [null, false, 1, true];
        $parameters2 = [true, [], 1, new \stdClass()];

        $registryMock = m::mock(BaseRegistry::class);
        $registryMock->shouldReceive('someScope')->with(...$parameters);
        $registryMock->shouldReceive('someScope2')->with(...$parameters2);

        $registry = new ModuleRegistryRepositoryStub($this->app);
        $registry->setRegistry($registryMock);

        $registry->someScope(...$parameters);
        $registry->someScope2(...$parameters2);
    }
}
