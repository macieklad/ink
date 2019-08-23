<?php


namespace Ink\Scribe;

use Ink\Contracts\Scribe\ThemeAssistant;
use Ink\Contracts\Scribe\Resource as ResourceContract;

class Resource implements ResourceContract
{
    /**
     * Utility class for theme ops
     *
     * @var ThemeAssistant
     */
    protected $assistant;

    /**
     * Resource constructor.
     *
     * @param ThemeAssistant $assistant
     */
    public function __construct(ThemeAssistant $assistant)
    {
        $this->assistant = $assistant;
    }

    /**
     * Publish all data related with the resource
     *
     * @return void
     */
    public function publish(): void
    {
        // silence is golden
    }

    /**
     * Move file to theme config directory
     *
     * @param string $file
     *
     * @return void
     */
    public function publishConfig(string $file): void
    {
        $this->assistant->publishConfig($file);
    }

}