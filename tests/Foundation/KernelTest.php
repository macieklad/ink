<?php

namespace Tests\Foundation;

use Ink\Foundation\Kernel;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Container\ContainerInterface as Container;

class KernelTest extends MockeryTestCase
{
    /**
     * Expect that kernell passes command calls to container
     *
     * @return void
     */
    public function testCommandExecutionIsDelegatedToContainer()
    {
        $container = \Mockery::mock(Container::class);

        $container->shouldReceive('call')
            ->with(['Foo', 'fire'])
            ->twice();

        $kernel = new Kernel($container);

        $kernel->executeCommands(
            [
                'Foo',
                'Foo'
            ]
        );
    }
}
