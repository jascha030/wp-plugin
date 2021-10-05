<?php

namespace Jascha030\ComposerTemplate\Hookable;

/**
 * Solves HookabkleInterface implementation for any class without need to repeatedly re-write methods, or
 * sacrifice hierarchically flexibility, being bound to HookableInterface implementing ancestors.
 * In other words: any method of any class should be able to interface with WordPress' plugin API, without being
 * forced to always construct it, while it might not be used during a request's lifecycle.
 * @property $actions array
 * @property $filters array
 */
trait ProvidesHookDefinitionsTrait
{
    final public static function getActions(): array
    {
        return static::$actions ?? [];
    }

    final public static function getFilters(): array
    {
        return static::$filters ?? [];
    }
}
