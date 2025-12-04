#!/usr/bin/env php
<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use TQ\Shamir\Console\RecoverCommand;
use TQ\Shamir\Console\ShareCommand;

$application = new Application('Shamir\'s Shared Secret CLI', '2.0.0');

if (method_exists($application, 'addCommand')) {
    $application->addCommand(new RecoverCommand());
    $application->addCommand(new ShareCommand());
} else {
    $application->add(new RecoverCommand());
    $application->add(new ShareCommand());
}

$application->run();

