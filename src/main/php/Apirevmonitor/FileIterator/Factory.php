<?php
/**
 * Created by JetBrains PhpStorm.
 * User: michael.indyk
 * Date: 21.10.13
 * Time: 14:45
 * To change this template use File | Settings | File Templates.
 */

namespace Apirevmonitor\FileIterator;


use mindyk\FileIterator\FileIterator;

class Factory {

	public function getFileIterator($path) {
		if (!is_readable($path)) {
			throw new \RuntimeException("invalid path, file not readable");
		}
		$handle = fopen($path, 'r');
		if (false == $handle) {
			throw new \RuntimeException("invalid path, failed to fopen");
		}
		return new FileIterator($handle);
	}
}