<?php

namespace Ink\Providers;

use Ink\Support;
use DI\Container;
use Ink\Aliases\Alias;
use Ink\Config\Repository;
use Ink\Aliases\AliasLoader;
use Ink\Foundation\ServiceProvider;

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