<?php declare(strict_types=1);

namespace Bone\Console;

use Barnacle\Container;
use Barnacle\RegistrationInterface;
use Bone\Console\Command\DebugContainerCommand;
use Bone\Console\Command\PackagesCommand;
use Bone\Console\Command\PostInstallCommand;

class ConsolePackage implements RegistrationInterface
{
    public function addToContainer(Container $c): void
    {
        $app = $c->has(ConsoleApplication::class) ? $c->get(ConsoleApplication::class) : new ConsoleApplication();
        $consoleCommands = $c->get('consoleCommands');
        $consoleCommands[] = new DebugContainerCommand($c);
        $consoleCommands[] = new PackagesCommand($c->get('packages'));
        $consoleCommands[] = new PostInstallCommand('packages:post-install');
        $app->addCommands($consoleCommands);
    }
}
