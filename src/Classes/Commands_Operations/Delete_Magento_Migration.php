<?php

namespace Ector\Cli\Classes\Commands_Operations {

    class Delete_Magento_Migration extends Command_Operations
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
