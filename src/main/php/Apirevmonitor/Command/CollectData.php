<?php

namespace Apirevmonitor\Command;

use Apirevmonitor\Processor\AccessLog;
use Apirevmonitor\Shell\Command\Scp;
use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Apirevmonitor\Shell\Proxy;

class CollectData extends ConsoleCommand {

	const localPathFormat = 'tmp/api0%d.portal.access_%s.log';
	/**
	 * @var Proxy $cli
	 */
	protected $cli;

	/**
	 * @var \DateTime $dateTime
	 */
	protected $dateTime;

	/**
	 * @var array
	 */
	protected $config;

	/**
	 * @var AccessLog
	 */
	protected $processor;

	protected function configure(){
		$this->setName('collect-data')
			->setDescription('gets access.log from api01-04.portal and process it')
			->setHelp('The <info>collect-data</info> command copys access.log via scp from api01-04.portal and process the data for monitoring');
	}

	protected function initialize(InputInterface $input, OutputInterface $output) {
		$this->dateTime = new \DateTime();
	}

	public function execute(InputInterface $input, OutputInterface $output) {
		$output->writeln('running ...');
		$this->cli->setOutput($output);
		$output->writeln('collecting logs');
		$this->collectAccessLogs();
		$output->writeln('processing logs');
		$this->processAccessLogs();
		$output->writeln('saving data');
		$this->saveData();
		$output->writeln('outputting data');
		$this->outputData();
		$output->writeln('... finish');
	}

	private function collectAccessLogs() {
		$timestamp = $this->dateTime->getTimestamp();
		$remotePath = 'www/portal-api/logs/access.log';
		$remoteFormat = $this->config['api_server_url'];
		for ($i=1;$i<=4;$i++) {
			$currentRemotePath = sprintf($remoteFormat, $i, $remotePath);
			$currentLocalPath = sprintf(self::localPathFormat, $i, $timestamp);

			$this->cli->run(new Scp($currentRemotePath, $currentLocalPath));
		}
	}

	private function processAccessLogs() {
		$timestamp = $this->dateTime->getTimestamp();
		for ($i=1;$i<=4;$i++) {
			$currentLocalPath = sprintf(self::localPathFormat, $i, $timestamp);
			$this->processor->addLog($currentLocalPath);
		}

		$this->processor->process();

	}

	private function saveData() {

	}

	private function outputData() {

	}

	/**
	 * ! has to be set before others (e.g. setCli)
	 *
	 * @param array $config
	 */
	public function setConfig(array $config) {
		$this->config = $config;
	}

	public function setCli(Proxy $cli) {
		$this->cli = $cli;
		$this->cli->setDryRun($this->config['dry-run']);
	}

	public function setProcessor(AccessLog $processor) {
		$this->processor = $processor;
	}

}