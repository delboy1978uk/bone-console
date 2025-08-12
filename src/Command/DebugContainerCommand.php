<?php

declare(strict_types=1);

namespace Bone\Console\Command;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DebugContainerCommand extends Command
{
    public function __construct(private ContainerInterface $container)
    {
        parent::__construct('debug:container');
    }
    protected function configure(): void
    {
        $this->setDescription('See config values from the container.');
        $this->setHelp('See config values from the container.');
        $this->addArgument('key', InputArgument::REQUIRED, 'The config key you wish to debug', false);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $key = $input->getArgument('key');
        $io->title('💀Bone container debug');

        if ($this->container->has($key)) {
            $item = $this->container->get($key);

            if (\is_array($item)) {
                $io->tree($item);
            }

            return self::SUCCESS;
        }

        $io->error('Key "' . $key . '" does not exist.');

        return self::FAILURE;
    }
}
