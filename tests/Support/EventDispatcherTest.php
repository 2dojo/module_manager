<?php

namespace TwoDojo\Test\ModuleManager\Support;

use TwoDojo\Module\Contracts\EventListener;
use TwoDojo\ModuleManager\Support\EventDispatcher;
use TwoDojo\Test\ModuleManager\TestCase;
use Mockery as m;

class EventDispatcherTest extends TestCase
{
    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /**
     * @covers \TwoDojo\ModuleManager\Support\EventDispatcher::registerListener()
     */
    public function testCanRegisterListener()
    {
        $dispatcher = new EventDispatcher();

        $dispatcher->registerListener(m::mock(EventListener::class));
    }

    /**
     * @covers \TwoDojo\ModuleManager\Support\EventDispatcher::dispatchEvent()
     */
    public function testCanDispatchEvent()
    {
        $arguments = [true, null, 2];

        $dispatcher = new EventDispatcher();

        $mock = m::mock(EventListener::class);
        $mock->shouldReceive('getEventGroup')->andReturn('test');
        $mock->shouldReceive('onEventReceived')->with('testEvent', $arguments);

        $dispatcher->registerListener($mock);

        $dispatcher->dispatchEvent('test', 'testEvent', $arguments);
    }
}
