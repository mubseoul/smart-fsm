<?php

// Product Name : Smart FSM SaaS
// Version      : 1.4.1

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

$appUri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

if ($appUri !== '/' && file_exists(__DIR__.'/public'.$appUri)) {
    return false;
}

require_once __DIR__.'/public/index.php';
