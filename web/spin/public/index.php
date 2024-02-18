<?php



require_once __DIR__ . '/../config/bootstrap.php';
/** @var Foundation\Routing\Router $router */
$router = require_once __DIR__ . '/../config/routes.php';



$response = $router->handle($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);


header('Content-Type: application/json');
http_response_code($response->getStatusCode());
echo $response->getBody();



