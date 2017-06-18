<?php

namespace console;

use console\command\exampleCommand;
use console\command\PubSubCommand;
use console\command\swaggerCommand;
use Symfony\Component\Console\Application;
use console\command\cron\agendaStateCommand;
use Symfony\Component\Console\Input\InputOption;

/**
 * @REQUIRE:
 * composer require symfony/console
 *
 * @USAGE: /usr/bin/php bin/console app:example-command
 *
 * cron example:
 * command: crontab -e
 * 0 17 * * * /usr/bin/php /var/www/pharma.skrymed.com/bin/console cron:achievement
 */


/** @var \e1\Application $app */
$app = require dirname(__DIR__) . '/web/app.php';
$app->boot();

$console = new Application('SkryMed Application with console', 'n/a');

# description of application
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'Silex Application'));

$PubSubCommand = new PubSubCommand();
$PubSubCommand->app = $app;
$console->add($PubSubCommand);

$exampleCommand = new exampleCommand();
$exampleCommand->app = $app;
$console->add($exampleCommand);

return $console;

