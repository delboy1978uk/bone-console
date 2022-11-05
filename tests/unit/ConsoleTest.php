<?php

namespace Bone\Tests;

use Barnacle\Container;
use Barnacle\Exception\NotFoundException;
use Bone\Console\ConsoleApplication;
use Bone\Console\ConsolePackage;
use Bone\Db\DbPackage;
use Codeception\Test\Unit;
use PDO;

class ConsoleTest extends Unit
{
    /** @var Container */
    protected $container;

    protected function _before()
    {
        $this->container = $c = new Container();
        $c[ConsoleApplication::class] = new ConsoleApplication();
        $c['consoleCommands'] = [];
        $package = new ConsolePackage();
        $package->addToContainer($c);
    }

    protected function _after()
    {
        unset($this->container);
    }


    public function testGetApplicationFromContainer()
    {
        $c = $this->container;
        /** @var ConsoleApplication $app */
        $app = $c->get(ConsoleApplication::class);
        $this->assertInstanceOf(ConsoleApplication::class, $app);
        $this->assertEquals("
  {}           {}                                                                   
    \  _---_  /       ____                       ______                                           _      
     \/     \/       |  _ \                     |  ____|                                         | |     
      |() ()|        | |_) | ___  _ __   ___    | |__ _ __ __ _ _ __ ___   _____      _____  _ __| | __  
       \ + /         |  _ < / _ \| '_ \ / _ \   |  __| '__/ _` | '_ ` _ \ / _ \ \ /\ / / _ \| '__| |/ /  
      / HHH  \       | |_) | (_) | | | |  __/   | |  | | | (_| | | | | | |  __/\ V  V / (_) | |  |   <   
     /  \_/   \      |____/ \___/|_| |_|\___|   |_|  |_|  \__,_|_| |_| |_|\___| \_/\_/ \___/|_|  |_|\_\\ 
   {}          {}        
                                                                                                                                                                                                                                                
", $app->getLongVersion());
    }




}


