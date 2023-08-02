<?php

namespace Ector\Cli\Commands;

use Ector\Cli\Classes\CommandsOperations\MagentoMigration;
use Ector\Cli\Classes\Tools\Env_Loader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MagentoMigrationCommand extends Command
{
    protected static $defaultName = 'magento_migration:execute';

    protected function configure()
    {
        $this
            ->setDescription('Migrate from Magento to Prestashop')
            ->setHelp('This command migrates Magento permalinks to Prestashop urls and create 301 redirects dinamically, writing those records in your PrestaShop database.');

        // Additional command configuration specific to Magento migration
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        Env_Loader::employ();
        $output->writeln('<info>Env loaded successfully!</info>');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::initialize($input, $output);

        $output->writeln('Migrating from Magento to PrestaShop...');

        try {
            MagentoMigration::execute();
            return 0;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
