<?php

declare(strict_types=1);

namespace NullDev\Skeleton\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MiroCommand extends BaseBoundaryCommand
{
    protected function configure()
    {
        $this->setName('miro')
            ->setDescription('testing command');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;
        $this->io     = new SymfonyStyle($input, $output);

        $config = $this->readClassConfig();

        dump($config);
    }

    protected function getSectionMessage(): string
    {
        return 'xxxx';
    }

    protected function getIntroductionMessage(): array
    {
        return [
            '',
            'yyyy',
        ];
    }
}
