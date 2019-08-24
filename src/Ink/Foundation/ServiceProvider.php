<?php

namespace Ink\Foundation;

use Ink\Contracts\Foundation\Theme;

abstract class ServiceProvider
{

    /**
     * Theme instance with helpful methods
     *
     * @var Ink\Contracts\Foundation\Theme
     */
    protected $theme;

    /**
     * Initialize new provider
     *
     * @param Theme $theme
     */
    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
    }
}