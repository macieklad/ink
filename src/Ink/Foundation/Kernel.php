<?php

namespace Ink\Foundation;

use Ink\Foundation\Theme;
use Stamp\Providers\AliasProvider;

class Kernel 
{
    /**
     * Global theme instance
     *
     * @var Stamp\Theme
     */
    protected $theme;

    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
    }

    /**
     * Read service providers list, and initalize each of them
     *
     * @return void
     */
    public function loadServices() 
    {
        $providers = $this->theme->get('providers');

        foreach($providers as $provider) {
            $this->theme->call([$provider, 'boot']);
        }
    }
}