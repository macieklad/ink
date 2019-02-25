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
        $this->container = $container;
        $this->config = $config;

        $this->bootstrapManagers();
        $this->loadHooks();
    }

    /**
     * Create global hook managers and configure them
     *
     * @return void
     */
    protected function bootstrapManagers()
    {
        $handlerNamespace = $this->config->get(
            'hooks.handlerNamespace', 
            'Theme\Hooks\Handlers'
        );

        $mutatorNamespace = $this->config->get(
            'hooks.mutatorNamespace',
            'Theme\Hooks\Mutators'
        );

        $this->actionManager = $this->container->get(ActionManager::class);
        $this->filterManager = $this->container->get(FilterManager::class);

        $this->actionManager->setHandlerNamespace($handlerNamespace);
        $this->filterManager->setMutatorNamespace($mutatorNamespace);

        $this->container->set(ActionManager::class, $this->actionManager);
        $this->container->set(FilterManager::class, $this->filterManager);
    }

    /**
     * Load hooks registered in files
     *
     * @return void
     */
    protected function loadHooks()
    {
        $hookFiles = $this->config->get(
            'hooks.files', 
            [   
                $this->theme->basePath('src/Hooks/actions.php'),
                $this->theme->basePath('src/Hooks/filters.php')
            ]
        );
        
        $actionManager = $this->actionManager;
        $filterManager = $this->filterManager;

        foreach ($hookFiles as $hookFile) {
            include_once $hookFile;
        }
    }
}
