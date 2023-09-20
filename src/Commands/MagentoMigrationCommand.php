<?php

namespace Ector\Cli\Commands;

use Ector\Cli\Classes\CommandsOperations\MagentoMigration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Helper\Table;

class MagentoMigrationCommand extends Command
{
    protected static $defaultName = 'magento_migration:execute';
    const TEST_MODE = false;
    const TEST_CREDENTIALS = [
        "xxx.xxx.xxx.xxx",
        "user",
        "password",
        "database"
    ];

    protected function configure()
    {
        $this
            ->setDescription('Migrate from Magento to Prestashop URLS')
            ->setHelp('This command migrates Magento permalinks to Prestashop urls and create 301 redirects dinamically, writing those records in your PrestaShop database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $helper = $this->getHelper('question');
        $output->writeln("This command will copy Magento permalinks stored in your Magento database to cleanup data. Please double check that your sku on Magento is the same as Prestashop. \n \n Please enter your database credentials \n");

        if (!self::TEST_MODE) {
            $dbHostname = $helper->ask($input, $output, new Question('Enter the database hostname: '));
            $dbUsername = $helper->ask($input, $output, new Question('Enter the database username: '));
            $dbPassword = $helper->ask($input, $output, new Question('Enter the database password: '));
            $dbName = $helper->ask($input, $output, new Question('Enter the database name: '));
        } else list($dbHostname, $dbUsername, $dbPassword, $dbName) = self::TEST_CREDENTIALS;

        $dsn = 'mysql:host=' . $dbHostname . ';dbname=' . $dbName;

        try {
            $PDO = new \PDO($dsn, $dbUsername, $dbPassword);
            $output->writeln('<info>Magento database connected successfully!</info>');
        } catch (\PDOException $e) {
            $output->writeln('<error>Magento database connection failed : </error> ' . $e->getMessage());
        }

        $prefix = $helper->ask($input, $output, new Question('Enter your table prefix if any: '));

        if (empty($prefix)) {
            throw new \Exception("Prefix name cannot be empty.");
        }

        $storeId = $helper->ask($input, $output, new Question('Enter the store id: ', 1));

        $magentoMigration = new MagentoMigration($PDO, $prefix, $storeId);
        $failed = $magentoMigration->execute();

        if (count($failed) > 0) {
            $table = new Table($output);
            $table
                ->setHeaders(['N', 'SKU', 'Prestashop URL'])
                ->setRows($failed);
            $table->render();
        }

        return 0;
    }
}
