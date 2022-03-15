<?php

namespace Jascha030\ComposerTemplate;

use Jascha030\ComposerTemplate\Container\HookableStoreInterface;
use Jascha030\ComposerTemplate\Registry\PluginRegistry;

class Plugin
{
    private string $file;

    private HookableStoreInterface $hookableStore;

    private PluginRegistry $registry;

    public function __construct(string $file)
    {
        if (! file_exists($file)) {
            throw new \RuntimeException("Invalid plugin file path: \"{$file}\".");
        }

        $this->file = $file;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getRootDirectory(): string
    {
        return \dirname($this->file);
    }
}
