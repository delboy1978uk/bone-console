<?php

declare(strict_types=1);

namespace Bone\Console\Command;

use Barnacle\Container;
use Bone\Console\Command;
use Bone\Contracts\Container\DefaultSettingsProviderInterface;
use Bone\Contracts\Container\EntityRegistrationInterface;
use Bone\Contracts\Container\FixtureProviderInterface;
use Composer\Autoload\ClassLoader;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class PostInstallCommand extends Command
{
    protected function configure(): void
    {
        $this->setDescription('Bone Framework package setup for post install.');
        $this->setHelp('Bone Framework package setup for post install.');
        $this->addArgument('package', InputArgument::REQUIRED, 'Package name.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $io = $this->getIo($input, $output);
            $io->title('💀 Post package setup');
            $package = $input->getArgument('package');
            $io->info($package);
            $package = new $package();

            if ($package instanceof DefaultSettingsProviderInterface) {
                $path = 'config/' . $package->getSettingsFileName() . '.php';

                if (!file_exists($path)) {
                    $contents = $package->getSettingsFileContents();
                    file_put_contents($path, $contents);

                    $io->info('Settings saved to ' . $path);
                }
            }

            if ($package instanceof EntityRegistrationInterface) {
                $io->writeln('Generating database migration for ' . $package . '.');
                $process = new Process(['vendor/bin/bone', 'm:diff']);
                $process->enableOutput();
                $process->run(function ($type, $buffer) use ($io): void {
                    $io->write($buffer);
                });
                $io->writeln('Running migrations...');
                $process = new Process(['vendor/bin/bone', 'm:migrate']);
                $process->enableOutput();
                $process->run(function ($type, $buffer) use ($io): void {
                    $io->write($buffer);
                });
                $io->writeln('Generating Proxies...');
                $process = new Process(['vendor/bin/bone', 'm:generate-proxies']);
                $process->enableOutput();
                $process->run(function ($type, $buffer) use ($io): void {
                    $io->write($buffer);
                });
            }

            if ($package instanceof FixtureProviderInterface) {
                $io->writeln('Loading fixtures... @todo');
//            $process = new Process(['vendor/bin/bone', 'm:generate-proxies']);
//            $process->enableOutput();
//            $process->run(function ($type, $buffer) use ($io): void {
//                $io->write($buffer);
//            });
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $io->error([$e->getMessage(), $e->getFile() . ':' . $e->getLine()]);

            return self::FAILURE;
        }
    }
}
