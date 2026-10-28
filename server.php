<?php

$publicPath = getcwd();

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// Same as Laravel's own "php artisan serve" router, but only real files are served as-is:
// public/admin is an asset folder, so /admin itself must reach the CMS dashboard.
if ($uri !== '/' && is_file($publicPath.$uri)) {
    return false;
}

require_once $publicPath.'/index.php';
