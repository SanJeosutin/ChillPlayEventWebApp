<?php
$public = __DIR__ . '/views';
$function = __DIR__ . '/sources/functions';

$request = $_SERVER['REQUEST_URI'];

if (isset($_POST["authUser"])) {
    switch ($request) {
        case '/auth':
            require $function . '/auth.php';
            break;
    }
} else {
    switch ($request) {
        case '/':
            require $public . '/index.php';
            break;

        case '':
            require $public . '/index.php';
            break;

        case '/about':
            require $public . '/about.php';
            break;

        default:
            //http_response_code(404);
            require $public . '/errors/404.php';
            break;
    }
}
