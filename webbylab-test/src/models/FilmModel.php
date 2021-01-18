<?php

namespace Models;

use Core\Model;

class FilmModel extends Model {
    protected static string $table_name = "films";
    protected static string $film_formats_table = "film_formats";
    protected static string $film_actors_list_table = "film_actors_list";

    private $filmFormats;

    public function getFilmFormats() {
        if (!$this->filmFormats) {
            $mysqli = $this->getConnection();
            $query = 'SELECT * FROM ' . self::$film_formats_table;
            $query_result = $mysqli->query($query);
            $this->closeConnection();

            if ($query_result->num_rows > 0) {
                $this->filmFormats = $query_result->fetch_all(MYSQLI_ASSOC);
            }
        }
        return $this->filmFormats;
    }

    public function getFilmsData() {
        $mysqli = $this->getConnection();
        $a = self::$table_name;
        $b = self::$film_formats_table;
        $query = "SELECT $a.id, $a.title, $a.release_year, $b.title AS format_title";
        $query .= ' FROM ' . $a;
        $query .= " INNER JOIN $b ON $a.format_id = $b.id";
        $query_result = $mysqli->query($query);
        $this->closeConnection();

        if ($query_result->num_rows > 0) {
            $filmsData = $query_result->fetch_all(MYSQLI_ASSOC) ?: [];
            $filmIds = array_map(function ($filmData) {
                return $filmData['id'];
            }, $filmsData);
            $actorsByFilms = $this->getActorsByFilms($filmIds);
            foreach ($actorsByFilms as $filmId => $actors) {
                $filmsDataKey = array_search($filmId, $filmIds);
                $filmsData[$filmsDataKey]['actors'] = $actors;
            }
            return $filmsData;
        }
        return [];
    }

    public function addFilm(array $data) {
        $mysqli = $this->getConnection();
        
        $query = 'INSERT INTO ' . self::$table_name;
        $query .= ' (title, release_year, format_id)';
        $query .= ' VALUES (?, ?, ?)';

        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("sii", $data['title'], $data['year'], $data['format_id']);
        if ($query_result = $stmt->execute()) {
            $filmId = $mysqli->insert_id;
            $this->addActors($filmId, $data['actors']);
        }
        $stmt->close();

        $this->closeConnection();
        return $query_result;
    }

    public function deleteFilm($filmId) {
        $mysqli = $this->getConnection();

        $this->deleteActors($filmId);

        $query = 'DELETE FROM ' . self::$table_name;
        $query .= " WHERE id=?";

        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $filmId);
        $query_result = $stmt->execute();
        $stmt->close();

        $this->closeConnection();
        return $query_result;
    }

    protected function addActors(int $filmId, array $actors) {
        $mysqli = $this->getConnection();
        $fullname = "";

        $query = 'INSERT INTO ' . self::$film_actors_list_table;
        $query .= ' (film_id, full_name) VALUES (?, ?)';

        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("is", $filmId, $fullname);
        foreach ($actors as $actor) {
            $fullname = $actor;
            $stmt->execute();
        }
        $stmt->close();
    }

    protected function getActorsByFilms(?array $filmIds): array {
        $mysqli = $this->getConnection();

        $formattedIds = implode(', ', $filmIds);
        $query = 'SELECT *';
        $query .= ' FROM ' . self::$film_actors_list_table;
        $query .= " WHERE film_id IN ($formattedIds)";

        $query_result = $mysqli->query($query);
        $actorsData = $query_result->fetch_all(MYSQLI_ASSOC);

        $actorsByFilms = [];
        foreach ($actorsData as $actor) {
            $actorsByFilms[$actor['film_id']][] = $actor['full_name'];
        }
        return $actorsByFilms;
    }

    protected function deleteActors(int $filmId) {
        $mysqli = $this->getConnection();

        $query = 'DELETE FROM ' . self::$film_actors_list_table;
        $query .= " WHERE film_id=?";

        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $filmId);
        $query_result = $stmt->execute();
        $stmt->close();

        return $query_result;
    }

    public static function getFilmFormatsTable() {
        return self::$film_formats_table;
    }

    public static function getFilmActorsListTable() {
        return self::$film_actors_list_table;
    }
}
