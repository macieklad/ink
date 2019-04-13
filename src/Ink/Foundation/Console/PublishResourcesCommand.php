<?php

namespace Ink\Foundation\Console;

use Psr\Log\LoggerInterface;
use Ink\Contracts\Scribe\Resource;
use Ink\Contracts\Foundation\Theme;
use Psr\Container\ContainerInterface;
use Ink\Contracts\Scribe\ExtensionManifest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PublishResourcesCommand extends Command
{
    /**
     * Name of the command
     *
     * @var string
     */
    protected static $defaultName = 'extension:publish';

    /**
     * Theme assistant instance
     *
     * @var Theme
     */
    protected $assistant;

    /**
     * Extension manifest instance
     *
     * @var ExtensionManifest
     */
    protected $manifest;


    /**
     * Stamp service container
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Logger instance
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Instantiate the command.
     *
     * @param ContainerInterface $container
     * @param ExtensionManifest  $manifest
     * @param LoggerInterface    $logger
     */
    public function __construct(
        ContainerInterface $container,
        ExtensionManifest $manifest,
        LoggerInterface $logger
    ) {
        $this->container = $container;
        $this->manifest = $manifest;
        $this->logger = $logger;

        parent::__construct();
    }

    /**
     * Configure the command.
     *
     * @return void
     */
    public function configure(): void
    {
        $this
            ->setDescription('Publish all extension resources')
            ->setHelp(
                "This command will publish all resources " .
                "registered by extensions, such as config " .
                "files. It should be run only once, as it will " .
                "overwrite any existing files published by extensions, " .
                "replacing them with a default clean state. To publish " .
                "single files, use built in commands provided by the " .
                "extension authors."
            );
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $failCount = 0;
        $successCount = 0;

        $output->writeln("Starting the publish process ...");

        foreach ($this->manifest->resources() as $resource) {
            if (is_a($resource, Resource::class, true)) {
                $output->writeln("Published: $resource");
                $this->container->get($resource)->publish();
                $successCount++;
            } else {
                $this->logger->warning(
                    "Resource $resource " .
                    "is not implementing Stamp Resource " .
                    "interface, skipping ..."
                );
                $failCount++;
            }
        }

        $output->writeln(
            "Finished publishing resources. " .
            "$successCount published, $failCount failed."
        );

        if ($failCount != 0) {
            $output->writeln(
                "There were some failures, " .
                "please check the output log above"
            );
        }
    }
}
