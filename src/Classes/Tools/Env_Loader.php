<?php

namespace Ector\Cli\Classes\Tools {

    use Dotenv\Dotenv;

    class Env_Loader extends Tool
    {
        public static function employ()
        {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
            return $dotenv->load();
        }
    }
}
