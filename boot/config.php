<?php

namespace Perelson;

class Config {

	private static $config = array(
	  'dbserver' => '127.0.0.1'
	, 'dbport' => '3306'
	, 'dbuser' => 'root'
	, 'dbpwd' => 'root'
	, 'dbname' => 'customercontrol'
	);

	public static function get($key)
	{
		if (array_key_exists($key, self::$config)) {
			return self::$config[$key];
		}
		return null;
	}
}
