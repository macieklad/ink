<?php

namespace Tests\Foundation;

use DI\DependencyException;
use DI\NotFoundException;
use Ink\Contracts\Foundation\Theme;
use Ink\Foundation\Bootstrap\HookExtensions;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Tests\Foundation\Console\InstalledMocker;
use Tests\Foundation\Stub\BootStub;
use Tests\Scribe\StubAlias;

/**
 * Class HookExtensionsCommandTest
 *
 * @package Tests\Foundation
 */
class HookExtensionsCommandTest extends MockeryTestCase
{
    /**
     * Theme instance
     *
     * @var Theme
     */
    protected $theme;

    /**
     * Installed mocker instance,
     * replaces vendor directory
     *
     * @var InstalledMocker
     */
    protected $vendor;

    /**
     * Tested command
     *
     * @var HookExtensions
     */
    protected $command;

    /**
     * Set up the test
     *
     * @throws DependencyException
     * @throws NotFoundException
     *
     * @return void
     */
    protected function setUp()
    {
        $this->theme = new \Ink\Foundation\Theme(__DIR__);
        $this->command = $this->theme->container()->get(HookExtensions::class);
        $this->vendor = new InstalledMocker(__DIR__);
    }

    /**
     * Tear down the test
     *
     * @return void
     */
    protected function tearDown()
    {
        $this->vendor->clean();
    }

    /**
     * Test if extension hook adds the requested
     * functionality, such as providers.
     *
     * @throws DependencyException
     * @throws NotFoundException
     *
     * @return void
     */
    public function testExtensionIsLoadedCorrectly()
    {
        $this->vendor->writeManifest();
        $this->command->fire();

        $config = $this->theme->container()->get('config');

        $this->assertTrue(
            in_array(
                BootStub::class,
                $config->get('theme.providers', [])
            )
        );

        $this->assertTrue(
            in_array(
                StubAlias::class,
                $config->get('aliases', [])
            )
        );
    }

}