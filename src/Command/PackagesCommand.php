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

class PackagesCommand extends AbstractPackageCommand
{
    private array $packages = [
        'boneframework/debug-bar' => [
            'class' => 'Bone\DebugBar\DebugBarPackage',
            'description' => 'PHP Debug toolbar',
        ],
        'delboy1978uk/bone-address' => [
            'class' => '',
            'description' => 'Address database and management.',
        ],
        'delboy1978uk/bone-calendar' => [
            'class' => '',
            'description' => 'Calendar functionality.',
        ],
        'delboy1978uk/bone-contact' => [
            'class' => '',
            'description' => 'Contact page and form',
        ],
        'delboy1978uk/bone-crypt' => [
            'class' => '',
            'description' => 'OpenSSL encryption.',
        ],
        'delboy1978uk/bone-doctrine' => [
            'class' => 'Bone\BoneDoctrine\BoneDoctrinePackage',
            'description' => 'Doctrine ORM.',
        ],
        'delboy1978uk/bone-mail' => [
            'class' => 'Bone\Mail\MailPackage',
            'description' => 'Mail server functionality.',
        ],
        'delboy1978uk/bone-oauth2' => [
            'class' => 'Bone\OAuth2\BoneOAuth2Package',
            'description' => 'OAuth2 based authentication server',
        ],
        'delboy1978uk/bone-open-api' => [
            'class' => 'Bone\OpenApi\OpenApiPackage',
            'description' => 'OpenAPI API doc generator using Typespec.',
        ],
        'delboy1978uk/bone-paseto' => [
            'class' => 'Bone\Paseto\PasetoPackage',
            'description' => 'Platform agnostic security tokens.',
        ],
        'delboy1978uk/bone-passport' => [
            'class' => 'Bone\Passport\PassportPackage',
            'description' => 'ACL role based authorization.',
        ],
        'delboy1978uk/bone-pay' => [
            'class' => '',
            'description' => 'Payment gateway provider.',
        ],
        'delboy1978uk/bone-push-notifications' => [
            'class' => '',
            'description' => 'Push Notifications using Expo for Bone Native apps.',
        ],
        'delboy1978uk/bone-settings' => [
            'class' => '',
            'description' => 'Generic settings entity.',
        ],
        'delboy1978uk/bone-social-auth' => [
            'class' => '',
            'description' => 'Social auth via OAuth2 providers.',
        ],
        'delboy1978uk/bone-user' => [
            'class' => 'Bone\User\BoneUserPackage',
            'description' => 'User registation, activation, and login functionality.',
        ],
        'delboy1978uk/bone-user-api' => [
            'class' => 'Bone\BoneUserApi\BoneUserApiPackage',
            'description' => 'API endpoints for bone-user.',
        ],
    ];

    public function __construct(
        private array $installedPackages,
    ) {
        parent::__construct('packages:install');
    }

    protected function configure(): void
    {
        $this->setDescription('Install official Bone Framework packages.');
        $this->setHelp('Install official Bone Framework packages.');
        $this->addOption('remove', 'r', InputOption::VALUE_NONE, 'Removes package.');
        $this->addOption('disable', 'd', InputOption::VALUE_NONE, 'Disables a package without uninstalling it.');
        $this->addOption('enable', 'e', InputOption::VALUE_NONE, 'Re-enable a package.');
        $this->addArgument('package', InputArgument::OPTIONAL, 'Package name.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $io = $this->getIo($input, $output);
            $io->title('💀 Bone package installer');
            $package = $input->getArgument('package');
            $remove = $input->getOption('remove');
            $disable = $input->getOption('disable');
            $enable = $input->getOption('enable');

            if ($remove) {
                return $this->removePackage($io, $package);
            }

            if ($disable) {
                return $this->disablePackage($io, $package);
            }

            if ($enable) {
                $packageName = $this->packages[$package]['class'];

                return $this->enablePackage($io, $packageName);
            }

            $availablePackages = [];
            $installedPackages = [];
            $choices = [];
            $installed = [];

            foreach ($this->packages as $name => $info) {
                if (in_array($info['class'], $this->installedPackages)) {
                    $installedPackages[] = [$name, $info['description']];
                    $installed[] = $name;
                } else {
                    $availablePackages[] = [$name, $info['description']];
                    $choices[] = $name;
                }
            }

            if ($package && in_array($package, $choices)) {
                $io->writeln('Installing package: ' . $package);
                $process = $this->runProcess($io, ['composer', 'require', $package]);

                if ($process->isSuccessful()) {
                    $io->success('Successfully installed package: ' . $package);
                    $packageName = $this->packages[$package]['class'];
                    $io->info('Enabling package in `config/packages.php`');
                    $this->postInstall($io, $packageName);
                    $io->success('Successfully installed and configured package: ' . $packageName);
                } else {
                    $io->error('Failed installing package: ' . $package);

                    return self::FAILURE;
                }
            } else {
                if ($package && in_array($package, $installed)) {
                    $io->warning('Package ' . $package . ' is already in `config/packages.php`');

                    return self::SUCCESS;
                }

                if (count($installedPackages) > 0) {
                    $io->writeln('Installed packages:');
                    $io->table(['Name', 'Description'], $installedPackages);
                }

                $io->writeln('Available packages:');
                $io->table(['Name', 'Description'], $availablePackages);
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $io->error([$e->getMessage(), $e->getFile() . ':' . $e->getLine()]);

            return self::FAILURE;
        }
    }

    private function removePackage(SymfonyStyle $io, string $package): int
    {
        $this->runProcess($io, ['composer', 'remove', $package]);
        $this->disablePackage($io, $package);

        return self::SUCCESS;
    }

    private function enablePackage(SymfonyStyle $io, string $package): int
    {
        $this->runProcess($io, ['vendor/bin/bone', 'packages:post-install', $package, '--enable-only']);

        return self::SUCCESS;
    }

    private function disablePackage(SymfonyStyle $io, string $package): int
    {
        $packageClass = $this->packages[$package]['class'];
        $packages = include 'config/packages.php';

        foreach ($packages['packages'] as $index => $name) {
            if ($name === $packageClass) {
                unset($packages['packages'][$index]);
            }
        }

        $this->exportArray($packages);
        $io->success('Successfully disabled package: ' . $package);

        return self::SUCCESS;
    }

    private function postInstall(SymfonyStyle $io, string $package): void
    {
        $io->writeln('Running post install tasks for ' . $package);
        $this->runProcess($io, ['vendor/bin/bone', 'packages:post-install', $package]);
    }
}
