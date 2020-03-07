<?php declare(strict_types=1);

namespace Bone\Console;

use Barnacle\Container;
use Bone\Console\ConsoleApplication;

interface CommandRegistrationInterface
{
    /**
     * @param ConsoleApplication $app
     * @return ConsoleApplication
     */
    public function addToConsole(ConsoleApplication $app, Container $container): ConsoleApplication;
}