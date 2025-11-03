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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class PostInstallCommand extends AbstractPackageCommand
{
    protected function configure(): void
    {
        $this->setDescription('Bone Framework package setup for post install.');
        $this->setHelp('Bone Framework package setup for post install.');
        $this->addArgument('package', InputArgument::REQUIRED, 'Package name.');
        $this->addOption('enable-only', 'e', InputOption::VALUE_NONE, 'Enable the package.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $io = $this->getIo($input, $output);
            $io->title('💀 Post package setup');
            $package = $input->getArgument('package');
            $enableOnly = $input->getOption('enable-only');

            if ($enableOnly) {
                $this->enablePackage($package);

                return self::SUCCESS;
            }

            $this->enablePackage($package);
            $io->info($package);
            $instance = new $package();

            if ($instance instanceof DefaultSettingsProviderInterface) {
                $path = realpath($instance->getSettingsFileName());

                if (!file_exists($path)) {
                    $contents = file_get_contents($path);
                    file_put_contents($path, $contents);

                    $io->info('Settings saved to ' . $path . ' successfully.');
                } else {
                    $io->warning('Settings already exist in ' . $path . 'ignoring.');
                }
            }

            if (class_exists('Bone\BoneDoctrine\BoneDoctrinePackage')) {
                $io->writeln('Generating database migration for ' . $package . '.');
                $this->runProcess($io, ['vendor/bin/bone', 'm:diff']);

                $io->writeln('Running migrations...');
                $this->runProcess($io, ['vendor/bin/bone', 'm:migrate']);

                $io->writeln('Generating Proxies...');
                $this->runProcess($io, ['vendor/bin/bone', 'm:generate-proxies']);
            }

            if ($instance instanceof FixtureProviderInterface) {
                $io->writeln('Loading fixtures...');
                $this->runProcess($io, ['vendor/bin/bone', 'm:vendor-fixtures', '--package=' . $package]);
            }

            $io->writeln('Deploying assets...');
            $this->runProcess($io, ['vendor/bin/bone', 'assets:deplpoy']);

            if ($instance instanceof DefaultSettingsProviderInterface) {
                $instance->postInstall($this, $io);
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $io->error([$e->getMessage(), $e->getFile() . ':' . $e->getLine()]);

            return self::FAILURE;
        }
    }


    private function enablePackage(string $package): void
    {
        $packages = include 'config/packages.php';
        $appPackage = array_pop($packages['packages']);
        $instance = new $package();

        if ($instance instanceof DefaultSettingsProviderInterface) {
            $dependencies = $instance->getRequiredPackages();

            foreach ($dependencies as $dependency) {
                if (!in_array($dependency, $packages['packages'])) {
                    $packages['packages'][] = $dependency;
                }
            }
        } else if (!in_array($package, $packages['packages'])) {
            $packages['packages'][] = $package;
        }

        $packages['packages'][] = $appPackage;
        $this->exportArray($packages);
    }
}
