<?php
// config/config.php

date_default_timezone_set('Asia/Manila');

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
$databaseName = getenv('MALANGAS_DB_NAME');
define('DB_NAME', $databaseName !== false && $databaseName !== '' ? $databaseName : 'malangas_tourism');

// Dynamic Base URL detection
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Auto-detect project base folder from script path
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $basePath = preg_replace('#/public$#', '', $scriptDir);
    if ($basePath === '/' || $basePath === '\\') {
        $basePath = '';
    }
    
    $autoUrl = rtrim($protocol . $host . $basePath, '/');
    define('BASE_URL', !empty($autoUrl) ? $autoUrl : 'http://localhost/MalangasTourist');
}

// Base map center (Malangas, Zamboanga Sibugay)
define('MAP_CENTER_LAT', 7.625);
define('MAP_CENTER_LNG', 123.025);
define('MAP_DEFAULT_ZOOM', 13);

// File uploads
define('UPLOAD_DIR', __DIR__ . '/../public/assets/uploads/');

// SMTP Email Configuration (Gmail)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'ricardoesioiii@gmail.com');   // Gmail address
define('SMTP_PASS', 'nfllhxsgmdixsllp');            // Gmail App Password
define('SMTP_PORT', 587);
