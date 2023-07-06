<?php

namespace Ector\Cli\Classes\Tools {

    use Symfony\Component\Console\Output\ConsoleOutput;

    class Pdo_Connection extends Tool
    {
        public static function employ()
        {
            $output = new ConsoleOutput();

            $host = $_ENV['DB_HOST'];
            $db_name = $_ENV['DB_NAME_MG'];
            $username = $_ENV['DB_USERNAME'];
            $password = $_ENV['DB_PASSWORD'];

            $dsn = 'mysql:host=' . $host . ';dbname=' . $db_name;

            try {
                $PDO = new \PDO($dsn, $username, $password);
                $output->writeln('<info>Magento database connected successfully!</info>');
                return $PDO;
            } catch (\PDOException $e) {
                $output->writeln('<error>Magento database connection failed : </error> ' . $e->getMessage());
            }
        }
    }
}
