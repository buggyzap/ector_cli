<?php

namespace Ector\Cli\Classes\Tools {

    use Ector\Cli\Classes\Tools\Env_Loader;

    class Pdo_Connection extends Tool
    {
        public static function employ()
        {
            $host = $_ENV['DB_HOST'];
            $db_name = $_ENV['DB_NAME_MG'];
            $username = $_ENV['DB_USERNAME'];
            $password = $_ENV['DB_PASSWORD'];

            $dsn = 'mysql:host=' . $host . ';dbname=' . $db_name;

            try {
                return new \PDO($dsn, $username, $password);
            } catch (\PDOException $e) {
                echo 'Connection failed : ' . $e->getMessage();
            }
        }
    }
}
