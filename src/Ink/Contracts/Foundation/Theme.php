<?php

namespace Ink\Contracts\Foundation;

use DI\Container;

interface Theme extends \ArrayAccess
{
    /**
     * Create new theme with base path
     *
     * @param string $basePath
     */
    public function __construct(string $basePath = null);

    /**
     * Return path relative to theme root directory
     *
     * @param string $path
     * 
     * @return string
     */
    public function basePath(string $path = ''): string;
    
    /**
     * Return path relative to the config directory 
     * inside the root directory
     *
     * @param string $path
     * 
     * @return string
     */
    public function configPath(string $path = ''): string;

    /**
     * Return path relative to the vendor composer
     * directory inside the root directory
     *
     * @param string $path
     *
     * @return string
     */
    public function vendorPath(string $path = ''): string;

    /**
     * Bootstrap the theme
     *
     * @return void
     */
    public function bootstrap(): void;

    /**
     * Return container that provides the theme with dependencies
     * 
     * @return Container
     */
    public function container(): Container;
}