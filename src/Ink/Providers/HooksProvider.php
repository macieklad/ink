<?php

namespace Ink\Providers;

use Ink\Contracts\Routing\Router;
use Ink\Foundation\ServiceProvider;
use Ink\Contracts\Config\Repository;
use Psr\Container\ContainerInterface;
use Ink\Contracts\Hooks\ActionManager;
use Ink\Contracts\Hooks\FilterManager;

class HooksProvider extends ServiceProvider
{
    /**
     * Boots the service provider
     *
     * @param Psr\Container\ContainerInterface $container
     * @param Ink\Contracts\Config\Repository  $config
     * 
     * @return void
     */
    public function boot(ContainerInterface $container, Repository $config)
    {
        $handlerNamespace = $config->get(
            'hooks.handlerNamespace', 
            'Theme\Hooks\Handlers'
        );

        $mutatorNamespace = $config->get(
            'hooks.mutatorNamespace',
            'Theme\Hooks\Mutators'
        );

        $actionManager = $container->get(ActionManager::class);
        $filterManager = $container->get(FilterManager::class);

        $actionManager->setHandlerNamespace($handlerNamespace);
        $filterManager->setMutatorNamespace($mutatorNamespace);

        $container->set(ActionManager::class, $actionManager);
        $container->set(FilterManager::class, $filterManager);
    }
}