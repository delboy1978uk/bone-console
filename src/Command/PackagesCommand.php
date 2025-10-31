<?php

declare(strict_types=1);

namespace Bone\Console\Command;

use Barnacle\Container;
use Bone\Console\Command;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class PackagesCommand extends Command
{
    private array $packages = [
        'boneframework/debug-bar' => [
            'class' => 'Bone\DebugBar\DebugBarPackage',
            'description' => 'PHP Debug toolbar'
        ],
        'delboy1978uk/bone-address' => [
            'class' => '',
            'description' => 'Address database and management.'
        ],
        'delboy1978uk/bone-calendar' => [
            'class' => '',
            'description' => 'Calendar functionality.'
        ],
        'delboy1978uk/bone-contact' => [
            'class' => '',
            'description' => 'Contact page and form'
        ],
        'delboy1978uk/bone-crypt' => [
            'class' => '',
            'description' => 'OpenSSL encryption.'
        ],
        'delboy1978uk/bone-doctrine' => [
            'class' => 'Bone\BoneDoctrine\BoneDoctrinePackage',
            'description' => 'Doctrine ORM.'
        ],
        'delboy1978uk/bone-mail' => [
            'class' => 'Bone\Mail\MailPackage',
            'description' => 'Mail server functionality.'
        ],
        'delboy1978uk/bone-oauth2' => [
            'class' => 'Bone\OAuth2\BoneOAuth2Package',
            'description' => 'OAuth2 based authentication server'
        ],
        'delboy1978uk/bone-openapi' => [
            'class' => 'Bone\OpenApi\OpenApiPackage',
            'description' => 'OpenAPI API doc generator using Typespec.'
        ],
        'delboy1978uk/bone-paseto' => [
            'class' => 'Bone\Paseto\PasetoPackage',
            'description' => 'Platform agnostic security tokens.'
        ],
        'delboy1978uk/bone-passport' => [
            'class' => 'Bone\Passport\PassportPackage',
            'description' => 'ACL role based authorization.'
        ],
        'delboy1978uk/bone-pay' => [
            'class' => '',
            'description' => 'Payment gateway provider.'
        ],
        'delboy1978uk/bone-push-notifications' => [
            'class' => '',
            'description' => 'Push Notifications using Expo for Bone Native apps.'
        ],
        'delboy1978uk/bone-settings' => [
            'class' => '',
            'description' => 'Generic settings entity.'
        ],
        'delboy1978uk/bone-social-auth' => [
            'class' => '',
            'description' => 'Social auth via OAuth2 providers.'
        ],
        'delboy1978uk/bone-user' => [
            'class' => 'Bone\User\BoneUserPackage',
            'description' => 'User registation, activation, and login functionality.'
        ],
        'delboy1978uk/bone-user-api' => [
            'class' => 'Bone\BoneUserApi\BoneUserApiPackage',
            'description' => 'API endpoints for bone-user.'
        ],
    ];

    public function __construct(
        private array $installedPackages
    ) {
        parent::__construct('packages:install');
    }

    protected function configure(): void
    {
        $this->setDescription('Install official Bone Framework packages.');
        $this->setHelp('Install official Bone Framework packages.');
        $this->addArgument('package', InputArgument::OPTIONAL, 'Package name.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->getIo($input, $output);
        $io->title('💀 Bone package installer');
        $package = $input->getArgument('package');
        $availablePackages = [];
        $installedPackages = [];
        $choices = [];

        foreach ($this->packages as $name => $info) {
            if (in_array($info['class'], $this->installedPackages)) {
                $installedPackages[] = [$name, $info['description']];
            } else {
                $availablePackages[] = [$name, $info['description']];
                $choices[] = $name;
            }
        }

        if ($package && in_array($package, $choices)) {
            $io->writeln('Installing package: '.$package);
            $process = new Process(['composer', 'require', $package]);
            $process->enableOutput();
            $process->run(function ($type, $buffer) use ($io): void {
                $io->write($buffer);
            });
        } else {
            if (count($installedPackages) > 0) {
                $io->writeln('Installed packages:');
                $io->table(['Name', 'Description'], $installedPackages);
            }

            $io->writeln('Available packages:');
            $io->table(['Name', 'Description'], $availablePackages);
        }

        return self::SUCCESS;
    }
}
