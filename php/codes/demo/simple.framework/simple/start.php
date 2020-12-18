<?php
namespace simple;

define('FRAME_PATH', __DIR__ . '/');
define('APP_PATH', __DIR__ . '/../application/');
define('CONF_PATH', __DIR__. '/../config/');

require FRAME_PATH . 'Application.php';

spl_autoload_register(function($class){
    
    $root = strstr($class, '\\', true);
    $class = strstr($class, '\\');
    if($root == 'app'){
        
        require APP_PATH . '/' . $class . '.php';
        
    }else if($root == 'simple'){
        
        require FRAME_PATH . $class . '.php';
    }
    

});


$app = new Application(require CONF_PATH . 'config.php');
$app->run();