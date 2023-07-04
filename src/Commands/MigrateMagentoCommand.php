<?php

namespace Ector\Cli\Commands;

use Ector\Cli\Classes\MigrateMagentoClass;
use Ector\Cli\Classes\PdoConnection as PDO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\Question;

class MigrateMagentoCommand extends Command
{
    protected static $defaultName = 'migrate:magento';

    protected function configure()
    {
        $this
            ->setDescription('Migrate from Magento to Prestashop')
            ->setHelp('This command migrates data from Magento to PrestaShop.');

        // Additional command configuration specific to Magento migration
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Migrating from Magento to PrestaShop...');
        // Logic for Magento migration

        // $output->writeLn("Database name: {$input->getArgument('magento')}");

        // $section1 = $output->section();
        // $section2 = $output->section();

        // $progress1 = new ProgressBar($section1);
        // $progress2 = new ProgressBar($section2);

        // $progress1->start(100);
        // $progress2->start(100);

        // $i = 0;
        // while (++$i < 100) {
        //     $progress1->advance();

        //     if ($i % 2 === 0) {
        //         $progress2->advance(4);
        //     }

        //     usleep(50000);
        // }

        // throw new Exception('Non ci siamo');


        try {
            $magento_product_urls = MigrateMagentoClass::RetrieveMagentoProductsUrls();
            var_dump($magento_product_urls);
            $output->writeln("Success!", 1);
            return 0;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
