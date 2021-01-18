<?php

namespace Controllers;

use Core\Controller;
use Exception;
use Models\FilmModel;

class FilmController extends Controller {

    public function addAction() {
        $this->setJsonResponseHeaders();
        $filmModel = new FilmModel();

        try {
            $film_data = $this->formatAddActionData();
            $result = $filmModel->addFilm($film_data);
        } catch (Exception $e) {
            return $this->withResponseCode(500);
        }
        return $this->withResponseCode($result ? 201 : 422);
    }

    protected function formatAddActionData(): array {
        $title = trim($_POST['title']);
        $actors = array_filter(array_map('trim', $_POST['actors'] ?: []));

        return [
            'title' => $title,
            'year' => $_POST['year'],
            'format_id' => $_POST['format_id'],
            'actors' => $actors
        ];
    }

    public function deleteAction() {
        $filmModel = new FilmModel();

        try {
            $filmModel->deleteFilm($_POST['id']);
        } catch (Exception $e) {
            return $this->withResponseCode(500);
        }
        return $this->withResponseCode(200);
    }
}
