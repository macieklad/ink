<?php

namespace Tests\Aliases;

use Ink\Aliases\Alias;
use Ink\Aliases\Route;
use Ink\Aliases\Action;
use Ink\Aliases\Config;
use Ink\Aliases\Filter;
use Ink\Routing\Router;
use Ink\Foundation\Theme;
use Ink\Config\Repository;
use Ink\Aliases\AliasLoader;
use Ink\Aliases\Theme as ThemeAlias;
use Ink\Hooks\ActionManager;
use Ink\Hooks\FilterManager;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Ink\Contracts\Foundation\Theme as ThemeContract;

class StampAliasesTest extends MockeryTestCase
{
    /**
     * Set up the test
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->theme = new Theme;
    }

    /**
     * Ensure that aliases bind correct class implementations
     *
     * @return void
     */
    public function testThemeAliasesResolveToProperClasses()
    {
        Alias::setAliasContainer($this->theme->container());

        $this->assertTrue(Route::getAliasRoot() instanceof Router); 
        $this->assertTrue(Config::getAliasRoot() instanceof Repository);
        $this->assertTrue(ThemeAlias::getAliasRoot() instanceof Theme);
        $this->assertTrue(Action::getAliasRoot() instanceof ActionManager);
        $this->assertTrue(Filter::getAliasRoot() instanceof FilterManager);
    }
}
