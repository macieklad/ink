<?php

namespace Ink\Foundation;

use function DI\create;
use Ink\Routing\Router;
use Ink\Foundation\Kernel;
use Ink\Foundation\Bootstrap\LoadServices;
use Ink\Container\ContainerProxy as Container;
use Ink\Foundation\Bootstrap\LoadConfiguration;

class Theme
{

    /**
     * Container instance
     *
     * @var Ink\Container\ContainerProxy
     */
    protected $container;

    /**
     * Theme kernel
     *
     * @var Ink\Foundation\Kernel
     */
    protected $kernel;

    /**
     * Base path of the theme directory
     * 
     * @var string
     */
    protected $basePath;

    /**
     * Create a new theme class with root directory
     * 
     * @param string $basePath
     */
    public function __construct(string $basePath = null)
    {
        $this->beautifyErrors();
        $this->prepareContainer();

        if ($basePath) {
            $this->setBasePaths($basePath);
        }

        $this->createBaseAliases();
        $this->loadKernel();
        $this->bootstrap();
    }

    /**
     * Use whoops for error handling in dev mode
     *
     * @return void
     */
    protected function beautifyErrors()
    {
        if (WP_DEBUG === true) {
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            $whoops->register();
        }
    }

    /**
     * Get the container for the application
     *
     * @return void
     */
    protected function prepareContainer()
    {
        Container::addDefinitions([
            'router' => create(Router::class)
        ]);

        $this->container = Container::getInstance();
    }

    /**
   
     * Create kernel class and assign it to the theme
     *
     * @return void
     */
    public function loadKernel() {
        $this->kernel = $this->container->get(Kernel::class);
    }

    /**
     * Bootstrap the theme core components
     *
     * @return void
     */
    public function bootstrap() 
    {
        $this->kernel->executeCommands([
            LoadConfiguration::class,
            LoadServices::class
        ]);
    }

    /**
     * Add basic aliases useful for the theme 
     *
     * @return void
     */
    public function createBaseAliases()
    {
        $container = $this->container;

        $container->set('theme', $this);
        $container->set(Theme::class, $this);
        $container->set('container', $container);
        $container->set(Container::class, $container);
    }

    /**
     * Register application base paths
     *
     * @param string $path
     * @return void
     */
    protected function setBasePaths(string $path)
    {
        $container = $this->container;

        $this->basePath = rtrim($path, '\/');

        $container->set('path.base', $this->basePath());
        $container->set('path.config', $this->configPath());
    }

    /**
     * Return path from application root to the pointed path
     *
     * @param string $path
     * @return void
     */
    public function basePath(string $path = '')
    {
        return $this->basePath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Return path to the path inside config directory
     *
     * @param string $path
     * @return void
     */
    public function configPath(string $path = '')
    {
        return $this->basePath . DIRECTORY_SEPARATOR . "config" . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }


}