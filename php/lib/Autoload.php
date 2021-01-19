<?php

/**
 * 自动加载类文件
 * author: zbseoag
示例：

//自动加载类名以'Model' 开头的类, 加载：./lib/Model/*.class.php 文件
Autoload::home('./lib/')
    ->match('\Model') //匹配以 \Model 开头的类
    ->suffix('class.php') //类文件名后缀
    ->register() //注册到加载函数

$user = new \Model\User();  //实例化一个对象
echo Autoload::$file; //输出被加载类文件路径

//注册并加载一个类
Autoload::home(__DIR__)->register()->load('Mysql');
echo Autoload::$file; //输出被加载类文件路径

//仅仅生成文件路径
echo Autoload::home(__DIR__)->suffix('class.php')->make('Mysql');

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
     * 根目录
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

    public function __toString(){

        return self::$file;
    }


}

