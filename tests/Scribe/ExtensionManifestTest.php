<?php

namespace Ink\Tests\Scribe;

use Ink\Foundation\Theme;
use Ink\Scribe\ExtensionManifest;
use PHPUnit\Framework\TestCase;

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
     * Prepare env for the test case
     *
     * @return void
     */
    protected function setUp()
    {
        $this->theme = new Theme(__DIR__ . "/theme");
        $this->manifest = new ExtensionManifest();

        parent::setUp();
    }

    /**
     * Test if extension resources specified in composer
     * installed file are loaded into respective arrays
     *
     * @return void
     */
    public function testManifestLoadsProperly()
    {
        $this->manifest->loadFrom($this->theme->basePath("stamp-manifest.json"));

        $this->assertCount(2, $this->manifest->commands());
        $this->assertCount(2, $this->manifest->resources());
    }

}