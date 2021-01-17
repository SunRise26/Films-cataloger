<?php

namespace Core;

use Core\Router\Route;

class Router {

    private array $requestData;

    public function __construct() {
        $this->requestData['uri'] = $_SERVER['REQUEST_URI'];
        $this->requestData['method'] = $_SERVER['REQUEST_METHOD'];

        $parsed_uri = parse_url($this->requestData['uri']);
        $this->requestData['url_path'] = $parsed_uri['path'];
        $this->requestData['url_query'] = $parsed_uri['query'];
    }

    public function handleRequest() {
        switch ($this->requestData['method']) {
            case 'GET':
                return $this->handleGetRequest();
            case 'POST':
                return $this->handlePostRequest();
            default:
                throw new \Exception("Unsupported request method");
        }
    }

    protected function prepareGetRequestData() {
        $this->requestData['payload'] = $_GET;
    }

    protected function handleGetRequest() {
        $this->prepareGetRequestData();
        $route = $this->requestData['url_path'];
        Route::executeRouteFunction($route, 'GET', $this->requestData);
    }

    protected function preparePostRequestData() {
        $this->requestData['payload'] = $_POST;
    }

    protected function handlePostRequest() {
        $this->preparePostRequestData();
        $route = $this->requestData['url_path'];
        Route::executeRouteFunction($route, 'POST', $this->requestData);
    }

}
