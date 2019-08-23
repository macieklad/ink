
<?php

namespace Ink\Contracts\Scribe;

interface Hook
{
    /**
     * Inject functionality for the theme
     *
     * @return void
     */
    public function attach(): void;
}
