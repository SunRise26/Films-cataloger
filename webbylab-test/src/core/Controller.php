<?php

namespace Core;

class Controller {
	public $view;
    public $requestData;

    public function __construct(array $requestData) {
        $this->requestData = $requestData;
    }

    public function getView() {
        if (!isset($this->view)) {
            $className = get_class($this);
            $pattern = '/^Controllers\\\\(.*)Controller$/';
            preg_match($pattern, $className, $matches);
            $viewClassName = 'Views\\' . $matches[1] . 'View';
            $this->view = new $viewClassName();
        }
        return $this->view;
    }

    protected function setJsonResponseHeaders() {
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json');
    }

    protected function withResponseCode($code = 200, $payload = null) {
        http_response_code($code);
        return $payload;
    }
}
