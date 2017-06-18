<?php

namespace console\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PubSubCommand
 * @package console\command
 *
 * @property \e1\Application $app
 */
class PubSubCommand extends Command
{
    protected $app;

    protected function configure()
    {
        $this->setName('app:pubSub')->setDescription('Pub Sub redis command.')->setHelp('...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        set_time_limit(0);

        $this->app['redis']->subscribe($this->app['redis.channels'], function ($payload, $channel) {

        });
    }
}