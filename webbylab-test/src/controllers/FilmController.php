<?php

namespace Controllers;

use Core\Controller;

class FilmController extends Controller {
    public function indexAction() {
        return "film page";
    }

    public function addAction() {
        return "added film";
    }

    public function deleteAction() {
        return "deleted film";
    }
}
