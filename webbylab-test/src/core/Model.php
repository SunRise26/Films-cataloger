<?php

namespace Core;

use mysqli;

class Model {
    private $db_adapter;
    private $connection;
    protected static string $table_name;

    public function __construct() {
        $this->db_adapter = new Database();
    }

    protected function getConnection(): mysqli {
        if (empty($this->connection)) {
            $this->connection = $this->db_adapter->connect();
        }
        return $this->connection;
    }

    protected function closeConnection() {
        $this->connection->close();
        $this->connection = null;
    }

    public static function getTable() {
        return get_called_class()::$table_name;
    }
}
