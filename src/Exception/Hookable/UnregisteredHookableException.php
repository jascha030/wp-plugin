<?php

namespace Jascha030\ComposerTemplate\Exception\Hookable;

use Throwable;

class UnregisteredHookableException extends \RuntimeException
{
    private const MESSAGE_TEMPLATE = 'Class: "%s" was not found in the provided container object.';

    public function __construct(string $className = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE_TEMPLATE, $className), $code, $previous);
    }
}
