<?php

namespace Ink\Contracts\Foundation;

use Psr\Container\ContainerInterface;

interface Theme extends \ArrayAccess
{
    /**
     * Create new theme with base path
     *
     * @param string $base
     */
    public function __construct(string $basePath = null);

    /**
     * Return path relative to theme root directory
     *
     * @param string $path
     * @return string
     */
    public function basePath(string $path = ''): string;
    
    /**
     * Return path relative to the config directory 
     * inside the root directory
     *
     * @param string $path
     * @return string
     */
    public function configPath(string $path = ''): string;

    /**
     * Bootstrap the theme
     *
     * @return void
     */
    public function bootstrap(): void;

    /**
     * Return container that provides the theme with dependencies
     * 
     */
    public function container(): ContainerInterface;
}