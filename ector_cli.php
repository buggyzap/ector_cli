<?php

/**
 * Ector Cli - A Prestashop Module
 * 
 * @author DGCAL SRL <m.ingraiti@dgcal.it>
 * @version 0.0.1
 */

if (!defined('_PS_VERSION_')) exit;

require_once('vendor/autoload.php');

class Ector_cli extends Module
{

    public function __construct()
    {
        $this->initializeModule();
    }

    public function install()
    {
        return
            parent::install();
    }

    public function uninstall()
    {
        return
            parent::uninstall();
    }


    /** Initialize the module declaration */
    private function initializeModule()
    {
        $this->name = 'ector_cli';
        $this->tab = 'front_office_features';
        $this->version = '0.0.1';
        $this->author = 'DGCAL SRL';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Ector Cli');
        $this->description = $this->l('Cli to help developers to use helper functions and commands');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }
}
