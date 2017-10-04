<?php

namespace TwoDojo\Test\ModuleManager\Registries;

use TwoDojo\Test\ModuleManager\Stubs\RegistryStub;
use TwoDojo\Test\ModuleManager\TestCase;

class BaseRegistryTest extends TestCase
{
    /**
     * @covers \TwoDojo\ModuleManager\Registries\BaseRegistry
     */
    public function testScopeCallable()
    {
        $registry = new RegistryStub();

        $registry->someScope(1, 8, 10);
        $registry->someScope2(['data'], null);

        $scopes = $registry->scopes;

        $this->assertCount(2, $scopes);
        $this->assertArrayHasKey('someScope', $scopes);
        $this->assertArrayHasKey('someScope2', $scopes);

        $this->assertEquals(1, $scopes['someScope'][0]);
        $this->assertEquals(8, $scopes['someScope'][1]);
        $this->assertEquals(10, $scopes['someScope'][2]);

        $this->assertEquals('data', $scopes['someScope2'][0][0]);
        $this->assertNull($scopes['someScope2'][1]);
    }
}
