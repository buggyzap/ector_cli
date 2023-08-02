<?php

namespace Ector\Cli\Classes\CommandsOperations {

    class DeleteMagentoMigration extends CommandOperations
    {
        public static function execute()
        {
            $db = \Db::getInstance();

            $sql = "DELETE FROM " . _DB_PREFIX_ . "lgseoredirect WHERE is_magento=1";

            try {
                $db->execute($sql);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
    }
}
