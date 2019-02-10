<?php

namespace Ink\Providers;

use Ink\Support;
use Ink\Aliases\Alias;
use Ink\Aliases\AliasLoader;
use Ink\Foundation\ServiceProvider;
use Ink\Container\ContainerProxy as Container;

class AliasProvider extends ServiceProvider
{
    /**
     * Boots the service provider
     *
     * @param Router $router
     * @return void
     */
    public function boot(Container $container)
    {
        $aliases = Support::config('aliases');

        Alias::setAliasContainer(Container::getInstance());
        AliasLoader::getInstance($aliases)->register();
    }
}