<?php
// Core/View.php
class View {
    private $viewPath;
    private $data = [];
    private $layout = null;

    public function __construct($viewPath, $data = []) {
        $this->viewPath = $viewPath;
        $this->data = $data;
    }

    public function with($key, $value) {
        $this->data[$key] = $value;
        return $this;
    }

    public function layout($layoutPath) {
        $this->layout = $layoutPath;
        return $this;
    }

    public function render() {
        extract($this->data);
        
        ob_start();
        require $this->viewPath;
        $content = ob_get_clean();
        
        if ($this->layout) {
            extract($this->data);
            ob_start();
            require $this->layout;
            return ob_get_clean();
        }
        
        return $content;
    }

    public static function make($viewPath, $data = []) {
        return new self($viewPath, $data);
    }
}
