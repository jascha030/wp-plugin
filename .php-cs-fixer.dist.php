<?php

/*
 * This file is part of the Jascha van Aalst WordPress Back-end PHP-CS-Fixer Config package.
 *
 * (c) Jascha van Aalst <contact@jaschavanaalst.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Jascha030\PhpCsFixer\Config;
use PhpCsFixer\Finder;

require_once __DIR__ . '/vendor-bin/php-cs-fixer/vendor/autoload.php';

/**
 * Cache dir and file location.
 */
$cacheDirectory = __DIR__ . '/.var/cache';

/**
 * Create a .cache dir if not already present.
 */
if (! file_exists($cacheDirectory) && ! mkdir($cacheDirectory, 0o700, true) && ! is_dir($cacheDirectory)) {
    throw new RuntimeException(sprintf('Directory "%s" was not created', $cacheDirectory));
}

$finder = Finder::create()
    ->in(__DIR__)
    ->exclude([
        'tests/Fixtures',
        'vendor-bin',
        'vendor',
    ])
    ->ignoreDotFiles(false);

return (new Config(
    Config::PHP_83,
    null,
))
    ->setFinder($finder)
    ->setCacheFile("{$cacheDirectory}/.php-cs-fixer.cache");
