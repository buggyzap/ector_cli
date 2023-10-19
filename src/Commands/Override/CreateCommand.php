<?php

namespace Ector\Cli\Commands\Override;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputArgument;
use Ector\Cli\Classes\Override\Override;

class CreateCommand extends Command
{
    protected static $defaultName = 'override:create';

    protected function configure()
    {
        $this
            ->setDescription('Create a new override file from our ready-to-use templates')
            ->setHelp('This command allows you to create a new override file from our ready-to-use templates')
            ->addArgument('module_name', InputArgument::REQUIRED, 'The name of the module you want to create an override, type the name of the folder inside modules prestashop directory, e.g. ps_facetedsearch');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $helper = $this->getHelper('question');
        $moduleName = $input->getArgument('module_name');
        $override = new Override($moduleName);

        if ($override->alreadyExists()) {
            $output->writeln('<error>Override already exists, delete it from ector_overrides if you want to create a new one.</error>');
            return 0;
        }

        $override->draftFile();

        $className = new Question('What is the name of the class you want to override? (e.g. ' . ucfirst($moduleName) . ') ', ucfirst($moduleName));

        $className = $helper->ask($input, $output, $className);
        $override->addClass($className);

        $output->writeln('<info>Override base created successfully</info>');

        $question = new ConfirmationQuestion('Did you want to add an override method? y/n ', false);

        $methods = [];

        while ($helper->ask($input, $output, $question)) {
            $methodName = new Question('What is the name of the method you want to override? (e.g. hookHeader) ', 'hookHeader');
            $methodName = $helper->ask($input, $output, $methodName);
            $methods[] = $override->getCompiledMethod($methodName);
            $output->writeln('<info>Method added successfully</info>');
        }

        $override->addMethods($methods);
        $output->writeln('<info>Override created at ' . $override->getFile() . '!</info>');

        return 0;
    }
}
