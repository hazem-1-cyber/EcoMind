<?php
// Core/Router.php
class Router {
    private $routes = [];
    private $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function get($path, $controller, $method) {
        $this->addRoute('GET', $path, $controller, $method);
    }

    public function post($path, $controller, $method) {
        $this->addRoute('POST', $path, $controller, $method);
    }

    public function any($path, $controller, $method) {
        $this->addRoute('ANY', $path, $controller, $method);
    }

    private function addRoute($httpMethod, $path, $controller, $method) {
        $this->routes[] = [
            'method' => $httpMethod,
            'path' => $path,
            'controller' => $controller,
            'action' => $method
        ];
    }

    public function dispatch() {
        $page = $this->request->get('page', 'events');
        
        foreach ($this->routes as $route) {
            if ($route['path'] === $page) {
                if ($route['method'] === 'ANY' || $route['method'] === $this->request->method()) {
                    $controllerName = $route['controller'];
                    $action = $route['action'];
                    
                    $controller = new $controllerName($this->request);
                    return $controller->$action();
                }
            }
        }
        
        // 404 - redirect to home
        $response = new Response();
        $response->redirect('index.php?page=events');
    }
}
