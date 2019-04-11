<?php

namespace Ink\Foundation\Console;

use Ink\Scribe\ExtensionManifest;
use Ink\Contracts\Foundation\Theme;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class DiscoverExtensionsCommand extends Command
{
    /**
     * Name of the command
     *
     * @var string
     */
    protected static $defaultName = 'extension:discover';

    /**
     * Stamp theme instance
     *
     * @var Theme
     */
    protected $theme;

    public function __construct(Theme $theme)
    {
        $this->theme = $theme;

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
            ->setDescription('Generate extension manifest to discover any resources provided by them.')
            ->setHelp(
                'It will parse all packages in composer installed.json file, 
                            look for ones that define "stamp" extra field, and create collective list 
                            of resources that can be used by scribe.'
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
        $output->writeln("Creating extension manifest");
        $this->discoverExtensions();
        $output->writeln("Manifest generated successfully !");
    }

    /**
     * Discover the stamp extensions after composer autoload dump
     *
     * @return void
     */
    protected function discoverExtensions(): void
    {
        $vendorDir= $this->theme->vendorPath();
        $composerDir = $vendorDir . DIRECTORY_SEPARATOR . "composer";
        $installed = $composerDir . DIRECTORY_SEPARATOR . "installed.json";

        if (file_exists($installed)) {
            static::buildExtensionManifest(
                json_decode(file_get_contents($installed), true),
                $vendorDir
            );
        }
    }

    /**
     * Build the stamp extension manifest from array of composer
     * package definition files
     *
     * @param array  $packages
     * @param string $location
     *
     * @return void
     */
    protected function buildExtensionManifest(array $packages, string $location): void
    {
        $manifest = $this->theme->container()->get(ExtensionManifest::class);

        foreach ($packages as $package) {
            if (array_key_exists("extra", $package)) {
                $extra = $package["extra"];

                if (array_key_exists("stamp", $extra)) {
                    $manifest->addExtension($extra["stamp"]);
                }
            }
        }

        $manifest->write($location);
    }
}
