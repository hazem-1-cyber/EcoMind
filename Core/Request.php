<?php
// Core/Request.php
class Request {
    private $get;
    private $post;
    private $server;
    private $method;
    private $uri;

    public function __construct() {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
        $this->method = $this->server['REQUEST_METHOD'] ?? 'GET';
        $this->uri = $this->server['REQUEST_URI'] ?? '/';
    }

    public function get($key, $default = null) {
        return $this->get[$key] ?? $default;
    }

    public function post($key, $default = null) {
        return $this->post[$key] ?? $default;
    }

    public function all() {
        return array_merge($this->get, $this->post);
    }

    public function method() {
        return $this->method;
    }

    public function isPost() {
        return $this->method === 'POST';
    }

    public function isGet() {
        return $this->method === 'GET';
    }

    public function uri() {
        return $this->uri;
    }

    public function has($key) {
        return isset($this->get[$key]) || isset($this->post[$key]);
    }
}
