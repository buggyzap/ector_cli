<?php

namespace Ector\Cli\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class DeleteMagentoMigration extends Command
{
    protected static $defaultName = 'magento_migration:delete';

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        // check if table exists
        $sql = "SELECT * FROM information_schema.tables WHERE table_schema = '" . _DB_NAME_ . "' AND table_name = '" . _DB_PREFIX_ . "lgseoredirect'";
        $result = \Db::getInstance()->executeS($sql);

        if (empty($result)) {
            throw new \Exception("Redirect table not found, please check that the lgseoredirect module is installed.");
        }
    }

    protected function configure()
    {
        $this
            ->setDescription('Delete Magento migration')
            ->setHelp('This command deletes Magento permalinks stored in your PrestaShop database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $output->writeln("This command will delete Magento permalinks stored in your PrestaShop database to cleanup data. \n \n");

        $confirm = $helper->ask($input, $output, new Question('Are you sure you want to delete Magento permalinks? This action is irreversible. (y/n) ', 'n'));
        if ($confirm  === "n") {
            throw new \Exception("Magento permalinks deletion aborted.");
        }

        $sql = "DELETE FROM " . _DB_PREFIX_ . "lgseoredirect";
        \Db::getInstance()->execute($sql);


        return 0;
    }
}
