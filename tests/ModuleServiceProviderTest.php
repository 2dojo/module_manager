<?php

namespace TwoDojo\Test\ModuleManager;

use TwoDojo\ModuleManager\ModuleManagerServiceProvider;
use TwoDojo\ModuleManager\ModuleManager;
use Mockery as m;
use TwoDojo\ModuleManager\Repositories\ModuleRegistryRepository;

class ModuleServiceProviderTest extends TestCase
{
    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /**
     * @covers \TwoDojo\ModuleManager\ModuleManagerServiceProvider::provides()
     */
    public function testProvidesReturnsModuleManager()
    {
        $mock = m::mock(\Illuminate\Contracts\Foundation\Application::class);
        $provider = new ModuleManagerServiceProvider($mock);

        $provides = $provider->provides();

        $this->assertEquals([ModuleManager::class], $provides);
    }

    /**
     * @covers \TwoDojo\ModuleManager\ModuleManagerServiceProvider::boot()
     */
    public function testCanBoot()
    {
        $mock = m::mock(ModuleManagerServiceProvider::class);
        $mock->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('boot')->passthru();

        $mock->shouldReceive('publishes')->withAnyArgs();
        $mock->shouldReceive('loadMigrationsFrom')->withAnyArgs();

        $mock->boot();
    }

    /**
     * @covers \TwoDojo\ModuleManager\ModuleManagerServiceProvider::register()
     */
    public function testCanRegisterModuleManager()
    {
        $configMock = m::mock();
        $mock = m::mock(\ArrayAccess::class);

        $mock->shouldReceive('offsetGet')->zeroOrMoreTimes()->with('config')->andReturn($configMock);

        $configMock->shouldReceive('get')->withAnyArgs()->once()->andReturn([]);

        $configMock->shouldReceive('set')->withAnyArgs()->once()->andReturnUndefined();

        $closureMock = m::mock();

        $closureMock->shouldReceive('make')->withAnyArgs()->andReturn($this->app->make(ModuleRegistryRepository::class));

        $resolvedClass = null;

        $mock->shouldReceive('singleton')->with(ModuleManager::class, m::on(function ($closure) use ($closureMock, &$resolvedClass) {
            $resolvedClass = $closure($closureMock);

            return true;
        }));

        $provider = new ModuleManagerServiceProvider($mock);

        $provider->register();

        $closureMock->shouldHaveReceived('make');

        $this->assertInstanceOf(ModuleManager::class, $resolvedClass);
    }
}
