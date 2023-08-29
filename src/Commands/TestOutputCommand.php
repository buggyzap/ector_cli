<?php

namespace Ector\Cli\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestOutputCommand extends Command
{
    protected static $defaultName = 'execute:test';

    protected function configure()
    {
        $this
            ->setDescription('Test Command')
            ->setHelp('This command is used for debugging and testing.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Test command executed successfully!</info>');
        return 0;
    }
}
