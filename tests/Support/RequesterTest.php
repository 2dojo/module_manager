<?php

namespace TwoDojo\Test\ModuleManager\Support;

use TwoDojo\ModuleManager\Support\Requester;
use TwoDojo\Test\ModuleManager\TestCase;

class RequesterTest extends TestCase
{
    /**
     * @covers \TwoDojo\ModuleManager\Support\Requester::make()
     */
    public function testCanMake()
    {
        $this->assertInstanceOf(Requester::class, Requester::make());
    }

    /**
     * @covers \TwoDojo\ModuleManager\Support\Requester::setBasePath()
     */
    public function testCanSetBasePath()
    {
        $requester = Requester::make();
        $requester->setBasePath('test_path');

        $this->assertAttributeEquals('test_path'.DIRECTORY_SEPARATOR, 'basePath', $requester);
    }

    /**
     * @covers \TwoDojo\ModuleManager\Support\Requester::exists()
     */
    public function testExists()
    {
        $requester = Requester::make();
        $requester->setBasePath(__DIR__);

        $this->assertTrue($requester->exists('RequesterTest.php'));
    }

    /**
     * @covers \TwoDojo\ModuleManager\Support\Requester::path()
     */
    public function testCanGetPath()
    {
        $requester = Requester::make();
        $requester->setBasePath(__DIR__);

        $this->assertEquals(realpath(__DIR__.'/RequesterTest.php'), $requester->path('RequesterTest.php'));
    }

    /**
     * @covers \TwoDojo\ModuleManager\Support\Requester::getFiles()
     */
    public function testCanGetFiles()
    {
        $requester = Requester::make();
        $requester->setBasePath(__DIR__);

        $expectedFiles = [];

        foreach (new \DirectoryIterator(__DIR__) as $file) {
            if ($file->isFile()) {
                $expectedFiles[] = $file->getFileInfo();
            }
        }

        $files = $requester->getFiles('');

        $this->assertCount(count($expectedFiles), $files);
    }
}
