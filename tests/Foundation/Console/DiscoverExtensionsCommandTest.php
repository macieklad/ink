<?php

namespace Tests\Foundation\Console;

use Ink\Foundation\Theme;
use Ink\Foundation\Console\DiscoverExtensionsCommand;
use Ink\Scribe\ExtensionManifest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class DiscoverExtensionsCommandTest extends TestCase
{
    /**
     * Stamp theme instance
     *
     * @var \Ink\Foundation\Theme
     */
    protected $theme;

    /**
     * Installed.json file mocker
     *
     * @var InstalledMocker
     */
    protected $installed;

    /**
     * ExtensionManifest instance
     *
     * @var ExtensionManifest
     */
    protected $manifest;

    /**
     * Set up the test env.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->theme = new Theme(__DIR__);
        $this->installed = new InstalledMocker();
        $this->manifest = $this->theme->container()->get(ExtensionManifest::class);

        $this->installed->write();

        parent::setUp();
    }

    /**
     * Tear down the test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->installed->clean();

        parent::tearDown();
    }


    /**
     * Test if the scribe package discovery works
     *
     * @return void
     */
    public function testExtensionDiscoveryGeneratesManifest(): void
    {
        $command = $this->theme->container()->get(DiscoverExtensionsCommand::class);

        $tester = new CommandTester($command);
        $tester->execute([]);

        $this->manifest->loadFrom($this->theme->vendorPath("stamp-manifest.json"));



        $this->assertSame(
            $this->installed->field("commands"),
            $this->manifest->commands()
        );
        $this->assertEquals(0, $tester->getStatusCode());
    }

}
