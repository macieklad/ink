<?php

namespace Ink\Foundation\Console;

use Ink\Contracts\Foundation\Theme;
use Ink\Contracts\Scribe\Resource;
use Ink\Contracts\Scribe\ThemeAssistant;
use Ink\Contracts\Scribe\ExtensionManifest;
use Psr\Container\ContainerInterface;
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

    public function __construct(
        ContainerInterface $container,
        ThemeAssistant $assistant,
        ExtensionManifest $manifest)
    {
        $this->container = $container;
        $this->assistant = $assistant;
        $this->manifest = $manifest;

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
            ->setHelp('This command will publish all resources registered by extensions, such as config files.
                            It should be run only once, as it will overwrite any existing files published by extensions,
                            replacing them with a default clean state. To publish single files, use built in commands
                            provided by the extension authors.');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
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
                $output->writeln("WARNING: Resource $resource is not implementing Stamp Resource interface, skipping ...");
                $failCount++;
            }
        }

        $output->writeln("Finished publishing resources. $successCount published, $failCount failed.");

        if ($failCount != 0) {
            $output->writeln("There were some failures, please check the output log above");
        }
    }
}