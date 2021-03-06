<?php

namespace Ink\Providers;

use Ink\Contracts\Routing\Router;
use Ink\Foundation\ServiceProvider;
use Ink\Contracts\Config\Repository;

class RoutingProvider extends ServiceProvider
{
    /**
     * Boots the service provider
     *
     * @param Router     $router
     * @param Repository $config
     * 
     * @return void
     */
    public function boot(Router $router, Repository $config)
    {
        $router->loadRoutes(
            $this->theme->basePath(
                $config->get('routing.routes', 'src/Api/routes.php')
            )
        );

        $router->setControllerNamespace(
            $config->get('routing.controllerNamespace', 'Theme\Api\Controllers')
        );

        $router->listen();
    }
}
