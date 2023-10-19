<?php

namespace Ector\Cli\Classes\Override;

use OverrideTemplate;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class Override
{

    private $filesystem;
    private $finder;
    private $name;
    private $file;

    const OVERRIDE_DIR = _PS_ROOT_DIR_ . "/ector_overrides/";
    public function __construct(string $moduleName)
    {
        $this->filesystem = new Filesystem();
        $this->finder = new Finder();
        $this->name = $moduleName;
        $this->file = self::OVERRIDE_DIR . $this->name . '/' . $this->name . '.php';
    }

    public function getFile()
    {
        return $this->file;
    }
    public function draftFile()
    {
        $this->filesystem->mkdir(self::OVERRIDE_DIR . $this->name);
        $this->filesystem->touch($this->file);
    }

    public function addContent(string $content)
    {
        $this->filesystem->appendToFile($this->file, $content);
    }

    public function replaceContent(string $content)
    {
        $this->filesystem->dumpFile($this->file, $content);
    }

    public function addClass(string $className)
    {
        $template = file_get_contents(__DIR__ . "/OverrideTemplate.php.tpl");
        $template = str_replace("{{className}}", $className, $template);

        $this->addContent($template);
    }

    public function addMethods($methods)
    {
        $template = file_get_contents($this->file);
        $template = str_replace("{{overrideMethods}}", implode("\n\n", $methods), $template);

        $this->replaceContent($template);
    }

    public function getCompiledMethod(string $methodName)
    {
        $template = file_get_contents(__DIR__ . "/OverrideTemplateMethod.php.tpl");
        $template = str_replace("{{methodName}}", $methodName, $template);

        return $template;
    }

    public function alreadyExists()
    {
        return $this->filesystem->exists($this->file);
    }

}