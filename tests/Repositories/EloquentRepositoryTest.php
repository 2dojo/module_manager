<?php

namespace TwoDojo\Test\ModuleManager\Repositories;

use Illuminate\Database\Eloquent\Model;
use TwoDojo\ModuleManager\Repositories\EloquentRepository;
use TwoDojo\Test\ModuleManager\Stubs\EloquentRepositoryStub;
use TwoDojo\Test\ModuleManager\Stubs\FakeModel;
use TwoDojo\Test\ModuleManager\Stubs\ModelStub;
use TwoDojo\Test\ModuleManager\TestCase;
use Mockery as m;

class EloquentRepositoryTest extends TestCase
{
    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /**
     * @covers \TwoDojo\ModuleManager\Repositories\EloquentRepository::__construct()
     * @covers \TwoDojo\ModuleManager\Repositories\EloquentRepository::setModel()
     * @covers \TwoDojo\ModuleManager\Repositories\EloquentRepository::getModel()
     */
    public function testCanConstructWithModel()
    {
        $repository = new EloquentRepository(new ModelStub());

        $this->assertInstanceOf(ModelStub::class, $repository->getModel());
    }

    /**
     * @covers \TwoDojo\ModuleManager\Repositories\EloquentRepository::save()
     */
    public function testCanSave()
    {
        $repository = new EloquentRepository(new FakeModel());

        $attributes = ['test_key' => 'test_value'];
        $model = $repository->save($attributes);

        $this->assertEquals($attributes, $model->toArray());
        $this->assertTrue($model->saved);
    }

    /**
     * @covers \TwoDojo\ModuleManager\Repositories\EloquentRepository::update()
     */
    public function testCanUpdate()
    {
        $attributes = ['test_key' => 'test_value'];

        $modelMock = m::mock(Model::class);
        $modelMock->shouldReceive('find')->with(1)->andReturnSelf();
        $modelMock->shouldReceive('update')->with($attributes)->andReturnSelf();

        $repository = new EloquentRepository($modelMock);

        $repository->update(1, $attributes);
    }

    /**
     * @covers \TwoDojo\ModuleManager\Repositories\EloquentRepository::update()
     */
    public function testCanUpdateModel()
    {
        $attributes = ['test_key' => 'test_value'];
        $modelMock = m::mock(Model::class);
        $modelMock->shouldReceive('update')->with($attributes);

        $repository = new EloquentRepository();

        $repository->update($modelMock, $attributes);
    }

    /**
     * @covers \TwoDojo\ModuleManager\Repositories\EloquentRepository::find()
     */
    public function testCanFind()
    {
        $modelMock = m::mock(Model::class);
        $modelMock->shouldReceive('find')->with(1);
        $repository = new EloquentRepository($modelMock);

        $repository->find(1);
    }

    /**
     * @covers \TwoDojo\ModuleManager\Repositories\EloquentRepository::findByField()
     */
    public function testCanFindByField()
    {
        $modelMock = m::mock(Model::class);
        $modelMock->shouldReceive('where')->with('field_name', 'field_value')->andReturnSelf();
        $modelMock->shouldReceive('get')->withNoArgs();
        $repository = new EloquentRepository($modelMock);

        $repository->findByField('field_name', 'field_value');
    }

    /**
     * @covers \TwoDojo\ModuleManager\Repositories\EloquentRepository::all()
     */
    public function testCanGetAll()
    {
        $modelMock = m::mock(Model::class);
        $modelMock->shouldReceive('get')->withNoArgs();
        $repository = new EloquentRepository($modelMock);

        $repository->all();
    }

    /**
     * @covers \TwoDojo\ModuleManager\Repositories\EloquentRepository::__call()
     */
    public function testCanRegisterScope()
    {
        $parameters = [1, ['array'], null];
        $repository = new EloquentRepositoryStub();
        $repository->someScope(...$parameters);

        $this->assertCount(1, $repository->getScopes());
        $this->assertEquals($parameters, $repository->getScopes()[0][1]);
    }

    /**
     * @covers \TwoDojo\ModuleManager\Repositories\EloquentRepository::applyScopes()
     */
    public function testCanApplyScopes()
    {
        $parameters = [1, ['array'], null];
        $parameters2 = [['array'], ['array'], null, 18];

        $modelMock = m::mock(Model::class);
        $modelMock->shouldReceive('find')->with(1)->andReturn(null);
        $modelMock->shouldReceive('someScope')->with(...$parameters)->andReturnSelf();
        $modelMock->shouldReceive('someScope2')->with(...$parameters2)->andReturnSelf();

        $repository = new EloquentRepositoryStub($modelMock);
        $repository->someScope(...$parameters);
        $repository->someScope2(...$parameters2);

        $repository->find(1);

        $modelMock->shouldHaveReceived('someScope');
        $modelMock->shouldHaveReceived('someScope2');
    }
}
