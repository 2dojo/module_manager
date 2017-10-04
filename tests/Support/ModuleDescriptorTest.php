<?php

namespace TwoDojo\Test\ModuleManager\Support;

use TwoDojo\ModuleManager\Support\ModuleDescriptor;
use TwoDojo\Test\ModuleManager\TestCase;

class ModuleDescriptorTest extends TestCase
{
    /**
     * @covers \TwoDojo\ModuleManager\Support\ModuleDescriptor::__construct()
     * @covers \TwoDojo\ModuleManager\Support\ModuleDescriptor::getAttribute()
     * @covers \TwoDojo\ModuleManager\Support\ModuleDescriptor::setAttribute()
     * @covers \TwoDojo\ModuleManager\Support\ModuleDescriptor::hasAttribute()
     */
    public function testCanConstructWithAttributes()
    {
        $descriptor = new ModuleDescriptor(['test_key' => 'test_value']);

        $this->assertTrue($descriptor->hasAttribute('test_key'));
        $this->assertEquals('test_value', $descriptor->getAttribute('test_key'));
    }

    /**
     * @covers \TwoDojo\ModuleManager\Support\ModuleDescriptor::getAttributes()
     */
    public function testCanGetAllAttributes()
    {
        $descriptor = new ModuleDescriptor(['test_key' => 'test_value', 'test_key2' => 'test_value2']);

        $this->assertCount(2, $descriptor->getAttributes());
    }

    public function testMagicSetterAndGetter()
    {
        $descriptor = new ModuleDescriptor();
        $descriptor->test_key = 'test_value';
        $descriptor->test_key2 = 'test_value2';

        $this->assertCount(2, $descriptor->getAttributes());
        $this->assertEquals('test_value', $descriptor->test_key);
        $this->assertEquals('test_value2', $descriptor->test_key2);
    }

    /**
     * @covers \TwoDojo\ModuleManager\Support\ModuleDescriptor::offsetExists()
     * @covers \TwoDojo\ModuleManager\Support\ModuleDescriptor::offsetGet()
     * @covers \TwoDojo\ModuleManager\Support\ModuleDescriptor::offsetSet()
     * @covers \TwoDojo\ModuleManager\Support\ModuleDescriptor::offsetUnset()
     */
    public function testArrayAccess()
    {
        $descriptor = new ModuleDescriptor();
        $descriptor['test_key'] = 'test_value';
        $descriptor['test_key2'] = 'test_value2';
        $descriptor['test_key3'] = 'test_value3';

        unset($descriptor['test_key3']);

        $this->assertEquals('test_value', $descriptor['test_key']);
        $this->assertEquals('test_value2', $descriptor['test_key2']);
        $this->assertTrue(isset($descriptor['test_key']));
        $this->assertFalse(isset($descriptor['test_key3']));
    }
}
