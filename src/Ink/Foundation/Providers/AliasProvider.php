<?php

namespace Ink\Foundation\Providers;

use Ink\Aliases\Alias;
use Ink\Aliases\AliasLoader;
use Ink\Providers\ServiceProvider;

class AliasProvider extends ServiceProvider
{
    /**
     * Boots the service provider
     *
     * @param Router $router
     * @return void
     */
    public function boot()
    {
        $aliases = config('aliases');

        Alias::setAliasContainer($this->theme->get('container'));
        AliasLoader::getInstance($aliases)->register();
    }
}