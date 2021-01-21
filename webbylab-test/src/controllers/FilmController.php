<?php

namespace Controllers;

use Core\Controller;
use Exception;
use Helpers\FilmDataHelper;
use Models\FilmModel;

class FilmController extends Controller {

    public function addAction() {
        $this->setJsonResponseHeaders();
        $filmModel = $this->getFilmModel();

        try {
            $formattedData = $this->formatAddActionData();
            $result = $filmModel->addFilms([$formattedData])[0];
        } catch (Exception $e) {
            return $this->withResponseCode(500);
        }
        switch ($result['error']) {
            case FilmModel::INSERT_ERROR_VALIDATION:
                $responseCode = 422;
                break;
            case FilmModel::INSERT_ERROR_FALSE:
                $responseCode = 201;
                break;
            case FilmModel::INSERT_ERROR_DEFAULT:
            case FilmModel::INSERT_ERROR_FILM_EXISTS:
            default:
                $responseCode = 200;
                break;
        }
        return $this->withResponseCode($responseCode, json_encode($result));
    }

    protected function formatAddActionData(): array {
        $title = trim($_POST['title']);
        $actors = array_map('trim', $_POST['actors'] ?: []);
        ksort($actors);
        return [
            'title' => $title,
            'release_year' => $_POST['release_year'],
            'format_id' => $_POST['format_id'],
            'actors' => $actors
        ];
    }

    public function deleteAction() {
        $filmModel = $this->getFilmModel();

        try {
            $filmModel->deleteFilm($_POST['id']);
        } catch (Exception $e) {
            return $this->withResponseCode(500);
        }
        return $this->withResponseCode(200);
    }

    protected function getFilmModel(): FilmModel {
        if (empty($this->film_model)) {
            $this->film_model = new FilmModel();
        }
        return $this->film_model;
    }
}
