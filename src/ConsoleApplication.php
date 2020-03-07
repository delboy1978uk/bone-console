<?php

namespace Bone\Console;

use Symfony\Component\Console\Application as SymfonyConsoleApplication;

class ConsoleApplication extends SymfonyConsoleApplication
{
    public function getLongVersion()
    {
        return "
  {}           {}                                                                   
    \  _---_  /       ____                       ______                                           _      
     \/     \/       |  _ \                     |  ____|                                         | |     
      |() ()|        | |_) | ___  _ __   ___    | |__ _ __ __ _ _ __ ___   _____      _____  _ __| | __  
       \ + /         |  _ < / _ \| '_ \ / _ \   |  __| '__/ _` | '_ ` _ \ / _ \ \ /\ / / _ \| '__| |/ /  
      / HHH  \       | |_) | (_) | | | |  __/   | |  | | | (_| | | | | | |  __/\ V  V / (_) | |  |   <   
     /  \_/   \      |____/ \___/|_| |_|\___|   |_|  |_|  \__,_|_| |_| |_|\___| \_/\_/ \___/|_|  |_|\_\\ 
   {}          {}        
                                                                                                                                                                                                                                                
";
    }
}