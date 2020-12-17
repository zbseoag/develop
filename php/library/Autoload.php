<?php

/**
 * 自动加载类文件
 * author: zbseoag
 *
//自动加载类名以'Model' 开头的类, 加载：./lib/Model/*.class.php 文件
Autoload::home('./lib/')->match('\Model')->suffix('class.php')->register()
$user = new \Model\User();
echo Autoload::$file;

//测试文件路径
echo Autoload::home(__DIR__)->make('Mysql');

//注册再手动加载一个试
Autoload::home(__DIR__)->register()->load('Mysql');

 *
 */

class Autoload {
    
    public static $init;
    public $suffix = 'php';
    public $home = './';
    public static $file = '';
    public $match = '';

    private function __construct($home = ''){

        //删除 home 两边的斜杠： Model\User
        $this->home = trim($home, '\\/');
    }

    /**
     * 文件根目录
     * @param $value
     * @return $this
     */

    public static function home($value = ''){

        if(!self::$init) self::$init = new static($value);
        return self::$init;
    }


    /**
     * 匹配命名空间
     * @param $value
     * @return $this
     */
    public function match($value){

        $this->match = trim($value, '\\/');
        return $this;
    }

    /**
     * 类名文件后缀，如 'class.php'
     * @param $value
     * @return $this
     */
    public function suffix($value){
        
        $this->suffix = ltrim($value, '.');
        return $this;
    }

    public function register(){
        spl_autoload_register(function($class){
            $this->load($class);
        });

        return $this;
    }

    /**
     * 生成文件路径
     * @param $class
     * @return string
     */
    public function make($class){

        //删除 class 两边的斜杠： Model\User
        $class = trim($class, '\\');

        self::$file = '';
        if(empty($this->match) || strpos($class, $this->match) === 0){
            self::$file = strtr($this->home . '/' . $class, '\\',  '/') . '.' . $this->suffix;
        }

        return $this;

    }

    /**
     * 手动加载
     * @param $class
     */
    public function load($class = ''){

        if(!empty($class)) $this->make($class);

        if(self::$file) require self::$file;
        return $this;
    }

    /**
     * 动态创建对象
     * @param $class
     * @param $arguments
     * @return object
     * @throws ReflectionException
     */
    public static function create($class, $arguments){

        $class = new \ReflectionClass($class);
        return $class->newInstanceArgs($arguments);
    }


    public function functions(){
        return spl_autoload_functions();
    }


    
}