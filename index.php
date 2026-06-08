<?php
// index.php — router principal

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$uri = preg_replace('#^api/?#', '', $uri);

match (true) {
    str_starts_with($uri, 'testimonios') => require __DIR__ . '/testimonios/index.php',
    str_starts_with($uri, 'pdf')         => require __DIR__ . '/pdf/index.php',

    $uri === '' || $uri === 'health' => (function () {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok', 'version' => '1.0']);
    })(),

    default => (function () {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Not found']);
    })(),
};