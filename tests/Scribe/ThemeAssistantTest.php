<?php

namespace Ink\Tests\Scribe;

use Ink\Contracts\Config\Repository;
use Ink\Foundation\Theme;
use Ink\Scribe\ThemeAssistant;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

class ThemeAssistantTest extends TestCase
{

    /**
     * Stamp theme instance
     *
     * @var Theme
     */
    protected $theme;

    /**
     * Filesystem utility class
     *
     * @var Filesystem
     */
    protected $fs;

    /**
     * Theme assistant instance
     *
     * @var ThemeAssistant
     */
    protected $assistant;

    /**
     * Prepare env for the test case
     *
     * @return void
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    protected function setUp(): void
    {
        $this->theme = new Theme(__DIR__ . "/theme");
        $this->fs = new Filesystem();
        $this->assistant = new ThemeAssistant($this->theme, $this->fs, $this->theme['config']);

        parent::setUp();
    }

    /**
     * Tear down the test, and remove generated files
     *
     * @return void
     */
    public function tearDown(): void
    {
        $entities  = [
            "config/foo.php",
            "config/fooconfig.php",
            "StubCommand.php",
            "custom"
        ];

        $entities = array_map(
            function ($path) {
                return $this->theme->basePath($path);
            }, $entities
        );

        $this->fs->remove($entities);

        parent::tearDown();
    }

    /**
     * Test if config can be copied raw or with custom name
     *
     * @return void;
     */
    public function testConfigIsPublishedWithCorrectNames()
    {
        $configCustomName = "foo";
        $configSource = __DIR__ . "/fooconfig.php";
        $configDest = __DIR__ . "/theme/config";

        $this->assistant->publishConfig($configSource);
        $this->assistant->publishConfig($configSource, $configCustomName);

        $this->assertTrue(
            $this->fs->exists("{$configDest}/{$configCustomName}.php")
        );
        $this->assertTrue(
            $this->fs->exists("{$configDest}/fooconfig.php")
        );

        $this->expectException(FileNotFoundException::class);
        $this->assistant->publishConfig(__DIR__ . '/nonExistentFile');
    }

    /**
     * Test if files are published to any location insde theme
     *
     * @return void
     */
    public function testCustomResourceIsPublished()
    {
        $this->assistant->publishResource(__DIR__ . "/StubCommand.php");
        $this->assistant->publishResource(__DIR__ . "/StubCommand.php", "custom");

        $this->assertTrue(
            $this->fs->exists(
                $this->theme->basePath("StubCommand.php")
            )
        );
        $this->assertTrue(
            $this->fs->exists(
                $this->theme->basePath("custom/StubCommand.php")
            )
        );

        $this->expectException(FileNotFoundException::class);
        $this->assistant->publishResource(__DIR__ . '/nonExistentFile');
    }

    /**
     * Ensure that theme helper can inject theme aliases
     *
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     *
     * @return void
     */
    public function testCustomAliasesArePublishedInsideConfig()
    {
        $fooMock = \Mockery::mock('foo');
        $barMock = \Mockery::mock('bar');
        $overwriteMock = $fooMock;

        $baseAliases = [
            'Foo' => $fooMock,
            'Bar' => $barMock,
            'Overwrite' => $overwriteMock
        ];

        /**
         * Theme config
         *
         * @var Repository $config
        */
        $config = $this->theme->container()->get('config');

        $config->set('aliases', $baseAliases);
        $this->assistant->registerAliases(
            [
            'Overwrite' => $barMock,
            'Foo' => $barMock
            ]
        );

        $this->assertSame($config->get('aliases')['Overwrite'], $barMock);
        $this->assertSame($config->get('aliases')['Foo'], $barMock);
    }

    /**
     * Ensure that theme helper can inject theme providers
     *
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     *
     * @return void
     */
    public function testCustomProvidersArePublishedInsideConfig()
    {
        $fooMock = \Mockery::mock('foo');
        $barMock = \Mockery::mock('bar');
        $overwriteMock = $fooMock;

        $baseAliases = [
            'Foo' => $fooMock,
            'Bar' => $barMock,
            'Overwrite' => $overwriteMock
        ];

        /**
         * Theme config
         *
         * @var Repository $config
         */
        $config = $this->theme->container()->get('config');

        $config->set('theme.providers', $baseAliases);
        $this->assistant->registerProviders(
            [
            'Overwrite' => $barMock,
            'Foo' => $barMock
            ]
        );

        $this->assertSame($config->get('theme.providers')['Overwrite'], $barMock);
        $this->assertSame($config->get('theme.providers')['Foo'], $barMock);
    }
}
