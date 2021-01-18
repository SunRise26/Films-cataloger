<?php

namespace Models;

use Core\Model;

class FilmModel extends Model {
    protected static string $table_name = "films";
    protected static string $film_formats_table = "film_formats";
    protected static string $film_actors_list_table = "film_actors_list";
    
    const SEARCH_BY_ALL = 0;
    const SEARCH_BY_TITLE = 1;
    const SEARCH_BY_ACTOR = 2;
    public static array $search_by_options = [
        "all" => self::SEARCH_BY_ALL,
        "title" => self::SEARCH_BY_TITLE,
        "actor" => self::SEARCH_BY_ACTOR
    ];

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

    public function getFilmsData($searchParams) {
        $mysqli = $this->getConnection();
        $stmtParams = [
            'types' => '',
            'variables' => []
        ];

        $a = self::$table_name;
        $b = self::$film_formats_table;
        $query = "SELECT $a.id, $a.title, $a.release_year, $b.title AS format_title";
        $query .= ' FROM ' . $a;
        $query .= " INNER JOIN $b ON $a.format_id = $b.id";
        $this->applyFilmsSearchParams($query, $searchParams, $stmtParams);
        $query .= " ORDER BY title ASC";
 
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param($stmtParams['types'], ...$stmtParams['variables']);
        $stmt->execute();
        $query_result = $stmt->get_result();
        $stmt->close();
        $this->closeConnection();

        if ($query_result->num_rows > 0) {
            $filmsData = $query_result->fetch_all(MYSQLI_ASSOC) ?: [];
            $this->fetchFilmsActors($filmsData);
            return $filmsData;
        }
        return [];
    }

    protected function fetchFilmsActors(array &$filmsData) {
        $filmIds = array_map(function ($filmData) {
            return $filmData['id'];
        }, $filmsData);
        $actorsByFilms = $this->getActorsByFilms($filmIds);
        foreach ($actorsByFilms as $filmId => $actors) {
            $filmsDataKey = array_search($filmId, $filmIds);
            $filmsData[$filmsDataKey]['actors'] = $actors;
        }
    }

    protected function applyFilmsSearchParams(string &$query, array $searchParams, &$stmtParams) {
        switch ($searchParams['s_type']) {
            case self::SEARCH_BY_TITLE:
                $this->applyFilmsSearchByTitle($query, $searchParams, $stmtParams);
                break;
            case self::SEARCH_BY_ACTOR:
                $this->applyFilmsSearchByActors($query, $searchParams, $stmtParams);
                break;
            default:
                $this->applyFilmsSearchDefault($query, $searchParams, $stmtParams);
                break;
        }
    }

    protected function applyFilmsSearchByTitle(string &$query, array $searchParams, &$stmtParams) {
        if ($key = $searchParams['s_key']) {
            $a = self::$table_name;
            $query .= " WHERE $a.title LIKE ?";
            $stmtParams['types'] .= 's';
            $stmtParams['variables'][] = "%$key%";
        }
    }

    protected function applyFilmsSearchByActors(string &$query, array $searchParams, &$stmtParams) {
        if ($key = $searchParams['s_key']) {
            $a = self::$table_name;
            $b = self::$film_actors_list_table;
            $query .= " WHERE $a.id IN (SELECT DISTINCT film_id FROM $b WHERE full_name like ?)";
            $stmtParams['types'] .= 's';
            $stmtParams['variables'][] = "%$key%";
        }
    }

    protected function applyFilmsSearchDefault(string &$query, array $searchParams, &$stmtParams) {
        if ($key = $searchParams['s_key']) {
            $a = self::$table_name;
            $b = self::$film_actors_list_table;
            $query .= " WHERE $a.title LIKE ?";
            $query .= " OR $a.id IN (SELECT DISTINCT film_id FROM $b WHERE full_name like ?)";
            $stmtParams['types'] .= 'ss';
            $stmtParams['variables'][] = "%$key%";
            $stmtParams['variables'][] = "%$key%";
        }
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
