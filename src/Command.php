<?php

declare(strict_types=1);

namespace Bone\Console;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Command extends BaseCommand
{
    private ?SymfonyStyle $io = null;

    public function __construct(?string $name = null)
    {
        parent::__construct($name);
    }

    public function getIo(InputInterface $input, OutputInterface $output): SymfonyStyle
    {
        if ($this->io === null) {
            $this->io = new SymfonyStyle($input, $output);
        }

        return $this->io;
    }
}
