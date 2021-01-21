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
        $view->setData('search_sort_order_oprions', FilmModel::$search_sort_order_options);

        return $view->toHtml();
    }

    protected function formatIndexActionData() {
        return [
            's_key' => !empty($_GET['s_key']) ? trim($_GET['s_key'])  : '',
            's_type' => $_GET['s_type'] ?: FilmModel::SEARCH_BY_ALL,
            's_sort_order' => $_GET['s_sort_order'] ?: FilmModel::SORT_ORDER_ASC
        ];
    }
}
