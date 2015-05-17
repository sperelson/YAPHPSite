<?php

namespace Perelson;

/*
 * The view helper
 *
 */

class View
{
	public static function show()
	{
		$viewName = "index";
		$vars = array();

		if (!empty($_SESSION['errors'])) {
			$errors = $_SESSION['errors'];
		}
		if (!empty($_SESSION['remember'])) {
			$v = $_SESSION['remember'];

			if (is_array($v) && count($v) > 0) {
				foreach ($v as $key => $val) {
					${$key} = $val;
				}
			}
		}
		if (func_num_args() > 0) {
			$viewName = func_get_arg(0);

			if (func_num_args() == 2) {
				$vars = func_get_arg(1);
			}
		}
		if (is_array($vars) && count($vars) > 0) {
			foreach ($vars as $key => $val) {
				${$key} = $val;
			}
		}
		$ds = DIRECTORY_SEPARATOR;
		include __DIR__ . $ds . '..' . $ds . 'views' . $ds . $viewName . '.html';

		Session::flashClear();
	}
}
