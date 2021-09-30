<?php

namespace Jascha030\ComposerTemplate\Registry;

use Jascha030\ComposerTemplate\Container\HookableStoreInterface;
use Jascha030\ComposerTemplate\Hookable\HookableInterface;

class PluginRegistry
{
    private HookableStoreInterface $hookablesContainer;

    public function __construct(HookableStoreInterface $hookablesContainer)
    {
        $this->hookablesContainer = $hookablesContainer;
    }

    /**
     * @throws \Exception
     */
    public function run(): void
    {
        $this->injectHookables();
    }

    /**
     * @throws \Exception
     */
    private function injectHookables(): void
    {
        foreach ($this->hookablesContainer->getBoundClassNames() as $hookableClassName) {
            $this->injectHookableMethods($hookableClassName);
        }
    }

    /**
     * @throws \Exception
     */
    private function injectHookableMethods(string $hookableClassName): void
    {
        if (! is_subclass_of($hookableClassName, HookableInterface::class)) {
            $interface = HookableInterface::class;

            throw new \Exception("Class \"{$hookableClassName}\" does not implement \"{$interface}\".");
        }

        $hooks = [
            'add_action' => $hookableClassName::getActionHooks(),
            'add_filter' => $hookableClassName::getFilterHooks()
        ];

        foreach ($hooks as $additionMethod => $hooksToInject) {
            foreach ($hooksToInject as $tag => $parameters) {
                // Check if single or multiple methods are added to hook.
                if (\is_array($parameters) && \is_array($parameters[0])) {
                    foreach ($parameters as $params) {
                        // multiple hooked methods.
                        if (!\is_array($params)) {
                            $params = [$params];
                        }

                        $this->injectFilterWithHookableMethod($additionMethod, $tag, $hookableClassName, ...$params);
                    }

                    continue;
                }

                // single hooked method.
                if (!\is_array($parameters)) {
                    $parameters = [$parameters];
                }

                $this->injectFilterWithHookableMethod($additionMethod, $tag, $hookableClassName, ...$parameters);
            }
        }
    }

    private function injectFilterWithHookableMethod(
        string $additionMethod,
        string $tag,
        string $hookableClassname,
        string $hookableClassMethod,
        int $prio = 10,
        int $args = 1
    ): void {
        $closure = $this->wrapHook($hookableClassname, $hookableClassMethod);

        $additionMethod($tag, $closure, $prio, $args);
    }

    private function wrapHook(string $hookableClassname, string $hookableClassMethod): \Closure
    {
        return function (...$arguments) use ($hookableClassname, $hookableClassMethod) {
            return $this->hookablesContainer->get($hookableClassname)->{$hookableClassMethod}(...$arguments);
        };
    }
}
