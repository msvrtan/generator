<?php

declare(strict_types=1);

namespace NullDev\Skeleton\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AddBoundaryCommand extends BaseBoundaryCommand
{
    protected function configure()
    {
        $this->setName('boundary:add')
            ->setDescription('Add boundary')
            ->addOption('namespace', null, InputOption::VALUE_REQUIRED, 'Boundary namespace')
            ->addOption('model', null, InputOption::VALUE_REQUIRED, 'Boundary model name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->io    = new SymfonyStyle($input, $output);

        $config = $this->readClassConfig();

        $namespace = $this->askBoundaryNamespace();

        $model = $this->askModelName();

        $config['boundaries'][$namespace] = [
            'model'    => $model,
            'commands' => [
                'Add'.$model => [
                    'properties' => [
                        $namespace.'\\Core\\'.$model.'Id' => $model.'Id',
                        $namespace.'\\Core\\Something'    => 'Something',
                    ],
                ],
            ],
            'events'   => [
                $model.'Added' => [
                    'properties' => [
                        $namespace.'\\Core\\'.$model.'Id' => $model.'Id',
                        $namespace.'\\Core\\Something'    => 'Something',
                        'DateTime'                        => 'AddedAt',
                    ],
                ],
            ],
            'read'     => null,
        ];

        $this->writeClassConfig($config);
    }

    private function askBoundaryNamespace(): string
    {
        $namespace = $this->input->getOption('namespace');
        if (null !== $namespace) {
            return $namespace;
        }

        return $this->io->ask('Namespace:');
    }

    private function askModelName(): string
    {
        $model = $this->input->getOption('model');
        if (null !== $model) {
            return $model;
        }

        return $this->io->ask('Model name:');
    }

    protected function getSectionMessage(): string
    {
        return 'Add new boundary';
    }

    protected function getIntroductionMessage(): array
    {
        return [
            '',
            'This command helps adding new boundary.',
            '',
        ];
    }
}
