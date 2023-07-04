<?php

namespace Ector\Cli\Classes {

    use Ector\Cli\Classes\PdoConnection as PDO;
    use Symfony\Component\Console\Helper\QuestionHelper;
    use Symfony\Component\Console\Question\Question;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Output\OutputInterface;

    class MigrateMagentoClass
    {
        public static function RetrieveMagentoProductsUrls()
        {
            $PDO = PDO::executeConnection();

            $sql = "SELECT mg_url_rewrite.request_path FROM mg_catalog_product_entity INNER JOIN mg_url_rewrite ON mg_catalog_product_entity.entity_id = mg_url_rewrite.url_rewrite_id WHERE mg_catalog_product_entity.type_id = 'configurable' AND mg_url_rewrite.store_id = 1 AND mg_url_rewrite.target_path LIKE '%view/id%';";

            $stmt = $PDO->prepare($sql);
            $stmt->execute();

            $product_urls = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $product_urls[] = $row['request_path'];
            }

            return $product_urls;
        }

        public static function InsertMagentoUrlsInPrestashopDb()
        {
            $magento_urls = self::RetrieveMagentoProductsUrls();
        }
    }
}
