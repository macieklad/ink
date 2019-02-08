<?php

namespace Stamp\Providers;

use Stamp\Http\Router;
use Stamp\Providers\Provider;

class RoutingProvider extends Provider
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
            join_paths(theme_root(), 'app/routes.php')
        );
    }
}