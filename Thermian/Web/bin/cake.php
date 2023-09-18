#!/usr/bin/php -q
<?php
declare(strict_types=1);

require dirname(__DIR__) . '/config/requirements.php';
require dirname(__DIR__, 3) . '/vendor/autoload.php';

use Web\Application;
use Cake\Console\CommandRunner;

$runner = new CommandRunner(new Application(dirname(__DIR__) . '/config'), 'cake');
exit($runner->run($argv));
