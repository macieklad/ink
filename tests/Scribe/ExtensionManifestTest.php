<?php

namespace Ink\Tests\Scribe;

use Ink\Foundation\Theme;
use Ink\Scribe\ExtensionManifest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class ExtensionManifestTest extends TestCase
{

    /**
     * Stamp theme instance
     *
     * @var Theme
     */
    protected $theme;

    /**
     * ExtensionManifest instance
     *
     * @var ExtensionManifest
     */
    protected $manifest;

    /**
     * Filesystem utility class
     *
     * @var Filesystem
     */
    protected $fs;

    /**
     * Manifest location
     *
     * @var string
     */
    protected $manifestName = "stamp-manifest.json";

    /**
     * Prepare env for the test case
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->theme = new Theme(__DIR__ . "/theme");
        $this->manifest = new ExtensionManifest();
        $this->fs = new Filesystem();

        parent::setUp();
    }

    /**
     * Tear down the tests
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $manifest = $this->theme->vendorPath($this->manifestName);

        if ($this->fs->exists($manifest)) {
            $this->fs->remove($manifest);
        }

        parent::tearDown();
    }

    /**
     * Test if extension resources specified in composer
     * installed file are loaded into respective arrays
     *
     * @return void
     */
    public function testManifestLoadsProperly()
    {
        $manifestPath = $this->theme->vendorPath($this->manifestName);
        $contents = [
            "commands" => [
                "StubCommand",
                "StubCommand",
            ],
            "resources" => [
                "StubResource",
                "StubResource"
            ]
        ];

        $this->fs->dumpFile($manifestPath, json_encode($contents));
        $this->manifest->loadFrom($manifestPath);

        $this->assertCount(2, $this->manifest->commands());
        $this->assertCount(2, $this->manifest->resources());
    }
    /**
     * Test if adding parsed extension array to manifest
     * resolves to correct fields in the class
     *
     * @return void
     */
    public function testExtensionsAreAddedToManifest()
    {
        $this->manifest->addExtension(
            [
            "commands" => [
                "StubCommand"
            ],
            "resources" => [
                "StubResource"
            ]
            ]
        );

        $this->assertEquals(["StubCommand"], $this->manifest->commands());
        $this->assertEquals(["StubResource"], $this->manifest->resources());
    }

    /**
     * Check if manifest can write itself to a file,
     * and has correct JSON scheme
     *
     * @return void
     */
    public function testExtensionManifestWritesFile()
    {
        $manifest = __DIR__ . "/{$this->manifestName}";
        $extension = [
            "commands" => [
                "StubCommand"
            ],
            "resources" => [
                "StubResource"
            ]
        ];

        $this->manifest->addExtension($extension);
        $this->manifest->write(__DIR__);

        $this->assertTrue(
            $this->fs->exists($manifest)
        );

        $this->assertEquals(
            json_encode($extension),
            file_get_contents($manifest)
        );
    }

}
