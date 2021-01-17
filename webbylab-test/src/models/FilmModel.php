<?php

namespace Models;

use Core\Model;

class FilmModel extends Model {
    protected static string $table_name = "films";
    protected static string $film_formats_table = "film_formats";
    protected static string $film_actors_list_table = "film_actors_list";

    public function __construct() {}

    public static function getFilmFormatsTable() {
        return self::$film_formats_table;
    }

    public static function getFilmActorsListTable() {
        return self::$film_actors_list_table;
    }
}
