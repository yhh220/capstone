<?php

// Serve storage files directly, bypassing the Junction that php -S can't follow
if (preg_match('#^/storage/(.+)$#', $_SERVER['REQUEST_URI'], $m)) {
    $file = __DIR__.'/storage/app/public/'.$m[1];
    if (is_file($file)) {
        $mime = mime_content_type($file) ?: 'application/octet-stream';
        header('Content-Type: '.$mime);
        readfile($file);

        return true;
    }
    http_response_code(404);

    return true;
}

// All other requests go through Laravel
if (php_sapi_name() === 'cli-server') {
    $path = __DIR__.'/public'.parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($path)) {
        return false; // serve static files directly
    }
}

require_once __DIR__.'/public/index.php';
