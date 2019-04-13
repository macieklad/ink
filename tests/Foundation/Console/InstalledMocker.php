<?php

namespace Tests\Foundation\Console;

use Symfony\Component\Filesystem\Filesystem;
use Tests\Scribe\StubCommand;
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
     * Path to installed.json file
     *
     * @var string
     */
    protected $path;

    /**
     * Prepare the mocker for usage, set the contents of installed file.
     */
    public function __construct()
    {
        $this->fs = new Filesystem();
        $this->path = __DIR__ . "/vendor/composer/installed.json";

        $this->stampFields = [
            "commands" => [
                StubCommand::class
            ],
            "resources" => [
                "NotExistentResource",
                StubResource::class
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
            $this->path,
            json_encode($this->contents)
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
            __DIR__ . '/vendor'
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
