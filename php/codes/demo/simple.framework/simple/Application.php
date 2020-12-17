<?php
namespace simple;

class Application{
    
    public $config = null;
    public $module = '';
    public $controller = '';
    public $action = '';
    public $pathinfo = '';
    
    public function __construct($config){
    
        $config = (object) $config;
    
        $this->pathinfo = [$config->module, $config->controller, $config->action];
        if(isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] != '/'){
            $this->pathinfo = explode('/', trim($_SERVER['PATH_INFO'], '/')) + $this->pathinfo;
        }
        
        list($this->module, $this->controller, $this->action) = $this->pathinfo;
        $this->controller = ucfirst($this->controller);
        
    }
    
    public  function run(){
        
        $controller =  '\\app\\' .$this->module . '\\controller\\' . $this->controller;
        $controller =  new $controller($this);
        $controller->{$this->action}();
        
    }
    
    
    
}