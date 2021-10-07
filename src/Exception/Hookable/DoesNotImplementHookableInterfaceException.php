<?php

namespace Jascha030\ComposerTemplate\Exception\Hookable;

use Jascha030\ComposerTemplate\Hookable\HookableInterface;

final class DoesNotImplementHookableInterfaceException extends \RuntimeException
{
    private const IMPLEMENTING_INTERFACE = HookableInterface::class;

    private const MESSAGE_TEMPLATE = 'Class "%s" does not implement "%s".';

    public function __construct(string $className, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct(
            sprintf(self::MESSAGE_TEMPLATE, $className, self::IMPLEMENTING_INTERFACE),
            $code,
            $previous
        );
    }
}
