<?php

namespace Perelson;

class Session
{
    public static function create($name, $username)
    {
        $_SESSION['name']     = $name;
        $_SESSION['username'] = $username;
    }

    public static function flashSuccess($value)
    {
        $_SESSION['success'] = $value;
    }

    public static function flashFail($value)
    {
        $_SESSION['fail'] = $value;
    }

    public static function remember($value)
    {
        $_SESSION['remember'] = $value;
    }

    public static function errors($key, $value)
    {
        if (empty($_SESSION['errors'])) {
            $_SESSION['errors'] = array();
        }
        $_SESSION['errors'][$key] = $value;
    }

    public static function flashClear()
    {
        $_SESSION['fail'] = null;
        $_SESSION['success'] = null;
        $_SESSION['remember'] = null;
        $_SESSION['errors'] = null;
    }

    public static function destroy()
    {
        session_destroy();
    }
}
