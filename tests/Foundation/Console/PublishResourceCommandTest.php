<?php

namespace Tests\Foundation\Console;

use Mockery;
use Psr\Log\LoggerInterface;
use Tests\Scribe\StubResource;
use Ink\Scribe\ExtensionManifest;
use Ink\Contracts\Scribe\Resource;
use Psr\Container\ContainerInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Ink\Foundation\Console\PublishResourcesCommand;
use Symfony\Component\Console\Tester\CommandTester;

class PublishResourceCommandTest extends MockeryTestCase
{
    /**
     * Test if publishing resources provided by extensions
     * is executed without friction.
     *
     * @return void
     */
    public function testResourcesArePublished(): void
    {
        $container = Mockery::mock(ContainerInterface::class);
        $logger = Mockery::mock(LoggerInterface::class);
        $manifest = Mockery::mock(ExtensionManifest::class);
        $validResource = Mockery::mock(Resource::class);

        $manifest->shouldReceive('resources')
            ->once()
            ->andReturn(
                [
                    'NonExistentResource',
                    StubResource::class
                ]
            );

        $container->shouldReceive('get')
            ->times(1)
            ->andReturn($validResource);

        $logger->shouldReceive('warning')
            ->once();

        $validResource->shouldReceive('publish')
            ->once();

        $command = new PublishResourcesCommand($container, $manifest, $logger);

        $tester = new CommandTester($command);
        $tester->execute([]);

        $this->assertEquals(0, $tester->getStatusCode());
    }

}
