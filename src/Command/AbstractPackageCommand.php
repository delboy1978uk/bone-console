<?php

declare(strict_types=1);

namespace Bone\Console\Command;

use Bone\Console\Command;

abstract class AbstractPackageCommand extends Command
{
    protected function exportArray(array $packages): void
    {
        $array = var_export($packages['packages'], true);
        $array = str_replace('array (', '[', $array);
        $array = str_replace(')', ']', $array);
        $array = preg_replace('#\s\s\d+\s=>\s\'#', '        ', $array);
        $array = str_replace('\',', '::class,', $array);
        $array = str_replace('\\\\', '\\', $array);
        $array = str_replace("\n]", "\n    ]", $array);
        file_put_contents('config/packages.php', "<?php\n\nreturn [\n    'packages' => " . $array . "\n];");
    }
}
