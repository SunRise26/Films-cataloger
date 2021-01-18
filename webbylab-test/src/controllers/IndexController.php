<?php

namespace Controllers;

use Core\Controller;
use Models\FilmModel;

class IndexController extends Controller {
    public function indexAction() {
        $view = $this->getView();
        $filmModel = new FilmModel();

        $view->setData('film_formats', $filmModel->getFilmFormats());
        $view->setData('films', $filmModel->getFilmsData());

        return $view->toHtml();
    }
}
