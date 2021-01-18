<?php

namespace Controllers;

use Core\Controller;
use Models\FilmModel;

class IndexController extends Controller {
    public function indexAction() {
        $view = $this->getView();
        $filmModel = new FilmModel();

        $searchParams = $this->formatIndexActionData();
        $view->setData('films', $filmModel->getFilmsData($searchParams));
        $view->setData('film_formats', $filmModel->getFilmFormats());
        $view->setData('search_by_options', FilmModel::$search_by_options);

        return $view->toHtml();
    }

    protected function formatIndexActionData() {
        return [
            's_key' => $_GET['s_key'] ?: '',
            's_type' => $_GET['s_type'] ?: false,
        ];
    }
}
