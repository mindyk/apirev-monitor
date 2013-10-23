#!/usr/bin/env php
<?php
/**
 * Entry point for cli
 */
$currentDir = dirname(__FILE__);
require_once $currentDir . '/../vendor/autoload.php';

use Apirevmonitor\Application;

if (!is_readable('/../etc/config.json')) {
	exit('copy etc/config.json.dist and rename it to config.json');
}
$rawJsonConfig = file_get_contents($currentDir . '/../etc/config');
$config = json_decode($rawJsonConfig, true);

$db = new SQLite3($config['db_path']);
initSqliteDb($db);

$app = new Application();

$collectDataCmd = new Apirevmonitor\Command\CollectData();
$collectDataCmd->setConfig($config);
$collectDataCmd->setCli(new Apirevmonitor\Shell\Proxy());
$app->add($collectDataCmd);

$processDataCmd = new \Apirevmonitor\Command\ProcessData();
$processDataCmd->setConfig($config);
$processDataCmd->setCli(new Apirevmonitor\Shell\Proxy());
$processDataCmd->setProcessor(new Apirevmonitor\Processor\AccessLog(new \mindyk\FileIterator\Factory(), new \Apirevmonitor\Map\Game()));
$processDataCmd->setStorage(new Apirevmonitor\Storage\AccessLog($db));
$app->add($processDataCmd);

$app->run();

function initSqliteDb(SQLite3 $db) {
	$db->exec("
		CREATE TABLE IF NOT EXISTS apirev(
			id INTEGER PRIMARY KEY AUTOINCREMENT,
			game_id TEXT,
			revision INTEGER,
			timestamp_collected INTEGER
		);
	");
}