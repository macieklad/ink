<?php

namespace Ink\Tests\Scribe;

use Ink\Scribe\Cli;
use Ink\Foundation\Theme;
use Psr\Log\LoggerInterface;
use Tests\Scribe\StubCommand;
use Ink\Contracts\Scribe\ExtensionManifest;
use Symfony\Component\Filesystem\Filesystem;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Symfony\Component\Console\Tester\ApplicationTester;

class CliTest extends MockeryTestCase
{
    /**
     * Stamp theme instance
     *
     * @var Theme
     */
    protected $theme;

    /**
     * Stamp extension manifest instance
     *
     * @var ExtensionManifest
     */
    protected $manifest;

    /**
     * Filesystem utility instance
     *
     * @var Filesystem
     */
    protected $fs;

    /**
     * Set up the cli test, by providing all components
     *
     * @return void
     */
    protected function setUp(): void
    {
        $theme = new Theme(__DIR__ . "/theme");
        $manifest = $theme->container()->get(ExtensionManifest::class);
        $fs = new Filesystem();

        $manifest->addExtension(
            [
            "commands" => [
                "NonExistentCommand",
                StubCommand::class
            ]
            ]
        );

        $manifest->write($theme->vendorPath());

        $this->manifest = $manifest;
        $this->theme = $theme;
        $this->fs = $fs;

        parent::setUp();
    }


    /**
     * Tear down the test
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->fs->remove($this->theme->vendorPath("stamp-manifest.json"));

        parent::tearDown();
    }


    /**
     * Test if the cli starts without problems
     *
     * @return void
     */
    public function testCliBootsCorrectly()
    {
        $logger = \Mockery::mock(LoggerInterface::class);
        $this->theme->container()->set(LoggerInterface::class, $logger);
        $cli = $this->theme->container()->get(Cli::class);
        $tester = new ApplicationTester($cli);

        $logger->shouldReceive('warning')
            ->times(1);


        $cli->setAutoExit(false);
        $cli->prepare();
        $tester->run([]);

        $this->assertEquals(0, $tester->getStatusCode());
    }

}
