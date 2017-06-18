<?php

namespace console\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class exampleCommand
 * @package console\command
 */
class exampleCommand extends Command
{
    public $app;

    protected function configure()
    {
        $this->setName('app:example-command') # the name of the command (the part after "bin/console")
        ->setDescription('Creates example command.') # the short description shown while running "php bin/console list"
            ->setDescription('Creates example command.') # the full command description shown when running the command with the "--help" option
            ->setHelp("This command allows you to create example command...");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        # outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln(['Example command', '============']);

        # outputs a message followed by a "\n"
        $output->writeln('Whoa!');

        # outputs a message without adding a "\n" at the end of the line
        $output->write('You are about to ');
        $output->write('example command.');
    }
}