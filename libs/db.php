<?php

namespace Perelson;

class DB
{
	private static $conn;

	public function conn()
	{
		$server = Config::get('dbserver');
		$user = Config::get('dbuser');
		$pwd = Config::get('dbpwd');
		$db = Config::get('dbname');

		if (!isset(self::$conn)) {
			self::$conn = new \mysqli($server, $user, $pwd, $db);

			if (mysqli_connect_errno()) {
				echo '';
				die();
			}
		}
		return self::$conn;
	}
}
