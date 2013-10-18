#!/usr/bin/env php
<?php
/**
 * Entry point for cli
 */
require_once dirname(__FILE__) . '/../vendor/autoload.php';

use Apirevmonitor\Application;

$app = new Application();
$app->add(new Apirevmonitor\Command\CollectData());
$app->run();