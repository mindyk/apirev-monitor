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

class ProcessData extends ConsoleCommand {

	const MANY_LOGS = 4;
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
		$this->setName('process-data')
			->setDescription('process collected access logs')
			->setHelp('The <info>process-data</info> command process the collected data logs and displays a convinient data summery')
			->setDefinition(array(
				new InputArgument('project', InputArgument::OPTIONAL, 'The projects\'s checkouts that should be listet.'),
				new InputOption('prod', null, InputOption::VALUE_NONE, 'Lists all production checkouts.'),
			));
	}

	protected function initialize(InputInterface $input, OutputInterface $output) {
		$this->dateTime = new \DateTime();
	}

	public function execute(InputInterface $input, OutputInterface $output) {
		$output->writeln('running command ...');
		$output->writeln('searching for logs in tmp');
		$logs = $this->collectAccessLogsFromTmp();
		if (empty($logs)) {
			throw new \RuntimeException("no logs found! try command collect-data");
		}
		$countLogs = count($logs);

		$output->writeln("logs found:");
		foreach ($logs as $path) {
			$output->writeln('<info>'. $path ."</info>");
		}
		if ($countLogs > self::MANY_LOGS) {
			$dialog = $this->getHelperSet()->get('dialog');
			if (!$dialog->askConfirmation(
				$output,
				'<question>many logs! could take a while to process. Continue with this action [y|n]?</question>',
				false
			)) {
				return;
			}
		}
		$output->writeln("processing ... (this will take a minute or two ...)");
		$data = $this->processAccessLogs($logs);

		$output->writeln('... finish processing');
		$output->writeln('data summery:');
		$output->writeln(print_r($data['sum'], true));
		$output->writeln('saving data ...');
		$output->writeln('... data saved');
		$output->writeln('... finished command');
	}

	private function collectAccessLogsFromTmp() {
		$logs = array();
		$dir = new \DirectoryIterator('tmp');

		/**
		 * @var \SplFileInfo $fileinfo
		 */
		foreach ($dir as $fileinfo) {
			if (!$fileinfo->isDot()) {
				$logs[] = $fileinfo->getRealPath();
			}
		}

		return $logs;
	}

	private function processAccessLogs(array $logs) {
		foreach ($logs  as $path) {
			$this->processor->addLog($path);
		}
		$this->processor->process();

		return $this->processor->getData();;
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