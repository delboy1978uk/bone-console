# bone-console
[![Latest Stable Version](https://poser.pugx.org/delboy1978uk/bone-console/v/stable)](https://packagist.org/packages/delboy1978uk/bone-console) [![Total Downloads](https://poser.pugx.org/delboy1978uk/bone/downloads)](https://packagist.org/packages/delboy1978uk/bone) [![Latest Unstable Version](https://poser.pugx.org/delboy1978uk/bone-console/v/unstable)](https://packagist.org/packages/delboy1978uk/bone-console) [![License](https://poser.pugx.org/delboy1978uk/bone-console/license)](https://packagist.org/packages/delboy1978uk/bone-console)<br />
[![Build Status](https://travis-ci.org/delboy1978uk/bone-console.png?branch=master)](https://travis-ci.org/delboy1978uk/bone-console) [![Code Coverage](https://scrutinizer-ci.com/g/delboy1978uk/bone-console/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/delboy1978uk/bone-console/?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/delboy1978uk/bone-console/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/delboy1978uk/bone-console/?branch=master)<br />

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
    /**
     * @param Container $container
     * @return array
     */
    public function registerConsoleCommands(Container $container) : array
    {
        $someDependency = $container->get(Some::class);
        $awesomeCommand = new AwesomeCommand();
        $differentCommand = new DifferentCommand($someDependency);

        return [
            $awesomeCommand,
            $differentCommand,
        ];
    }
}
```
Now in the Terminal, you can run the `vendor/bin/bone command`, and your commands will be available.