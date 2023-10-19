<?php

/**
 * Ector Cli
 * 
 * @author DGCAL SRL <m.ingraiti@dgcal.it>
 * @version 0.0.1
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . "/vendor/autoload.php"))
    require_once __DIR__ . "/vendor/autoload.php";

class Ector_cli extends Module
{

    private $checker;

    public function __construct()
    {
        $this->initializeModule();
        if ($this->checker === null && $this->context->controller instanceof AdminController) {
            $this->checker = $this->get("ector.checker");
        }
    }

    public function install()
    {
        return
            parent::install() &&
            $this->registerHook('actionAdminControllerInitAfter');
    }

    public function uninstall()
    {
        return
            parent::uninstall() &&
            $this->unregisterHook('actionAdminControllerInitAfter');
    }

    public function hookActionAdminControllerInitAfter($params)
    {
        $controller = $params["controller"];
        $this->checker->healthCheck($controller);
    }

    private function initializeModule()
    {
        $this->name = 'ector_cli';
        $this->tab = 'front_office_features';
        $this->version = '0.0.1';
        $this->author = 'DGCAL SRL';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Ector CLI');
        $this->description = $this->l('CLI to help developers to use helper functions and commands');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }
}