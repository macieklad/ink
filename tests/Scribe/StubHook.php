<?php

namespace Tests\Scribe;

use Ink\Contracts\Scribe\Hook;

class StubHook implements Hook
{
    /**
     * Attach the hook inside theme
     *
     * @return void
     */
    public function attach(): void
    {
        // Silence is golden
    }
}
