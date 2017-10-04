<?php

namespace TwoDojo\Test\ModuleManager\Registries;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use TwoDojo\ModuleManager\Registries\FileRegistry;
use TwoDojo\ModuleManager\Support\ModuleDescriptor;
use TwoDojo\Test\ModuleManager\Stubs\FileRegistryStub;
use TwoDojo\Test\ModuleManager\TestCase;
use Mockery as m;

class FileRegistryTest extends TestCase
{
    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /**
     * @covers \TwoDojo\ModuleManager\Registries\FileRegistry::__construct()
     */
    public function testCanConstruct()
    {
        $storageMock = m::mock();
        $this->registerStorageMock($storageMock);

        new FileRegistry();

        $storageMock->shouldHaveReceived('exists')->once();
        $storageMock->shouldHaveReceived('makeDirectory')->once();
    }

    /**
     * @covers \TwoDojo\ModuleManager\Registries\FileRegistry::save()
     * @covers \TwoDojo\ModuleManager\Registries\FileRegistry::getFiles()
     * @covers \TwoDojo\ModuleManager\Registries\FileRegistry::getFile()
     * @covers \TwoDojo\ModuleManager\Registries\FileRegistry::getNewModuleDescriptor()
     */
    public function testCanSave()
    {
        $attributes = ['uniqueName' => 'test_module', 'test_key_1' => 'test_value'];
        $storageMock = m::mock();
        $storageMock->shouldReceive('allFiles')->withAnyArgs()->andReturn(collect([]));

        $json = null;
        $storageMock->shouldReceive('put')->with(m::any(), m::on(function ($entryData) use (&$json) {
            $json = $entryData;
            return true;
        }))->andReturn(true);

        $storageMock->shouldReceive('getFile')->passthru();
        $storageMock->shouldReceive('exists')->with(m::any())->andReturn(true);
        $storageMock->shouldReceive('get')->with(m::any())->andReturn('{}');

        $this->registerStorageMock($storageMock);

        $registry = new FileRegistry();

        $response = $registry->save($attributes);

        $this->assertInstanceOf(ModuleDescriptor::class, $response);

        $parsed = json_decode($json, true);
        $this->assertArrayHasKey('created_at', $parsed);
        $this->assertArrayHasKey('updated_at', $parsed);
    }

    /**
     * @covers \TwoDojo\ModuleManager\Registries\FileRegistry::update()
     */
    public function testCanUpdate()
    {
        $attributes = [
            'uniqueName' => 'test_module',
            'test_key_1' => 'test_value',
            'created_at' => Carbon::now()->subSecond(3)->timestamp
        ];
        $storageMock = m::mock();
        $storageMock->shouldReceive('allFiles')->withAnyArgs()->andReturn(['test_module_path']);
        $storageMock->shouldReceive('get')->with('test_module_path')->andReturn('{"id": 1}');

        $json = null;
        $storageMock->shouldReceive('put')->with(m::any(), m::on(function ($entryData) use (&$json) {
            $json = $entryData;
            return true;
        }))->andReturn(true);

        $this->registerStorageMock($storageMock);

        $registry = new FileRegistry();

        $this->assertTrue($registry->update(1, $attributes));
        $this->assertNotEquals($attributes['created_at'], json_decode($json, true)['updated_at']);
    }

    /**
     * @covers \TwoDojo\ModuleManager\Registries\FileRegistry::update()
     */
    public function testUpdateReturnFalseIfRecordNotExists()
    {
        $storageMock = m::mock();
        $storageMock->shouldReceive('allFiles')->withAnyArgs()->andReturn([]);

        $this->registerStorageMock($storageMock);

        $registry = new FileRegistry();

        $this->assertFalse($registry->update(1, []));
    }

    /**
     * @covers \TwoDojo\ModuleManager\Registries\FileRegistry::find()
     */
    public function testCanFind()
    {
        $storageMock = m::mock();
        $storageMock->shouldReceive('allFiles')->withAnyArgs()->andReturn(['test_module_path']);
        $storageMock->shouldReceive('get')->with('test_module_path')->andReturn('{"id": 1, "uniqueName": "test_module", "is_enabled": true}');

        $this->registerStorageMock($storageMock);

        $registry =new FileRegistry();

        $response = $registry->find(1);

        $this->assertInstanceOf(ModuleDescriptor::class, $response);
    }

