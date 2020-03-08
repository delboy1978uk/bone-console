<?php declare(strict_types=1);

namespace Bone\Console;

use Barnacle\Container;
use Barnacle\RegistrationInterface;

class ConsolePackage implements RegistrationInterface
{
    /**
     * @param Container $c
     */
    public function addToContainer(Container $c)
    {
        $c[ConsoleApplication::class] = $c->factory(function(Container $c) {
            $app = new ConsoleApplication();
            $consoleCommands = $c->get('consoleCommands');
            $app->addCommands($consoleCommands);

            return $app;
        });
    }
}
