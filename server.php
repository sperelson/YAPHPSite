<?php
// Borrowed from the Laravel project
// It's not a real part of the test.
// Use this to run local dev using
// the PHP Web server:
// php -S localhost:8080 server.php

$uri = urldecode(
	parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

if ($uri !== '/' && file_exists(__DIR__.'/'.$uri))
{
	return false;
}

require_once __DIR__.'/index.php';
