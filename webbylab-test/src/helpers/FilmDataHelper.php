<?php

namespace Helpers;

use Models\FilmModel;

class FilmDataHelper {

    public function __construct(?FilmModel $filmModel = null) {
        $this->film_model = $filmModel;
    }

    public function validateFilmData(array $data): array {
        $errors = [];
        $filmModel = $this->getFilmModel();

        if (empty($data['title'])) {
            $errors['title'][] = "Empty field";
        } else {
            $title = $data['title'];
            if (!preg_match('/^[a-zA-Z0-9 \-\(\)\.,\!:"\']+$/', $title)) {
                $errors['title'][] = "Allowed only latin letters, digits, spaces and the following chars: [-().,!:\"'].";
            }
            if (strlen($title) > 50) {
                $errors['title'][] = "Max length: 50 characters.";
            }
        }

        if (empty($data['release_year'])) {
            $errors['release_year'][] = "Empty field";
        } else {
            $releaseYear = intval($data['release_year']);
            $minYear = 1850;
            $maxYear = intval(date('Y'));

            if (!is_numeric($releaseYear)) {
                $errors['release_year'][] = "Should be an integer";
            } else if ($releaseYear < $minYear || $releaseYear > $maxYear) {
                $errors['release_year'][] = "Allowed date range: ($minYear - $maxYear)";
            }
        }

        if (empty($data['format_id'])) {
            $errors['format_id'][] = "Empty field";
        } else {
            $formatId = $data['format_id'];
            $filmFormatIds = array_keys($filmModel->getFilmFormats());
            if (!in_array($formatId, $filmFormatIds)) {
                $errors['format_id'][] = "Unsupported film format id";
            }
        }

        if (!empty($data['actors'])) {
            $actors = $data['actors'];

            foreach ($actors as $key => $actor) {
                if (empty($actor)) {
                    $errors['actors'][$key][] = "Empty field";
                } else {
                    if ($this->checkIfActorWasAdded($actors, $actor, $key)) {
                        $errors['actors'][$key][] = "Actor \"$actor\" was already added";
                    }
                    if (!preg_match('/^[a-zA-Z \-\.\']+$/', $actor)) {
                        $errors['actors'][$key][] = "Allowed only latin letters, spaces and the following chars: [-.'].";
                    }
                    if (strlen($actor) > 120) {
                        $errors['actors'][$key][] = "Max length: 120 characters.";
                    }
                }
            }
        }

        return $errors;
    }

    protected function checkIfActorWasAdded($actors, $actor, $key): bool {
        foreach ($actors as $currentKey => $value) {
            if ($currentKey == $key) {
                break;
            }
            if ($value == $actor) {
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
