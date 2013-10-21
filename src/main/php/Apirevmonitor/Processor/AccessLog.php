<?php
/**
 * Created by JetBrains PhpStorm.
 * User: michael.indyk
 * Date: 21.10.13
 * Time: 14:03
 * To change this template use File | Settings | File Templates.
 */

namespace Apirevmonitor\Processor;


use Apirevmonitor\FileIterator\Factory;
use Apirevmonitor\Map\Game;
use Apirevmonitor\Map\Map;

class AccessLog {

	/**
	 * @var array
	 */
	private $logs = array();

	/**
	 * @var \Apirevmonitor\FileIterator\Factory
	 */
	private $fileIteratorFactory;

	/**
	 * @var array
	 */
	private $data = array('resources' => array(), 'revisions' => array(), 'sum' => array('request_count' => 0));

	/**
	 * @var \Apirevmonitor\Map\Game
	 */
	private $map;

	public function __construct(Factory $factory, Game $resourceToGame) {
		$this->fileIteratorFactory = $factory;
		$this->map = $resourceToGame;
	}

	public function addLog($path) {
		$this->logs[] = $path;
	}

	public function process() {

		foreach ($this->logs as $path) {
			$fileIterator = $this->fileIteratorFactory->getFileIterator($path);

			foreach ($fileIterator as $line) {
				$this->processAccessLine($line);
			}
		}
	}

	private function processAccessLine($rawLine) {
		try {
			$line = new AccessLogLine($rawLine);
		} catch (\Exception $e) {
			return;
		}

		$this->data['sum']['request_count'] += 1;

		$resourceId = $line->getResource();
		$revision = $line->getRevision();

		if (!isset($this->data['resources'][$resourceId])) {
			$this->data['resources'][$resourceId] = array();
		}
		$this->data['resources'][$resourceId]['api-rev'] = $line->getRevision();

		if (!isset($this->data['revisions'][$revision])) {
			$this->data['revisions'][$revision] = array();
		}
		$this->data['revisions'][$revision]['resource_id'] = $resourceId;
	}
}

class AccessLogLine {

	private $date;
	private $revision;
	private $http_response_code;
	private $request;
	private $remote_addr;

	public function __construct($raw) {
		$simpleSegmentation = explode(' ', $raw);

	}

	public function getDate() {
		return new \DateTime();
	}

	public function getRevision() {
		return 777;
	}

	public function getRequest() {
		return 'PUT /api/games/5/players/698221012 HTTP/1.1';
	}

	public function getRemoteIP() {
		return '127.0.0.0';
	}

	public function getResource() {
		return 134;
	}
}