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
			//echo $e->getMessage();
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

	public function getData() {
		return $this->data;
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
		if (count($simpleSegmentation) != 8) {
			throw new \RuntimeException("invalid segmentation count");
		}

		$this->date = \DateTime::createFromFormat("[d/M/Y:H:i:s", $simpleSegmentation[0]);
		$this->revision = $simpleSegmentation[2];
		$this->http_response_code = $simpleSegmentation[3];
		$this->request = $simpleSegmentation[5];
		$this->remote_addr = $simpleSegmentation[7];
	}

	public function getDate() {
		return $this->date;
	}

	public function getRevision() {
		if ($this->revision == 'r-') {
			return 765; // revision bevore api-rev header
		}

		return (int) str_replace('r', '', $this->revision);
	}

	public function getRequest() {
		return $this->request;
	}

	public function getRemoteIP() {
		return $this->remote_addr;
	}

	public function getResource() {
		$request = $this->getRequest();
		$simpleSegmentation = explode('/', $request);
		if (count($simpleSegmentation) != 6) {
			throw new \RuntimeException("invalid segmentation count");
		}

		return $simpleSegmentation[3];
	}
}