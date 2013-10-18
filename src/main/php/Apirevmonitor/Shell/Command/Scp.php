<?php

namespace Apirevmonitor\Shell\Command;


class Scp extends Command {

	const CMD_FORMAT = 'scp %s %s';

	/**
	 * @var string $from
	 */
	protected $from;

	/**
	 * @var string $to
	 */
	protected $to;

	public function __construct($from = null, $to = null) {
		$this->from = $from;
		$this->to = $to;
	}

	public function setFrom($from) {
		$this->from = $from;
		return $this;
	}

	public function setTo($to) {
		$this->to = $to;
		return $this;
	}


	public function __toString() {
		return sprintf(self::CMD_FORMAT, $this->from, $this->to);
	}
}