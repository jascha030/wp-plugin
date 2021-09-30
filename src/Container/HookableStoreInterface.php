<?php

namespace Jascha030\ComposerTemplate\Container;

use Psr\Container\ContainerInterface;

interface HookableStoreInterface extends ContainerInterface
{
    /**
     * @return string[]
     */
    public function getBoundClassNames(): array;
}
