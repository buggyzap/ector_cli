<?php

namespace Ector\Cli\Commands;

use Ector\Cli\Classes\CommandsOperations\DeleteMagentoMigration as DeleteMagento;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteMagentoMigration extends Command
{
    protected static $defaultName = 'magento_migration:delete';

    protected function configure()
    {
        $this
            ->setDescription('Delete Magento migration')
            ->setHelp('This command deletes Magento permalinks stored in your PrestaShop database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Deleting Magento migration urls from database...');

        try {
            DeleteMagento::execute();
            $output->writeln('<info>Operation completed!</info>');
            return 0;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
