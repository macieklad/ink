<?php

namespace Stamp\Providers;

use Stamp\Theme;

class Provider {

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