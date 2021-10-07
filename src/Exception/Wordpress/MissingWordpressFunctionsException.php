<?php

namespace Jascha030\ComposerTemplate\Exception\Wordpress;

final class MissingWordpressFunctionsException extends \RuntimeException
{
    private const MESSAGE_TEMPLATE = 'Couldn\'t load function: "%s", make sure this is an active WordPress install.';

    public function __construct(string $function, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE_TEMPLATE, $function), $code, $previous);
    }
}
