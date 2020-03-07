# controller
Controller package for Bone Mvc Framework
## installation
Use Composer
```
composer require delboy1978uk/bone-controller
```
## usage
Simply add to the `config/packages.php`
```php
<?php

// use statements here
use Bone\Controller\ControllerPackage;

return [
    'packages' => [
        // packages here...,
        ControllerPackage::class,
    ],
    // ...
];
```