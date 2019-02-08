<?php

namespace Ink\Foundation;

use Ink\Foundation\Theme;

class ServiceProvider
{

    /**
     * Theme instance with helpful methods
     *
     * @var Stamp\Theme
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