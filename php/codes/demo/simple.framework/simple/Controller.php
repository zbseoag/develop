<?php 

namespace simple;

class Controller{
    
    public $view = null;
    public $app = null;
     
    public function __construct($app){
    
        $this->view = new View();
        $this->app = $app;
    
    }
    
    
    protected function display($url = ''){
        
        extract(get_object_vars($this->view));
        
        $template = empty($url)? APP_PATH . $this->app->pathinfo[0] . '/view/' .$this->app->pathinfo[1] . '/'.$this->app->pathinfo[2].'.php'
            : '../view/'.$url.'.php' ;
        
        require $template;
    
    }
    
    protected function location($path=''){
        
        header('Location: '.$this->url($path));
    }
    
    protected function url($path = ''){
        
        if(!empty($path)) $path = "?u=$path";
        return $_SERVER['PHP_SELF'] . $path;
    }
    
    
    
}



