<?php

namespace Jascha030\ComposerTemplate\Container;

use Jascha030\ComposerTemplate\Hookable\HookableInterface;
use Psr\Container\ContainerInterface;

abstract class HookableStoreAbstract implements HookableStoreInterface
{
    private ContainerInterface $container;

    /**
     * @var string[]
     */
    private array $hookableClasses;

    public function __construct(ContainerInterface $container, array $hookableClasses = [])
    {
        $this->container       = $container;

        $this->registerHookableClasses($hookableClasses);
    }

    public function get(string $id)
    {
        $this->container->get($id);
    }

    public function has(string $id): bool
    {
        return $this->container->has($id);
    }

    public function getBoundClassNames(): array
    {
        return array_keys($this->hookableClasses);
    }

    public function validateHookableClasses(array $hookableClasses): array
    {
        $this->hookableClasses = [];

        // Value should implement HookableInterface if key is alias
        foreach ($hookableClasses as $key => $value) {
            if (! $this->isHookable($key) && ! $this->isHookable($value)) {
                throw new \InvalidArgumentException();
            }
        }

        return $hookableClasses;
    }

    private function registerHookableClasses(array $hookableClasses): void
    {
        $this->hookableClasses = $this->validateHookableClasses($hookableClasses);
    }

    private function isHookable(string $class): bool
    {
        return is_subclass_of($class, HookableInterface::class);
    }
}
