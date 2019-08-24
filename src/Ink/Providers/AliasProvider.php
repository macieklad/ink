<?php

namespace Ink\Providers;

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
     * @param Container  $container
     * @param Repository $config
     * 
     * @return void
     */
    public function boot(Container $container, Repository $config): void
    {
        $aliases = $config->get('aliases', []);

        Alias::setAliasContainer($container);
        AliasLoader::getInstance($aliases)->register();
    }
}
