<?php declare(strict_types=1);

namespace Bone\Console;

use Barnacle\Container;
use Bone\Console\ConsoleApplication;

interface CommandRegistrationInterface
{
    /**
     * @param Container $container
     * @return array
     */
    public function registerConsoleCommands(Container $container): array;
}