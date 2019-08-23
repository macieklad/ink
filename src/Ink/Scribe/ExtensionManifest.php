<?php
namespace Ink\Scribe;

use Ink\Contracts\Scribe\ExtensionManifest as ManifestContract;

class ExtensionManifest implements ManifestContract
{
    /**
     * Array of available extension commands
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Array of resource publishers for each extension
     *
     * @var array
     */
    protected $resources = [];

    /**
     * List of extension hooks, which add services
     *
     * @var array
     */
    protected $hooks = [];

    /**
     * Load the manifest from location
     *
     * @param string $location
     *
     * @return void
     */
    public function loadFrom(string $location): void
    {
        if (file_exists($location)) {
            $this->load(
                json_decode(file_get_contents($location), true)
            );
        }
    }

    /**
     * Loads manifest from array
     *
     * @param array $manifest
     *
     * @return void
     */
    public function load(array $manifest): void
    {
        if (array_key_exists("commands", $manifest)) {
            $this->commands = $manifest["commands"];
        }

        if (array_key_exists("hooks", $manifest)) {
            $this->hooks = $manifest["hooks"];
        }

        if (array_key_exists("resources", $manifest)) {
            $this->resources = $manifest["resources"];
        }
    }

    /**
     * Load the extension from schema, extracted
     * from composer file "extra" fields
     *
     * @param array $extension
     *
     * @return void
     */
    public function addExtension(array $extension): void
    {
        $data = $this->extractKeys(
            $extension,
            [
                'commands' => [],
                'hooks' => [],
                'resources' => []
            ]
        );

        $this->commands = array_merge($this->commands, $data['commands']);
        $this->hooks = array_merge($this->hooks, $data['hooks']);
        $this->resources = array_merge($this->resources, $data['resources']);
    }

    /**
     * Write the parsed manifest into a location
     *
     * @param string $location
     *
     * @return void
     */
    public function write(string $location): void
    {
        $data = [
            "commands" => $this->commands,
            "resources" => $this->resources,
            "hooks" => $this->hooks
        ];

        if (is_writable(dirname($location))) {
            file_put_contents(
                $location . DIRECTORY_SEPARATOR . 'stamp-manifest.json',
                json_encode($data)
            );
        }
    }

    /**
     * Get commands registered by extensions
     *
     * @return array
     */
    public function commands(): array
    {
        return $this->commands;
    }

    /**
     * Get resource publishers provided by extensions
     *
     * @return array
     */
    public function resources(): array
    {
        return $this->resources;
    }

    /**
     * Get all hooks registered by extensions
     *
     * @return array
     */
    public function hooks(): array
    {
        return $this->hooks;
    }

    /**
     * Extract keys->vals  pair or default
     * values from the source array
     *
     * @param array $source
     * @param array $keysWithDefaults
     *
     * @return array
     */
    protected function extractKeys(array $source, array $keysWithDefaults)
    {
        foreach ($keysWithDefaults as $key => $default) {
            if (array_key_exists($key, $source)) {
                $keysWithDefaults[$key] = $source[$key];
            }
        }

        return $keysWithDefaults;
    }
}
