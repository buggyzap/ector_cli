<?php

namespace Ector\Cli\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Github\Client;
use Github\AuthMethod;

/**
 * This command will clone and extract modules from a github repository
 * 
 * The github repository must have at least 1 .zip file and a tagged version, the command will ask for github_token, user/repo and tag to clone
 */
class CloneModulesCommand extends Command
{
    protected static $defaultName = 'module:clone_by_repo';
    const TEST_MODE = false;
    const TEST_CREDENTIALS = [
        "xxx",
        "user",
        "repo",
        "tag"
    ];

    protected function configure()
    {
        $this
            ->setDescription('Clone module starting from user/repo and tag')
            ->setHelp('This command clone the modules of the repository and extract them in the modules folder');
    }

    /**
     * Delete the temporary files
     */
    protected function cleanUp($repositoryOwner)
    {
        @unlink(__DIR__ . "/temp.zip");
        $files = scandir(__DIR__);
        $dir = null;
        foreach ($files as $file) {
            if (strpos($file, $repositoryOwner) !== false) {
                $dir = $file;
                break;
            }
        }

        @unlink(__DIR__ . "/" . $dir . "/README.md");
        @rmdir(__DIR__ . "/" . $dir);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $helper = $this->getHelper('question');

        if (self::TEST_MODE) {
            list($token, $repositoryOwner, $repositoryName, $tag) = self::TEST_CREDENTIALS;
        } else {
            $token = $helper->ask($input, $output, new Question('Enter the github Token to access your customer repo: '));
            $repo = $helper->ask($input, $output, new Question('Enter the github repo name in a format user/repo: '));
            $repo = explode("/", $repo);
            $repositoryOwner = $repo[0];
            $repositoryName = $repo[1];
            $tag = $helper->ask($input, $output, new Question('Enter the tag to clone: '));
        }

        $github = new Client();
        $github->authenticate($token, null, AuthMethod::ACCESS_TOKEN);

        try {
            $content = $github->api('repo')->contents()->archive($repositoryOwner, $repositoryName, 'zipball', $tag);

            if (empty($content)) {
                throw new \Exception('Error while downloading the ZIP file.');
            }

            $output->writeln('<info>Main ZIP file downloaded successfully!</info>');

            $write = file_put_contents(__DIR__ . "/temp.zip", $content);

            if (empty($write)) {
                throw new \Exception('Error while writing the ZIP file.');
            }

            $output->writeln('<info>Main ZIP file written successfully!</info>');

            // extract to modules
            $zip = new \ZipArchive;
            $res = $zip->open(__DIR__ . "/temp.zip");
            if ($res === TRUE) {
                $zip->extractTo(__DIR__);
                $zip->close();

                $files = scandir(__DIR__);
                $dir = null;
                foreach ($files as $file) {
                    if (strpos($file, $repositoryName) !== false) {
                        $dir = $file;
                        break;
                    }
                }

                if (empty($dir)) {
                    throw new \Exception('Error while extracting the ZIP file.');
                }

                $output->writeln('<info>ZIP file extracted successfully!</info>');

                // copy all content to modules
                $files = scandir(__DIR__ . "/" . $dir);
                foreach ($files as $file) {
                    if ($file == "." || $file == ".." || $file === "README.md") continue;

                    $output->writeln('<info>Copying ' . $file . ' to modules folder</info>');

                    rename(__DIR__ . "/" . $dir . "/" . $file, _PS_MODULE_DIR_ . $file);

                    // extract to modules
                    $zip = new \ZipArchive;
                    $res = $zip->open(_PS_MODULE_DIR_ . $file);
                    if ($res === TRUE) {
                        $zip->extractTo(_PS_MODULE_DIR_);
                        $zip->close();
                        @unlink(_PS_MODULE_DIR_ . $file);
                    } else {
                        throw new \Exception('Error while extracting the ZIP file.');
                    }
                }
            } else {
                throw new \Exception('Error while extracting the ZIP file.');
            }

            $output->writeln('<info>Modules cloned successfully!</info>');

            $this->cleanUp($repositoryOwner);

            $output->writeln('<info>Temporary files deleted successfully!</info>');
        } catch (\Exception $e) {
            die('Errore: ' . $e->getMessage());
        }


        return 0;
    }
}
