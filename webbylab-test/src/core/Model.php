<?php

namespace Core;

class Model {
    private Database $db_adapter;
    protected static string $table_name;

    public function __construct() {
        $this->db_adapter = new Database();
    }

    public static function getTable() {
        return get_called_class()::$table_name;
    }
}
