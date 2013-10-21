#!/usr/bin/env php
<?php
/**
 * Entry point for cli
 */
$currentDir = dirname(__FILE__);
require_once $currentDir . '/../vendor/autoload.php';

use Apirevmonitor\Application;

$rawJsonConfig = file_get_contents($currentDir . '/../etc/config.json.dist');
$config = json_decode($rawJsonConfig, true);
$app = new Application();

$collectDataCmd = new Apirevmonitor\Command\CollectData();
$collectDataCmd->setConfig($config);
$collectDataCmd->setCli(new Apirevmonitor\Shell\Proxy());
$collectDataCmd->setProcessor(new Apirevmonitor\Processor\AccessLog(new \Apirevmonitor\FileIterator\Factory(), new \Apirevmonitor\Map\Game()));

$app->add($collectDataCmd);

$app->run();