    /**
     * @covers \TwoDojo\ModuleManager\Registries\FileRegistry::find()
     */
    public function testFindReturnNullIfRecordNotExists()
    {
        $storageMock = m::mock();
        $storageMock->shouldReceive('allFiles')->withAnyArgs()->andReturn([]);

        $this->registerStorageMock($storageMock);

        $registry =new FileRegistry();

        $response = $registry->find(1);

        $this->assertNull($response);
    }

    /**
     * @covers \TwoDojo\ModuleManager\Registries\FileRegistry::findByUniqueName()
     */
    public function testCanFindByUniqueName()
    {
        $storageMock = m::mock();
        $storageMock->shouldReceive('exists')->withAnyArgs()->andReturn(true);
        $storageMock->shouldReceive('get')->withAnyArgs()->andReturn('{"id": 1, "uniqueName": "test_module", "is_enabled": true}');

        $this->registerStorageMock($storageMock);

        $registry =new FileRegistry();

        $response = $registry->findByUniqueName('test_module')->first();

        $this->assertInstanceOf(ModuleDescriptor::class, $response);
    }

    /**
     * @covers \TwoDojo\ModuleManager\Registries\FileRegistry::findByField()
     */
    public function testCanFindByField()
    {
        $storageMock = m::mock();
        $storageMock->shouldReceive('allFiles')->withAnyArgs()->andReturn(['test_module_path']);
        $storageMock->shouldReceive('get')->with('test_module_path')->andReturn('{"id": 1, "uniqueName": "test_module", "is_enabled": true}');

        $this->registerStorageMock($storageMock);

        $registry =new FileRegistry();

        $response = $registry->findByField('is_enabled', true)->first();

        $this->assertInstanceOf(ModuleDescriptor::class, $response);
    }

    /**
     * @covers \TwoDojo\ModuleManager\Registries\FileRegistry::all()
     * @covers \TwoDojo\ModuleManager\Registries\FileRegistry::getFiles()
     */
    public function testCanGetAll()
    {
        $storageMock = m::mock();
        $storageMock->shouldReceive('allFiles')->withAnyArgs()->andReturn(['test_module_path']);
        $storageMock->shouldReceive('get')->with('test_module_path')->andReturn('{"id": 1, "uniqueName": "test_module", "is_enabled": true}');

        $this->registerStorageMock($storageMock);

        $registry =new FileRegistry();

        $response = $registry->all();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertCount(1, $response);
    }

    /**
     * @covers \TwoDojo\ModuleManager\Registries\FileRegistry::scope()
     */
    public function testCanRegisterScope()
    {
        $storageMock = m::mock();

        $this->registerStorageMock($storageMock);

        $registry = new FileRegistryStub();

        $registry->someScope(1, null, ['array']);

        $this->assertCount(1, $registry->getScopes());
        $this->assertCount(3, $registry->getScopes()[0][1]);
    }

    /**
     * @covers \TwoDojo\ModuleManager\Registries\FileRegistry::getFile()
     * @covers \TwoDojo\ModuleManager\Registries\FileRegistry::getFullPath()
     */
    public function testGetFileReturnNullIfRecordNotExists()
    {
        $storageMock = m::mock();
        $storageMock->shouldReceive('exists')->with('module_manager/registry/test_module.json')->andReturn(false);

        $this->registerStorageMock($storageMock);

        $registry = new FileRegistry();

        $registry->enabled();

        $response = $registry->findByUniqueName('test_module');

        $this->assertNull($response);
    }

    /**
     * @covers \TwoDojo\ModuleManager\Registries\FileRegistry::applyScopes()
     */
    public function testCanApplyScope()
    {
        $storageMock = m::mock();
        $storageMock->shouldReceive('exists')->withAnyArgs()->andReturn(true);
        $storageMock->shouldReceive('get')->withAnyArgs()->andReturn('{"id": 1, "uniqueName": "test_module", "is_enabled": true}');

        $this->registerStorageMock($storageMock);

        $registry = new FileRegistry();

        $registry->enabled();

        $registry->findByUniqueName('test_module');
    }

    private function registerStorageMock($mock)
    {
        $mock->shouldReceive('exists')->with('module_manager/registry')->andReturn(false);
        $mock->shouldReceive('makeDirectory')->with('module_manager/registry')->andReturn(true);
        Storage::shouldReceive('disk')->with(config('module_manager.file_registry_storage'))->andReturn($mock);
    }
}
