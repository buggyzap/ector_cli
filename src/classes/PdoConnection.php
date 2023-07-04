<?php

namespace Ector\Cli\Classes {

    class PdoConnection
    {
        public static function executeConnection()
        {
            $auth = require(__DIR__ . '/../../PDO_auth.php');

            $host = $auth['host'];
            $db_name = $auth['dbname'];
            $username = $auth['username'];
            $password = $auth['password'];

            $dsn = 'mysql:host=' . $host . ';dbname=' . $db_name;

            try {
                return new \PDO($dsn, $username, $password);
            } catch (\PDOException $e) {
                echo 'Connection failed : ' . $e->getMessage();
            }
        }
    }
}
