<?php

namespace Jascha030\ComposerTemplate\Registry;

use Exception;
use Jascha030\ComposerTemplate\Container\HookableStoreInterface;
use Jascha030\ComposerTemplate\Exception\Hookable\DoesNotImplementHookableInterfaceException;
use Jascha030\ComposerTemplate\Exception\Wordpress\MissingWordpressFunctionsException;
use Jascha030\ComposerTemplate\Hookable\HookableInterface;

/**
 * Inject statically defined Hookable methods by wrapping the hooks in closures, which, in turn retrieve the
 * executing class from its own Container implementing HookableStoreInterface.
 */
final class PluginRegistry
{
    private HookableStoreInterface $hookablesContainer;

    public function __construct(HookableStoreInterface $hookablesContainer)
    {
        $this->hookablesContainer = $hookablesContainer;
    }

    /**
     * @throws Exception
     */
    public function run(): void
    {
        $this->injectHookables();
    }

    /**
     * @throws Exception
     */
    public function injectHookables(): void
    {
        foreach ($this->hookablesContainer->getBoundClassNames() as $hookableClassName) {
            $this->injectHookable($hookableClassName);
        }
    }

    /**
     * @throws Exception
     */
    private function injectHookable(string $hookableClassName): void
    {
        if (! is_subclass_of($hookableClassName, HookableInterface::class)) {
            throw new DoesNotImplementHookableInterfaceException($hookableClassName);
        }

        $hooks = [
            'add_action' => $hookableClassName::getActionHooks(),
            'add_filter' => $hookableClassName::getFilterHooks(),
        ];

        foreach ($hooks as $additionMethod => $hooksToInject) {
            $this->injectHookableMethods($additionMethod, $hooksToInject, $hookableClassName);
        }
    }

    private function injectHookableMethods(string $method, array $hooks, string $hookableClassName): void
    {
        foreach ($hooks as $tag => $parameters) {
            if ($this->hooksMultipleMethods($parameters)) {
                // Multiple hook definitions are bound to current filter.
                foreach ($parameters as $params) {
                    $params = (! \is_array($params)) ? [$params] : $params;

                    $this->injectHookableMethod($method, $tag, $hookableClassName, ...$params);
                }

                continue;
            }

            // Single method is hooked to filter.
            $parameters = ! \is_array($parameters) ? [$parameters] : $parameters;

            $this->injectHookableMethod($method, $tag, $hookableClassName, ...$parameters);
        }
    }

    /**
     * Lazily inject a class method to an action or filter-hook.
     */
    private function injectHookableMethod(
        string $additionMethod,
        string $tag,
        string $hookable,
        string $classMethod,
        int $priority = 10,
        int $args = 1
    ): void {
        $closure = $this->wrap($hookable, $classMethod);

        if (! \function_exists($additionMethod)) {
            throw new MissingWordpressFunctionsException($additionMethod);
        }

        $additionMethod($tag, $closure, $priority, $args);
    }

    /**
     * Wraps call to add_action/filter inside a closure, to prevent unnecessary class construction.
     */
    private function wrap(string $hookableClassname, string $hookableClassMethod): \Closure
    {
        return function (...$arguments) use ($hookableClassname, $hookableClassMethod) {
            return $this->hookablesContainer->get($hookableClassname)->{$hookableClassMethod}(...$arguments);
        };
    }

    private function hooksMultipleMethods(array $parameters): bool
    {
        return \is_array($parameters[0]);
    }
}
