<?php

declare(strict_types=1);

namespace Bone\Console\Command;

use Barnacle\Container;
use Bone\Console\Command;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DebugContainerCommand extends Command
{
    public function __construct(
        private ContainerInterface $container
    ) {
        parent::__construct('debug:container');
    }
    protected function configure(): void
    {
        $this->setDescription('See config values from the container.');
        $this->setHelp('See config values from the container.');
        $this->addArgument('key', InputArgument::OPTIONAL, 'The config key you wish to debug', false);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->getIo($input, $output);
        $key = $input->getArgument('key');
        $io->title('💀Bone container debug');

        if ($key && $this->container->has($key)) {
            $value = $this->container->get($key);
            $type = gettype($value);
            $io->writeln('<fg=yellow;>' .$key . '</> ');
            $io->writeln($this->getDisplayValue($type, $value));

            return self::SUCCESS;
        } else if ($key) {
            $io->error('Key "' . $key . '" does not exist.');

            return self::FAILURE;
        }

        /** @var Container $c */
        $c = $this->container;
        $keys = $c->keys();
        sort($keys);

        foreach ($keys as $name) {
            $value = $c->get($name);
            $type = gettype($value);
            $io->writeln('<fg=yellow;>' .$name . '</> ');
            $io->writeln($this->getDisplayValue($type, $value));
            $io->writeln('');
        }

        return self::SUCCESS;
    }

    private function getDisplayValue(string $type, mixed $value): string
    {
        switch ($type) {
            case 'boolean':
                return $value ? 'true' : 'false';
                break;
            case 'float':
            case 'integer':
            case 'string':
                return (string) $value;
                break;
            case 'array':
                return json_encode($value, JSON_PRETTY_PRINT);
                break;
            case 'object':
                return 'Object of class ' . \get_class($value);
        }
    }
}
