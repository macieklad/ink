<?php

namespace Ink\Providers;

use Ink\Routing\Router;
use Ink\Foundation\ServiceProvider;

class RoutingProvider extends ServiceProvider
{
    /**
     * Boots the service provider
     *
     * @param Router $router
     * @return void
     */
    public function boot(Router $router)
    {
        $router->loadRoutes(
            $this->theme->basePath('src/routes.php')
        );
    }
}