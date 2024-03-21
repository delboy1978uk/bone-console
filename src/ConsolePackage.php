<?php declare(strict_types=1);

namespace Bone\Console;

use Barnacle\Container;
use Barnacle\RegistrationInterface;

class ConsolePackage implements RegistrationInterface
{
    public function addToContainer(Container $c): void
    {
        $app = $c->has(ConsoleApplication::class) ? $c->get(ConsoleApplication::class) : new ConsoleApplication();
        $consoleCommands = $c->get('consoleCommands');
        $app->addCommands($consoleCommands);
    }
}
