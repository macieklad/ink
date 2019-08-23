<?php

use DI\DependencyException;
use DI\NotFoundException;
use Ink\Contracts\Foundation\Theme as ThemeContract;
use Ink\Foundation\Bootstrap\HandleErrors;
use Ink\Foundation\Bootstrap\HookExtensions;
use Ink\Foundation\Bootstrap\LoadConfiguration;
use Ink\Foundation\Bootstrap\LoadServices;
use Ink\Foundation\Kernel;
use Ink\Foundation\Theme;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Container\ContainerInterface;

class ThemeTest extends MockeryTestCase
{
    /**
     * Ensure that theme registers core class aliases
     *
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testThemeIsRegisteringBaseAliases()
    {
        $theme = new Theme;
        
        $this->assertSame(
            $theme, 
            $theme->container()->get(ThemeContract::class)
        );
        $this->assertSame(
            $theme->container(),
            $theme->container()->get(ContainerInterface::class)
        );
        $this->assertTrue($theme['kernel'] instanceof Kernel);
    }

    /**
     * Test all basic, array like operations on theme object
     *
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testThemeIsArrayLikeStructure()
    {
        $theme = new Theme;
        $mock = Mockery::mock(stdClass::class);

        $theme['mock'] = $mock;
        unset($theme['foo']);

        $this->assertNull($theme['foo']);
        $this->assertSame($mock, $theme['mock']);
        $this->assertSame($theme['theme'], $theme);
        $this->assertSame($theme['container'], $theme->container());
        $this->assertTrue(isset($theme['kernel']));

    }

    /**
     * Test if theme returns correct directory paths
     *
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBasePathsAreInitializedProperly() 
    {
        $base = 'foo/bar';
        $config = 'foo/bar' . DIRECTORY_SEPARATOR . 'config';
        $theme = new Theme($base);
        $generatedBase = $base . DIRECTORY_SEPARATOR . 'baz';
        $generatedConfig = $config . DIRECTORY_SEPARATOR . 'baz';

        $this->assertSame($generatedBase, $theme->basePath('baz'));
        $this->assertSame($generatedConfig, $theme->configPath('baz'));
        $this->assertSame($base, $theme['path.base']);
        $this->assertSame($config, $theme['path.config']);
    }

    /**
     * Ensure that theme executes kernel commands while bootstrapping
     *
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testThemeIsBootstrapingKernelWithCommands()
    {
        $kernel = Mockery::mock(Kernel::class);
        $theme = new Theme;

        $theme['kernel'] = $kernel;

        $kernel->shouldReceive('executeCommands')
            ->with(
                [
                    LoadConfiguration::class,
                    HandleErrors::class,
                    HookExtensions::class,
                    LoadServices::class
                ]
            )
            ->once();

        $theme->bootstrap();
    }

}
