<?php

namespace Helpers;

use Models\FilmModel;

class FilmSearchHelper {

    public function __construct(?FilmModel $filmModel = null) {
        $this->film_model = $filmModel;
    }

    public function applyFilmsSearchParams(string &$query, array $searchParams, &$stmtParams) {
        switch ($searchParams['s_type']) {
            case FilmModel::SEARCH_BY_TITLE:
                $this->applyFilmsSearchByTitle($query, $searchParams, $stmtParams);
                break;
            case FilmModel::SEARCH_BY_ACTOR:
                $this->applyFilmsSearchByActors($query, $searchParams, $stmtParams);
                break;
            default:
                $this->applyFilmsSearchDefault($query, $searchParams, $stmtParams);
                break;
        }

        switch ($searchParams['s_sort_order']) {
            case FilmModel::SORT_ORDER_DESC:
                $query .= " ORDER BY title DESC";
                break;
            default:
                $query .= " ORDER BY title ASC";
                break;
        }
    }

    protected function applyFilmsSearchByTitle(string &$query, array $searchParams, &$stmtParams) {
        if ($key = $searchParams['s_key']) {
            $a = FilmModel::getTable();
            $query .= " WHERE $a.title LIKE ?";
            $stmtParams['types'] .= 's';
            $stmtParams['variables'][] = "%$key%";
        }
    }

    protected function applyFilmsSearchByActors(string &$query, array $searchParams, &$stmtParams) {
        if ($key = $searchParams['s_key']) {
            $a = FilmModel::getTable();
            $b = FilmModel::getFilmActorsListTable();
            $query .= " WHERE $a.id IN (SELECT DISTINCT film_id FROM $b WHERE full_name like ?)";
            $stmtParams['types'] .= 's';
            $stmtParams['variables'][] = "%$key%";
        }
    }

    protected function applyFilmsSearchDefault(string &$query, array $searchParams, &$stmtParams) {
        if ($key = $searchParams['s_key']) {
            $a = FilmModel::getTable();
            $b = FilmModel::getFilmActorsListTable();
            $query .= " WHERE $a.title LIKE ?";
            $query .= " OR $a.id IN (SELECT DISTINCT film_id FROM $b WHERE full_name like ?)";
            $stmtParams['types'] .= 'ss';
            $stmtParams['variables'][] = "%$key%";
            $stmtParams['variables'][] = "%$key%";
        }
    }

    public function checkIfExists($filmData): bool {
        $filmModel = $this->getFilmModel();
        $films = $filmModel->getFilmsByTitle($filmData['title']);

        foreach($films as $relatedFilm) {
            $relatedFilm['actors'] = $relatedFilm['actors'] ?: [];
            sort($filmData['actors']);
            sort($relatedFilm['actors']);

            if (
                $filmData['title'] == $relatedFilm['title'] &&
                $filmData['format_id'] == $relatedFilm['format_id'] &&
                $filmData['release_year'] == $relatedFilm['release_year'] &&
                $filmData['actors'] == $relatedFilm['actors']
            ) {
                return true;
            }
        }
        return false;
    }

    protected function getFilmModel(): FilmModel {
        if (empty($this->film_model)) {
            $this->film_model = new FilmModel();
        }
        return $this->film_model;
    }

}
