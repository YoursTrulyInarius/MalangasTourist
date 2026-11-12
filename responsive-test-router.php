<?php
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$publicFile = __DIR__ . '/public' . str_replace('/', DIRECTORY_SEPARATOR, $requestPath);

if ($requestPath !== '/' && is_file($publicFile)) {
    return false;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';
$_GET['url'] = $requestPath;
require __DIR__ . '/public/index.php';
