<?php

namespace Ink\Providers;

use Ink\Support;
use Ink\Aliases\Alias;
use Ink\Aliases\AliasLoader;
use Ink\Foundation\ServiceProvider;
use Ink\Contracts\Config\Repository;
use Psr\Container\ContainerInterface as Container;

class AliasProvider extends ServiceProvider
{
    /**
     * Boots the service provider
     *
     * @return void
     */
    public function boot(Container $container, Repository $config)
    {
        $aliases = $config->get('aliases', []);

        Alias::setAliasContainer($container);
        AliasLoader::getInstance($aliases)->register();
    }
}