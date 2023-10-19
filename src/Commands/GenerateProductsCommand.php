<?php

namespace Ector\Cli\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\ProgressBar;

class GenerateProductsCommand extends Command
{
    protected static $defaultName = 'generate:products';

    protected function configure()
    {
        $this
            ->setDescription('Generate dummy products')
            ->setHelp('This command generate dummy products')
            ->addArgument('number', InputArgument::REQUIRED, 'Number of products to generate');
    }

    protected static function get_best_path($tgt_width, $tgt_height, $path_infos)
    {
        $path_infos = array_reverse($path_infos);
        $path = '';
        foreach ($path_infos as $path_info) {
            list($width, $height, $path) = $path_info;
            if ($width >= $tgt_width && $height >= $tgt_height) {
                return $path;
            }
        }

        return $path;
    }

    protected static function copyImg($id_entity, $image_obj, $url = '', $entity = 'products', $regenerate = true)
    {
        $tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
        $path = $image_obj->getPathForCreation();
        $url = trim($url);
        $orig_tmpfile = $tmpfile;

        if (@copy($url, $tmpfile)) {
            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (!\ImageManager::checkImageMemoryLimit($tmpfile)) {
                @unlink($tmpfile);
                return false;
            }

            $tgt_width = $tgt_height = 0;
            $src_width = $src_height = 0;
            $error = 0;
            \ImageManager::resize($tmpfile, $path . '.jpg', null, null, 'jpg', false, $error, $tgt_width, $tgt_height, 5, $src_width, $src_height);
            $images_types = \ImageType::getImagesTypes($entity, true);

            if ($regenerate) {
                $path_infos = [];
                $path_infos[] = [$tgt_width, $tgt_height, $path . '.jpg'];
                foreach ($images_types as $image_type) {
                    $tmpfile = self::get_best_path($image_type['width'], $image_type['height'], $path_infos);

                    if (\ImageManager::resize(
                        $tmpfile,
                        $path . '-' . stripslashes($image_type['name']) . '.jpg',
                        $image_type['width'],
                        $image_type['height'],
                        'jpg',
                        false,
                        $error,
                        $tgt_width,
                        $tgt_height,
                        5,
                        $src_width,
                        $src_height
                    )) {
                        if ($tgt_width <= $src_width && $tgt_height <= $src_height) {
                            $path_infos[] = [$tgt_width, $tgt_height, $path . '-' . stripslashes($image_type['name']) . '.jpg'];
                        }
                        if ($entity == 'products') {
                            if (is_file(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $id_entity . '.jpg')) {
                                unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $id_entity . '.jpg');
                            }
                            if (is_file(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $id_entity . '_' . (int) \Context::getContext()->shop->id . '.jpg')) {
                                unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $id_entity . '_' . (int) \Context::getContext()->shop->id . '.jpg');
                            }
                        }
                    }
                }
            }
        } else {
            @unlink($orig_tmpfile);
            return false;
        }
        unlink($orig_tmpfile);
        return true;
    }

    public function createImage($id_product, $image)
    {
        $imageObject = new \Image();
        $imageObject->id_product = $id_product;
        if (!\Db::getInstance()->getValue("SELECT id_image FROM " . _DB_PREFIX_ . "image WHERE id_product = '$id_product' AND cover = 1")) $imageObject->cover = 1;
        if (($imageObject->validateFields(false, true)) === true && ($imageObject->validateFieldsLang(false, true)) === true && $imageObject->add()) {
            self::copyImg($id_product, $imageObject, $image, 'products');
        }
        return $imageObject->id;
    }


    private function generateProduct()
    {
        $product = new \Product();
        $product->name = \Tools::passwdGen(10);
        $product->link_rewrite = \Tools::link_rewrite($product->name);
        $product->price = rand(1, 1000);
        $product->active = 1;

        // set random image from API
        $images = json_decode(file_get_contents('https://picsum.photos/v2/list'), true);
        $image = $images[rand(0, count($images) - 1)];

        $product->save();

        $this->createImage($product->id, $image['download_url']);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $number = $input->getArgument('number');

        $progressBar = new ProgressBar($output, $number);
        $progressBar->start();

        for ($i = 0; $i < $number; $i++) {

            try {
                $this->generateProduct();
                $progressBar->advance();
            } catch (\Throwable $th) {
                $output->writeln($th->getMessage());
            }
        }

        $progressBar->finish();


        return 0;
    }
}
