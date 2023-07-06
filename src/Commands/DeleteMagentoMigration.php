<?php

namespace Ector\Cli\Commands;

use Ector\Cli\Classes\Commands_Operations\Delete_Magento_Migration;
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
            ->setHelp('This command deletes magento urls from your PrestaShop Database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Deleting Magento migration urls from database...');

        try {
            Delete_Magento_Migration::execute();
            return 0;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
