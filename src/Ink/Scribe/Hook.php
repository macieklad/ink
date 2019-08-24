<?php

namespace Ink\Scribe;

use Ink\Contracts\Scribe\Hook as HookContract;
use Ink\Contracts\Scribe\ThemeAssistant;

class Hook implements HookContract
{
    /**
     * Utility class for theme ops
     *
     * @var ThemeAssistant
     */
    protected $assistant;

    /**
     * Hook constructor.
     *
     * @param ThemeAssistant $assistant
     */
    public function __construct(ThemeAssistant $assistant)
    {
        $this->assistant = $assistant;
    }

    /**
     * Hook some functionality inside the theme
     *
     * @return void
     */
    public function attach(): void
    {
        // silence is golden
    }

    /**
     * Add providers to theme before initial load
     *
     * @param array $providers
     *
     * @return void
     */
    public function registerProviders(array $providers): void
    {
        $this->assistant->registerProviders($providers);
    }

    /**
     * Add aliases to theme before initial load
     *
     * @param array $aliases
     *
     * @return void
     */
    public function registerAliases(array $aliases): void
    {
        $this->assistant->registerAliases($aliases);
    }

}

