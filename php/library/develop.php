<?php
require 'Autoload.php';
\Autoload::home(__DIR__)->register()->load('Debug');

class Work {

    public static $init = null;

    public function database(){
        
        $database = array(
            'hostname' => 'localhost',
            'username' => 'root',
            'password' => '',
            'database' => 'test',
            'charset' => 'utf8',
        );
    
        if(!class_exists('Mysql')) self::load('Mysql');
        
        return Mysql::instance()->connect($database);
        
    }

	
}








