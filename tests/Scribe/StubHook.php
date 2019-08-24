<?php

namespace Tests\Scribe;

use Ink\Scribe\Hook;
use Tests\Foundation\Stub\BootStub;

class StubHook extends Hook
{
    /**
     * Attach the hook inside theme
     *
     * @return void
     */
    public function attach(): void
    {
        $this->registerProviders(
            [
            BootStub::class
            ]
        );

        $this->registerAliases(
            [
            'Foo' => StubAlias::class
            ]
        );
    }
}
