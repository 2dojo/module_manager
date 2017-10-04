<?php

namespace TwoDojo\Test\ModuleManager\Registries;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use TwoDojo\ModuleManager\Registries\EloquentRegistry;
use TwoDojo\ModuleManager\Repositories\EloquentRepository;
use TwoDojo\Test\ModuleManager\Stubs\ModelStub;
use TwoDojo\Test\ModuleManager\TestCase;
use Mockery as m;

class EloquentRegistryTest extends TestCase
{
    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /**
     * @covers \TwoDojo\ModuleManager\Registries\EloquentRegistry::__construct()
     */
    public function testCanConstruct()
    {
        $mock = m::mock(EloquentRepository::class);
        $mock->shouldReceive('setModel')->withAnyArgs();

        $registry = new EloquentRegistry($this->app, $mock);
    }

    /**
     * @covers \TwoDojo\ModuleManager\Registries\EloquentRegistry::save()
     */
    public function testCanSave()
    {
        $attributes = [
            'test_key_1' => 'test_value',
            'test_key_2' => 'test_value_2'
        ];

        $mock = m::mock(EloquentRepository::class);
        $mock->shouldReceive('setModel')->withAnyArgs();

        $mock->shouldReceive('save')->with($attributes);

        $registry = new EloquentRegistry($this->app, $mock);

        $registry->save($attributes);
    }

    /**
     * @covers \TwoDojo\ModuleManager\Registries\EloquentRegistry::update()
     */
    public function testCanUpdate()
    {
        $attributes = [
            'test_key_1' => 'test_value',
            'test_key_2' => 'test_value_2'
        ];

        $mock = m::mock(EloquentRepository::class);
        $mock->shouldReceive('setModel')->withAnyArgs();

        $modelMock = m::mock(Model::class);
        $mock->shouldReceive('find')->with(1)->andReturn($modelMock);

        $mock->shouldReceive('update')->with($modelMock, $attributes);

        $registry = new EloquentRegistry($this->app, $mock);

        $registry->update(1, $attributes);
    }

    /**
     * @covers \TwoDojo\ModuleManager\Registries\EloquentRegistry::update()
     */
    public function testUpdateReturnFalseIfModelNotExists()
    {
        $attributes = [
            'test_key_1' => 'test_value',
            'test_key_2' => 'test_value_2'
        ];

        $mock = m::mock(EloquentRepository::class);
        $mock->shouldReceive('setModel')->withAnyArgs();
        $mock->shouldReceive('find')->with(1)->andReturn(null);

        $registry = new EloquentRegistry($this->app, $mock);

        $this->assertFalse($registry->update(1, $attributes));
    }

    /**
     * @covers \TwoDojo\ModuleManager\Registries\EloquentRegistry::find()
     * @covers \TwoDojo\ModuleManager\Registries\EloquentRegistry::mergeDescriptor()
     */
    public function testCanFind()
    {
        $attributes = [
            'test_key_1' => 'test_value',
            'test_key_2' => 'test_value_2'
        ];

        $mock = m::mock(EloquentRepository::class);
        $mock->shouldReceive('setModel')->withAnyArgs();
        $mock->shouldReceive('find')->with(1)->andReturn(new ModelStub($attributes));

        $registry = new EloquentRegistry($this->app, $mock);

        $descriptor = $registry->find(1)->first();

        $this->assertEquals($attributes, $descriptor->getAttributes());
    }

    /**
     * @covers \TwoDojo\ModuleManager\Registries\EloquentRegistry::mergeDescriptor()
     */
    public function testMergeDescriptorReturnEmptyCollectionIfModelNotExists()
    {
        $mock = m::mock(EloquentRepository::class);
        $mock->shouldReceive('setModel')->withAnyArgs();
        $mock->shouldReceive('find')->with(1)->andReturn(null);

        $registry = new EloquentRegistry($this->app, $mock);

        $collection = $registry->find(1);

        $this->assertEquals(0, $collection->count());
    }

    /**
     * @covers \TwoDojo\ModuleManager\Registries\EloquentRegistry::findByUniqueName()
     * @covers \TwoDojo\ModuleManager\Registries\EloquentRegistry::mergeDescriptor()
     */
    public function testCanFindByUniqueName()
    {
        $attributes = [
            'test_key_1' => 'test_value',
            'test_key_2' => 'test_value_2'
        ];

        $mock = m::mock(EloquentRepository::class);
        $mock->shouldReceive('setModel')->withAnyArgs();
        $mock->shouldReceive('findByField')->with('uniqueName', 'test_name')->andReturn(new ModelStub($attributes));

        $registry = new EloquentRegistry($this->app, $mock);

        $descriptor = $registry->findByUniqueName('test_name')->first();

        $this->assertEquals($attributes, $descriptor->getAttributes());
    }

    /**
     * @covers \TwoDojo\ModuleManager\Registries\EloquentRegistry::findByField()
     * @covers \TwoDojo\ModuleManager\Registries\EloquentRegistry::mergeDescriptor()
     */
    public function testCanFindByField()
    {
        $attributes = [
            'test_key_1' => 'test_value',
            'test_key_2' => 'test_value_2'
        ];

        $mock = m::mock(EloquentRepository::class);
        $mock->shouldReceive('setModel')->withAnyArgs();
        $mock->shouldReceive('findByField')->with('uniqueName', 'test_name')->andReturn(new ModelStub($attributes));

        $registry = new EloquentRegistry($this->app, $mock);

        $descriptor = $registry->findByField('uniqueName', 'test_name')->first();

        $this->assertEquals($attributes, $descriptor->getAttributes());
    }

    /**
     * @covers \TwoDojo\ModuleManager\Registries\EloquentRegistry::all()
     * @covers \TwoDojo\ModuleManager\Registries\EloquentRegistry::mergeDescriptor()
     */
    public function testCanGetAll()
    {
        $collection = new Collection([new ModelStub([
            'test_key_1' => 'test_value',
            'test_key_2' => 'test_value_2'
        ]), new ModelStub([
            'test_key_1' => 'test_value_2',
            'test_key_2' => 'test_value_2_2'
        ])]);

        $mock = m::mock(EloquentRepository::class);
        $mock->shouldReceive('setModel')->withAnyArgs();
        $mock->shouldReceive('all')->withNoArgs()->andReturn($collection);

        $registry = new EloquentRegistry($this->app, $mock);

        $results = $registry->all();

        $this->assertEquals($results->count(), $collection->count());
    }

    /**
     * @covers \TwoDojo\ModuleManager\Registries\EloquentRegistry::scope()
     */
    public function testCanRegisterScope()
    {
        $attributes = [1, null, ['data']];

        $mock = m::mock(EloquentRepository::class);
        $mock->shouldReceive('setModel')->withAnyArgs();
        $mock->shouldReceive('someScope')->with(...$attributes);

        $registry = new EloquentRegistry($this->app, $mock);

        $registry->someScope(...$attributes);
    }
}
