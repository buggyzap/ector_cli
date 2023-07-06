<?php

namespace Ector\Cli\Commands;

use Ector\Cli\Classes\Commands_Operations\Magento_Migration;
use Ector\Cli\Classes\Tools\Pdo_Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateMagentoCommand extends Command
{
    protected static $defaultName = 'magento_migration:execute';

    protected function configure()
    {
        $this
            ->setDescription('Migrate from Magento to Prestashop')
            ->setHelp('This command migrates products url from Magento to your PrestaShop database.');

        // Additional command configuration specific to Magento migration
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        // Connect to the database
        Pdo_Connection::employ();

        $output->writeln('<info>Database connected successfully!</info>');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Migrating from Magento to PrestaShop...');

        try {
            Magento_Migration::execute();
            return 0;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
