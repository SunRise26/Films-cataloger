<?php

namespace Controllers;

use Core\Controller;
use Exception;
use Models\FilmModel;

class ImportController extends Controller {

    public function importAction() {
        $fileData = $_FILES['file'];

        switch ($fileData['type']) {
            case 'text/plain':
                return $this->processTextPlain($fileData);
            default:
                return $this->withResponseCode(422, "Unsupported file type. Currently supported: text/plain (e.g. .txt)");
        }
    }

    protected function processTextPlain($fileData) {
        try {
            $filmModel = new FilmModel();
            $this->film_formats = array_map('strtolower', $filmModel->getFilmFormats());

            $text = file_get_contents($fileData["tmp_name"]);
            $filmTextBlocks = array_filter(explode(PHP_EOL . PHP_EOL, $text));
            $filmsData = array_map([$this, 'formatFilmData'], $filmTextBlocks);
            $filmModel->addFilms($filmsData);
        } catch (Exception $e) {
            return $this->withResponseCode(500);
        }
        return $this->withResponseCode(200);
    }

    protected function formatFilmData($filmTextBlock) {
        $filmFormats = $this->film_formats;
        $lines = explode(PHP_EOL, $filmTextBlock);
        $lines = array_reduce($lines, function ($result, $line) use ($filmFormats) {
            $key_value = explode(':', $line);
            $key = strtolower(trim($key_value[0]));
            $value = trim($key_value[1]);
            if ($key == 'format') {
                $key = 'format_id';
                $value = array_search(strtolower($value), $filmFormats);
            } else if ($key == 'stars') {
                $value = explode(',', $value);
                $value = array_map('trim', $value);
            }
            $result[$key] = $value;
            return $result;
        });
        $lines = array_filter($lines);
        
        return [
            'title' => $lines['title'],
            'release_year' => $lines['release year'],
            'format_id' => $lines['format_id'],
            'actors' => $lines['stars']
        ];
    }
}
