# bone-console
Bone Framework terminal CLI application functionality
## installation
bone-console is part of the core `delboy1978uk/bone` dependencies, it is installed automatically.
## usage
In your own package registration class, implement the `CommandRegistrationInterface`, and return an array of the 
commands you would like to add to the app.
```php
<?php declare(strict_types=1);

namespace Your\PackageName;

use Barnacle\Container;
use Barnacle\RegistrationInterface;
use Bone\Console\CommandRegistrationInterface;

class MyPackage extends RegistrationInterface implements CommandRegistrationInterface
{
    public function registerConsoleCommands(Container $container) : array
    {
        $someDependency = $container->get(Some::class);
        $awesomeCommand = new MyAwesomeCommand();
        $differentCommand = new MyAwesomeCommand($someDependency);

        return [
            $awesomeCommand,
            $differentCommand,
        ];
    }
}
```
Now in the Terminal, you can run the `vendor/bin/bone command`, and your commands will be available.