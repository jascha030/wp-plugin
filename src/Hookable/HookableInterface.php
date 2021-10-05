<?php

namespace Jascha030\ComposerTemplate\Hookable;

interface HookableInterface
{
    public static function getActionHooks(): array;

    public static function getFilterHooks(): array;
}
