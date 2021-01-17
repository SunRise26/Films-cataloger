<?php

namespace Controllers;

use Core\Controller;
use Models\FilmModel;

class IndexController extends Controller {
    public function indexAction() {
        $view = $this->getView();
        $filmModel = new FilmModel();

        return $view->toHtml();
    }
}
