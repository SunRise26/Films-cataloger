<?php

namespace Core;

use Exception;
use mysqli;

class Database {
    private string $host;
    private string $user;
    private string $pwd;
    private string $port;
    private string $dbname;

    public function __construct() {
        $this->host = $_ENV['DB_HOST'];
        $this->user = $_ENV['DB_USERNAME'];
        $this->pwd = $_ENV['DB_PASSWORD'];
        $this->port = $_ENV['DB_PORT'];
        $this->dbname = $_ENV['DB_DATABASE'];
    }

    public function connect(): mysqli {
        $mysqli = new mysqli(
            $this->host,
            $this->user,
            $this->pwd,
            $this->dbname,
            $this->port
        );
        if ($mysqli->connect_errno) {
            throw new Exception("Failed to connect to MySQL: " . $mysqli->connect_error);
        }
        return $mysqli;
    }
}
