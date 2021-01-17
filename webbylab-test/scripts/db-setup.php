<?php
require_once __DIR__ . "/../src/bootstrap.php";

use Core\Database;
use Models\FilmModel;

$filmsTable = FilmModel::getTable();
$filmFormatsTable = FilmModel::getFilmFormatsTable();
$filmActorsListTable = FilmModel::getFilmActorsListTable();

$db_adapter = new Database();
$mysqli = $db_adapter->connect();

// CREATE TABLES

// // create film formats table
$query = 'CREATE TABLE IF NOT EXISTS ' . $filmFormatsTable . ' (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(30) NOT NULL UNIQUE
) ENGINE = INNODB';
$query_result = $mysqli->query($query);

// // create films table
$query = 'CREATE TABLE IF NOT EXISTS ' . $filmsTable . ' (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(50) NOT NULL,
    release_year SMALLINT,
    format_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (format_id) REFERENCES ' . $filmFormatsTable . '(id)
) ENGINE = INNODB';
$query_result = $mysqli->query($query);

// // create film actors list table
$query = 'CREATE TABLE IF NOT EXISTS ' . $filmActorsListTable . ' (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    film_id INT UNSIGNED NOT NULL,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    FOREIGN KEY (film_id) REFERENCES ' . $filmsTable . '(id)
) ENGINE = INNODB';
$query_result = $mysqli->query($query);

// FILL TABLES

// // setup film formats
$film_formats = [
    'VHS',
    'DVD',
    'Blu-Ray'
];

$query = '';
foreach ($film_formats as $format) {

    // insert new value if not exists

    $query .= 'INSERT INTO ' . $filmFormatsTable . ' (title)
    SELECT * FROM (SELECT \'' . $format .  '\' AS title) AS tmp
    WHERE NOT EXISTS (
        SELECT title FROM ' . $filmFormatsTable . ' WHERE title=\'' . $format .  '\'
    ) LIMIT 1;';
}
$query_result = $mysqli->multi_query($query);

$mysqli->close();
