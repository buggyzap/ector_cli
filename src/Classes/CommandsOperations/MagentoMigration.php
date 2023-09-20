<?php

namespace Ector\Cli\Classes\CommandsOperations;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class MagentoMigration
{

    private $conn;
    private $urls;
    private $prefix;
    private $storeId;
    private $link;

    public function __construct(\PDO $conn, string $prefix, int $storeId)
    {
        $this->conn = $conn;
        $this->urls = [];
        $this->prefix = $prefix;
        $this->storeId = $storeId;
        $this->link = new \Link();
    }

    /**
     * Fetch all urls from Magento database
     * @param string $tableName
     * @return array $urls
     */
    public function fetchFromMagento(string $tableName, int $storeId)
    {
        $sql = "SELECT * FROM $tableName WHERE entity_type = 'product' AND store_id = $storeId AND target_path NOT LIKE '%category/2'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $this->urls = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {

            // add entity_id to main array
            if (!in_array($row["entity_id"], array_keys($this->urls))) {
                $this->urls[$row["entity_id"]] = [
                    "main" => [],
                    "others" => []
                ];
            }

            // check if request path follow the pattern catalog/product/view/id/1234 so is the product page, then add to main array
            if (preg_match('/catalog\/product\/view\/id\/\d+$/', $row['target_path'])) {
                $this->urls[$row["entity_id"]]["main"][] = $row['request_path'];
            } else $this->urls[$row["entity_id"]]["others"][] = $row['request_path'];
        }

        return $this->urls;
    }

    private function getSkus()
    {
        $sql = "SELECT id_product, reference FROM " . _DB_PREFIX_ . "product";
        $result = \Db::getInstance()->executeS($sql);

        $skus = [];

        foreach ($result as $row) {
            $skus[$row["id_product"]] = $row["reference"];
        }

        return $skus;
    }

    private function getMagentoUrlsBySku($sku)
    {
        // get entity_id by sku
        $sql = "SELECT entity_id FROM {$this->prefix}catalog_product_entity WHERE sku = '$sku'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $entityId = $stmt->fetch(\PDO::FETCH_ASSOC)["entity_id"] ?? null;

        if (empty($entityId)) return null;

        $sql = "SELECT * FROM {$this->prefix}url_rewrite WHERE entity_type = 'product' AND store_id = $this->storeId AND target_path NOT LIKE '%category/2' AND entity_id = $entityId";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $urls = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {

            // add entity_id to main array
            if (!in_array($row["entity_id"], array_keys($urls))) {
                $urls[$row["entity_id"]] = [
                    "main" => [],
                    "others" => []
                ];
            }

            // check if request path follow the pattern catalog/product/view/id/1234 so is the product page, then add to main array
            if (preg_match('/catalog\/product\/view\/id\/\d+$/', $row['target_path'])) {
                $urls[$row["entity_id"]]["main"][] = $row['request_path'];
            } else $urls[$row["entity_id"]]["others"][] = $row['request_path'];
        }

        return $urls;
    }

    private function getPrestashopUrlBySku($sku)
    {
        return $this->link->getProductLink((int)\Product::getIdByReference($sku));
    }

    public function execute()
    {
        $skus = $this->getSkus();

        $output = new ConsoleOutput();
        $progressBar = new ProgressBar($output, count($skus));
        $progressBar->start();

        $failed = 0;

        foreach ($skus as $sku) {

            $progressBar->advance();

            $urls = $this->getMagentoUrlsBySku($sku, $this->prefix, $this->storeId);
            $ps_url = $this->getPrestashopUrlBySku($sku);
            if ($urls === null) {
                $failed++;
                continue;
            }

            // foreach urls create a redirect
            foreach ($urls as $url) {
                $urls_to_convert = array_merge($url["main"], $url["others"]);
                foreach ($urls_to_convert as $url_to_convert) {
                    $this->createRedirect($url_to_convert, $ps_url);
                }
            }
        }

        $progressBar->finish();

        $output->writeln("\n\n <info>Magento migration completed successfully! $failed records failed without url matches.</info>");
    }

    private function createRedirect($path, $new)
    {

        $shops = \Shop::getShops(true, null, true);

        foreach ($shops as $shop) {

            // add / at the start of path if not present
            if (strpos($path, "/") !== 0) $path = "/" . $path;

            return \Db::getInstance()->insert(
                "lgseoredirect",
                [
                    "url_old" => $path,
                    "url_new" => $new,
                    "redirect_type" => 301,
                    "update" => new \DateTime("Y-m-d H:i:s"),
                    "id_shop" => $shop["id_shop"],
                    "pnf" => 0
                ]
            );
        }
    }

    public function getUrls()
    {
        return $this->urls;
    }
}
