<?php

namespace Perelson;

/*
 * This is the router for this project
 *
 * It works by taking the path and converting that to match controllers and methods
 *
 */
class App
{

    // Holds all parts of the URL's path
    private static $segments = array();

    public function __construct()
    {
        session_start();

        $classOk = false;
        $url = parse_url($_SERVER["REQUEST_URI"]);
        $params = explode('/', trim($url["path"], '/'));
        $controllerRef = !empty($params[0]) ? $params[0] : 'root';
        $action = !empty($params[1]) ? $params[1] : 'index';

        if (empty($_SESSION['username']) &&
           (!($controllerRef == "root" && $action == "index") &&
            !($controllerRef == "operator" && $action == "login") &&
            !($controllerRef == "operator" && $action == "init"))) {
            Utils::redirect('/operator/login?back=' . urlencode($_SERVER["REQUEST_URI"]), 302);
        }

        $method = isset($_SERVER["REQUEST_METHOD"]) ? strtolower($_SERVER["REQUEST_METHOD"]) : 'get';
        // Does the controller exist?
        $controllerName = 'Perelson\\' . ucfirst($controllerRef) . 'Controller';

        if (class_exists($controllerName)) {
            $controller = new $controllerName();
            $classOk = true;
        } else {
            $controller = new RootController();
        }
        $action = $method . '_' . $action;

        // Does the Controller->Function exist?
        if (!method_exists($controller, $action)) {
            if ($classOk === false) {
                $controller = new RootController();
            }
            $action = $method . '_index';
        }
        if (count($params) > 0) {
            self::$segments = $params;
        }
        // Invoke the controller
        $controller->$action();
    }

    // Helper function in an easily remembered place to find the URL path segments
    public static function segment($index = 0)
    {
        if (array_key_exists($index, self::$segments)) {
            return self::$segments[$index];
        }
        return null;
    }
}

// Begin serving
$app = new App();
