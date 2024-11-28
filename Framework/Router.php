<?php

namespace Framework;

use App\Controllers\ErrorController;
use Framework\Middleware\Authorize;


class Router{
    protected $routes = [];

    /**
     * add a new route
     * @param string$method
     * @param string $uri
     * @param string $action
     * @return void
     */
    public function registerRoute($method, $uri, $action, $middleware= []){
        list($controller, $controllerMethod) = explode('@', $action);
        
        $this->routes[] = [
            'method' => $method,
            'uri'=> $uri,
            'controller'=> $controller,
            'controllerMethod'=> $controllerMethod,
            'middleware' => $middleware
        ];
    }

    /**
     * Add a GET route
     * @param string $uri
     * @param string $controller 
     * @param array $middleware
     * @return void
     */

    public function get($uri, $controller, $middleware = []){
        $this->registerRoute('GET',$uri, $controller, $middleware);
    }
        /**
     * Add a POST route
     * @param string $uri
     * @param string $controller 
     * @return void
     */

     public function post($uri, $controller, $middleware = []){
        $this->registerRoute('POST',$uri, $controller, $middleware);
     }
            /**
     * Add a PUT route
     * @param string $uri
     * @param string $controller 
     * @return void
     */

     public function put($uri, $controller, $middleware = []){
        $this->registerRoute('PUT',$uri, $controller, $middleware);
     }
            /**
     * Add a DELETE route
     * @param string $uri
     * @param string $controller 
     * @return void
     */

     public function delete($uri, $controller, $middleware = []){
        $this->registerRoute('DELETE',$uri, $controller, $middleware);
     }

     

     /**
      * Route the request
      * @param string $uri
      * @param string $method
      * @return void
      */
      public function route($uri){

        
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        //  check for _method input

        if($requestMethod === 'POST' && isset($_POST['_method'])){
            //Overwrite request method with _method input
            $requestMethod = strtoupper($_POST['_method']);
        }
        $params = []; // Initialize params
    
        foreach ($this->routes as $route) {
            // Split the current URI into segments
            $uriSegments = explode('/', trim($uri, '/'));
    
            // Split the route URI into segments
            $routeSegments = explode('/', trim($route['uri'], '/'));
    
            $match = true;
    
            // Check if the number of segments match
            if (count($uriSegments) === count($routeSegments) && strtoupper($route['method']) === $requestMethod) {
                // Check each segment
                for ($i = 0; $i < count($routeSegments); $i++) {
                    // If the current segment doesn't match and there is no param
                    if ($uriSegments[$i] !== $routeSegments[$i] && !preg_match('/\{(.+?)\}/', $routeSegments[$i])) {
                        $match = false;
                        break;
                    }
    
                    // Check for the param and add it to the params array
                    if (preg_match('/\{(.+?)\}/', $routeSegments[$i], $matches)) {
                        $params[$matches[1]] = $uriSegments[$i];
                    }
                }
            } else {
                $match = false;
            }
    
            if ($match) {
                foreach ($route['middleware'] as $middleware) {
                    (new Authorize()) -> handle($middleware);
                }
                $controller = 'App\\Controllers\\' . $route['controller'];
                $controllerMethod = $route['controllerMethod'];
    
                // Instantiate controller and call method
                $controllerInstance = new $controller();
                $controllerInstance->$controllerMethod($params);
                return;
            }
        }
    
        // Handle not found
        ErrorController::notFound();
        return;
    }
    
}