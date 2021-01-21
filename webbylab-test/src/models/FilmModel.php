<?php

namespace Models;

use Core\Model;
use Exception;
use Helpers\FilmDataHelper;
use Helpers\FilmSearchHelper;

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

    const SORT_ORDER_ASC = 0;
    const SORT_ORDER_DESC = 1;
    public static array $search_sort_order_options = [
        "title A-Z" => self::SORT_ORDER_ASC,
        "title Z-A" => self::SORT_ORDER_DESC
    ];

    const INSERT_ERROR_FALSE = 0;
    const INSERT_ERROR_DEFAULT = 1;
    const INSERT_ERROR_FILM_EXISTS = 2;
    const INSERT_ERROR_VALIDATION = 3;

    private $filmFormats;

    public function getFilmFormats() {
        if (!$this->filmFormats) {
            $mysqli = $this->getConnection();
            $query = 'SELECT * FROM ' . self::$film_formats_table;
            $query_result = $mysqli->query($query);

            if ($query_result->num_rows > 0) {
                $filmFormats = $query_result->fetch_all(MYSQLI_ASSOC);
                $this->filmFormats = array_reduce($filmFormats, function ($result, $formatData) {
                    $result[$formatData['id']] = $formatData['title'];
                    return $result;
                });
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
        
        $query = $this->getFilmsBasicSelectQuery();
        $helper = $this->getFilmSearchHelper();
        $helper->applyFilmsSearchParams($query, $searchParams, $stmtParams);
 
        $stmt = $mysqli->prepare($query);
        if (count($stmtParams['types'])) {
            $stmt->bind_param($stmtParams['types'], ...$stmtParams['variables']);
        }
        $stmt->execute();
        $query_result = $stmt->get_result();
        $stmt->close();

        if ($query_result->num_rows > 0) {
            $filmsData = $query_result->fetch_all(MYSQLI_ASSOC) ?: [];
            $this->fetchFilmsActors($filmsData);
            return $filmsData;
        }
        return [];
    }

    protected function getFilmsBasicSelectQuery() {
        $a = self::$table_name;
        $b = self::$film_formats_table;
        $query = "SELECT $a.id, $a.title, $a.release_year, $b.id as format_id, $b.title AS format_title";
        $query .= ' FROM ' . $a;
        $query .= " INNER JOIN $b ON $a.format_id = $b.id";
        return $query;
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

    public function getFilmsByTitle($title) {
        $mysqli = $this->getConnection();
        $query = $this->getFilmsBasicSelectQuery();
        $query .= ' WHERE ' . self::$table_name . '.title = ?';

        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('s', $title);

        $stmt->execute();
        $query_result = $stmt->get_result();
        $stmt->close();

        if ($query_result->num_rows > 0) {
            $filmsData = $query_result->fetch_all(MYSQLI_ASSOC) ?: [];
            $this->fetchFilmsActors($filmsData);
            return $filmsData;
        }
        return [];
    }

    public function addFilms($filmsDataArray) {
        $mysqli = $this->getConnection();
        $searchHelper = $this->getFilmSearchHelper();
        $dataHelper = $this->getFilmDataHelper();
        $result = [];

        $query = 'INSERT INTO ' . self::$table_name;
        $query .= ' (title, release_year, format_id)';
        $query .= ' VALUES (?, ?, ?)';

        $title = null;
        $release_year = null;
        $format_id = null;
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("sii", $title, $release_year, $format_id);
        foreach ($filmsDataArray as $data) {
            $title = $data['title'];
            $release_year = $data['release_year'];
            $format_id = $data['format_id'];

            try {
                $validationErrors = $dataHelper->validateFilmData($data);
                if (!empty($validationErrors)) {
                    $result[] = [
                        'error' => self::INSERT_ERROR_VALIDATION,
                        'message' => "Validation Failed",
                        'data' => $validationErrors
                    ];
                    continue;
                }
                if ($searchHelper->checkIfExists($data)) {
                    $result[] = [
                        'error' => self::INSERT_ERROR_FILM_EXISTS,
                        'message' => "\"$title\" with the same release year, format and actors already exists.",
                        'data' => $data,
                    ];
                    continue;
                }
                if ($stmt->execute()) {
                    $filmId = $mysqli->insert_id;
                    $this->addActors($filmId, $data['actors']);
                    $data['id'] = $filmId;
                    $result[] = [
                        'error' => self::INSERT_ERROR_FALSE,
                        'message' => "\"$title\" was successfully saved.",
                        'data' => $data,
                    ];
                } else {
                    throw new Exception("Failed to insert \"$title\".");
                }
            } catch (Exception $e) {
                $result[] = [
                    'error' => self::INSERT_ERROR_DEFAULT,
                    'message' => $e->getMessage(),
                    'data' => $data,
                ];
            }
        }
        $stmt->close();
        return $result;
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

    protected function getFilmSearchHelper(): FilmSearchHelper {
        if (empty($this->film_search_helper)) {
            $this->film_search_helper = new FilmSearchHelper($this);
        }
        return $this->film_search_helper;
    }

    protected function getFilmDataHelper(): FilmDataHelper {
        if (empty($this->film_data_helper)) {
            $this->film_data_helper = new FilmDataHelper($this);
        }
        return $this->film_data_helper;
    }

    public static function getFilmFormatsTable() {
        return self::$film_formats_table;
    }

    public static function getFilmActorsListTable() {
        return self::$film_actors_list_table;
    }
}
