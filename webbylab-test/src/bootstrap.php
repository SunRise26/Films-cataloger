<?php

// load dependencies
require_once __DIR__ . "/../vendor/autoload.php";

// load project environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

// load routes
require_once __DIR__ . "/routes.php";
