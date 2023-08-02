<?php

namespace Ector\Cli\Classes\CommandsOperations {

    use Ector\Cli\Classes\Tools\PdoConnection as PDO;
    use Symfony\Component\Console\Helper\ProgressBar;
    use Symfony\Component\Console\Output\ConsoleOutput;

    class MagentoMigration extends CommandOperations
    {


        public static function execute()
        {
            $product_urls = self::fetchFromMagentoDb();

            self::writeInPrestashopDb($product_urls);
        }

        private static function fetchFromMagentoDb()
        {
            $PDO = PDO::employ();

            $sql = "SELECT request_path FROM mg_url_rewrite WHERE request_path LIKE '/%.html';";

            try {
                $stmt = $PDO->prepare($sql);
                $stmt->execute();
            } catch (\Exception $e) {
                echo $e->getMessage();
            }

            $product_urls = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                if (in_array($row['request_path'], $product_urls)) continue;
                $product_urls[] = $row['request_path'];
            }

            return $product_urls;
        }

        private static function writeInPrestashopDb($product_urls)
        {
            $db = \Db::getInstance();

            $output = new ConsoleOutput();
            $progressBar = new ProgressBar($output, count($product_urls));
            $progressBar->start();

            foreach ($product_urls as $url_old) {
                $sql = "INSERT INTO " . _DB_PREFIX_ . "lgseoredirect (url_old, redirect_type, is_magento) VALUES ('$url_old', 301, 1)";
                try {
                    $db->execute($sql);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                    continue;
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            $output->writeln("<info>\nMigration completed!</info>");
        }
    }
}
