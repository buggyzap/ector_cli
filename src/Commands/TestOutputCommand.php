<?php

namespace Ector\Cli\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;
use Ector\Cli\Classes\MigrateMagentoClass;
use Ector\Cli\Classes\PdoConnection as PDO;

class TestOutputCommand extends Command
{
    protected static $defaultName = 'execute:test';

    protected function configure()
    {
        $this
            ->setDescription('Test Command')
            ->setHelp('This command is used for debugging and testing.');

        // Additional command configuration specific to PrestaShop migration
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $auth = require(__DIR__ . '/../PDO_auth.php');
        var_dump($auth['host']);
        return 0;
    }
}
