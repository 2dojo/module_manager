<?php

namespace TwoDojo\Test\ModuleManager\Models;

use TwoDojo\ModuleManager\Models\Module;
use TwoDojo\Test\ModuleManager\TestCase;
use Mockery as m;

class ModuleTest extends TestCase
{
    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /**
     * @covers \TwoDojo\ModuleManager\Models\Module::scopeEnabled()
     */
    public function testEnabledScope()
    {
        $model = new Module();

        $mock = m::mock();
        $mock->shouldReceive('whereIsEnabled')->with(1)->once();

        $model->scopeEnabled($mock);

        $mock->shouldHaveReceived('whereIsEnabled')->once();
    }
}
