<?php
/**
 * Created by JetBrains PhpStorm.
 * User: michael.indyk
 * Date: 22.10.13
 * Time: 15:00
 * To change this template use File | Settings | File Templates.
 */

namespace Apirevmonitor\Storage;


class AccessLog {

	private $db;

	public function __construct(\SQLite3 $db) {
		$this->db = $db;
	}

	public function storeData(array $data) {
		$preValues = "'%s', %d, '%s'";
		$values = '';
		/*
		$this->db->query("
			INSERT INTO apirev (game_id, rev, date_collected) VALUES
				($values);
		");
		*/
	}

}