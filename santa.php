#!/usr/bin/env php

<?php

require_once 'vendor/autoload.php';

use Inouire\SecretFanta\Command\MainCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new MainCommand());
$application->run();
