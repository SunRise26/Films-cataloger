<?php

try {
    require_once __DIR__ . "/../src/bootstrap.php";
    $router = new Core\Router();
    $router->handleRequest();
} catch(\Throwable $e) {
    if ($_ENV["APP_DEBUG"]) {
        echo $e;
    }
    throw $e;
}
