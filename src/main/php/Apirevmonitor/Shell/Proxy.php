<?php

namespace Apirevmonitor\Shell;

use Symfony\Component\Console\Output\OutputInterface;

class Proxy {

	/**
	 * @var OutputInterface
	 */
	private $output;

	/**
	 * @var bool
	 */
	private $dryRun = false;

	public function setOutput(OutputInterface $output) {
		$this->output = $output;
	}

	/**
	 * If called with true all command will be send to output instead to shell.
	 *
	 * @param bool $dryRun
	 */
	public function setDryRun($dryRun=true) {
		$this->dryRun = $dryRun;
	}

	/**
	 * @param Command\Command $cmd
	 * @param bool $returnOutput
	 * @return null|string
	 */
	public function run(Command\Command $cmd, $returnOutput=false) {
		if (empty($cmd)) {
			return null;
		}

		if ($this->dryRun) {
			$this->output->writeln(sprintf("\t[%s]", $cmd));
			return null;
		}

		$ret = '';
		if ($returnOutput) {
			ob_start();
		}

		$returnValue = -1;
		passthru($cmd, $returnValue);

		if ($returnOutput) {
			$ret = ob_get_contents();
			ob_end_clean();
		}

		if ($returnValue < 0) {
			$this->output->write(sprintf("\033[1;31mError with exit code '%s'\n\033[0;31m%s\n%s\033[0;0m\n\n", $returnValue, $cmd, $ret));
			exit($returnValue);
		}

		if ($returnOutput) {
			return $ret;
		}

		return null;
	}
}