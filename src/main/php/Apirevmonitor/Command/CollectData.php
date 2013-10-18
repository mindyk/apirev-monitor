<?php

namespace Apirevmonitor\Command;

use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Apirevmonitor\Shell\Proxy;

class CollectData extends ConsoleCommand {

	/**
	 * @var Proxy $cli
	 */
	protected $cli;

	protected function configure(){
		$this->setName('collect-data')
			->setDescription('gets access.log from api01-04.portal and process it')
			->setHelp('The <info>collect-data</info> command copys access.log via scp from api01-04.portal and process the data for monitoring');
	}

	public function execute(InputInterface $input, OutputInterface $output) {
		$output->writeln('running ...');
	}

	public function setCli(Proxy $cli) {
		$this->cli = $cli;
	}

}