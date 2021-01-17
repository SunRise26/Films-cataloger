<?php

namespace Core\Router;

use Exception;

class Route {

    const ALLOWED_REQUEST_METHODS = [
        'GET',
        'POST'
    ];

    private static $registeredRoutes;

    public function __construct() {
        foreach (self::ALLOWED_REQUEST_METHODS as $method) {
            self::$registeredRoutes[$method] = [];
        }
    }
    
    /**
     * Set new route using GET method
     *
     * @param  string $route
     * @param  mixed $executable
     * @return void
     */
    public static function get(string $route, $executable) {
        self::setRoute($route, 'GET', $executable);
    }

    /**
     * Set new route using POST method
     *
     * @param  string $route
     * @param  mixed $executable
     * @return void
     */
    public static function post(string $route, $executable) {
        self::setRoute($route, 'POST', $executable);
    }
    
    /**
     * Set new route
     * 
     * @param  string $route
     * @param  string $method
     * @param  mixed $executable
     * @return void
     */
    protected static function setRoute(string $route, string $method, $executable) {
        if (!in_array($method, self::ALLOWED_REQUEST_METHODS)) {
            throw new Exception("Trying to register route with unsupported request method");
        }
        self::$registeredRoutes[$method][$route] = $executable;
    }
    
    /**
     * Execute route function by route and request method
     *
     * @param  string $route
     * @param  string $method
     * @param  mixed $requestData
     * @return void
     */
    public static function executeRouteFunction(string $route, string $method, $requestData) {
        if (empty(self::$registeredRoutes[$method][$route])) {
            http_response_code(404);
            return;
        }

        $executable = self::$registeredRoutes[$method][$route];
        switch (gettype($executable)) {
            case 'object':
                echo $executable($requestData);
                break;
            case 'string':
                echo (new $executable($requestData))->indexAction();
                break;
            case 'array':
                $classInstance = new $executable[0]($requestData);
                $actionName = $executable[1] . "Action";
                echo call_user_func([$classInstance, $actionName]);
                break;
            default:
                throw new Exception("Wrong route executable format");
        }
    }
}
