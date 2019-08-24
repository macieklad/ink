<?php

namespace Tests\Foundation\Console;

use Symfony\Component\Filesystem\Filesystem;
use Tests\Scribe\StubCommand;
use Tests\Scribe\StubHook;
use Tests\Scribe\StubResource;

class InstalledMocker
{
    /**
     * Stamp fields inside installed.json file
     *
     * @var array
     */
    protected $stampFields;

    /**
     * Raw content of installed.json file
     *
     * @var array
     */
    protected $contents;

    /**
     * Filesystem instance
     *
     * @var Filesystem
     */
    protected $fs;

    /**
     * Location of installation
     *
     * @var string
     */
    protected $location;

    /**
     * Path to installed.json file
     *
     * @var string
     */
    protected $path;

    /**
     * Prepare the mocker for usage, set the contents of installed file.
     *
     * @param string $location
     */
    public function __construct(string $location = '')
    {
        $this->fs = new Filesystem();
        $this->location = $location === '' ? __DIR__ : $location;

        $this->stampFields = [
            "commands" => [
                StubCommand::class
            ],
            "resources" => [
                "NotExistentResource",
                StubResource::class
            ],
            "hooks" => [
                StubHook::class
            ]
        ];

        $this->contents = [
            [
                "extra" => [
                    "stamp" => $this->stampFields
                ]
            ]
        ];
    }

    /**
     * Write the installed.json file
     *
     * @return void
     */
    public function write(): void
    {
        $this->fs->dumpFile(
            $this->location . '/vendor/composer/installed.json',
            json_encode($this->contents)
        );
    }

    /**
     * Write the stamp manifest,
     * skipping installed step.
     *
     * @return void
     */
    public function writeManifest(): void
    {
        $this->fs->dumpFile(
            $this->location . '/vendor/stamp-manifest.json',
            json_encode($this->stampFields)
        );
    }

    /**
     * Delete all created files and directories
     *
     * @return void
     */
    public function clean(): void
    {
        $this->fs->remove(
            $this->location . '/vendor'
        );
    }

    /**
     * Get json contents of installed file
     *
     * @return false|string
     */
    public function contents()
    {
        return json_encode($this->contents);
    }

    /**
     * Extract specific field from installed.json stamp
     * extra field, nested inside extension object
     *
     * @param string $fieldName
     *
     * @return mixed
     */
    public function field(string $fieldName)
    {
        return $this->stampFields[$fieldName];
    }
}
