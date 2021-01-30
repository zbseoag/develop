<?php

/**
 * 自动加载类文件
 * author: zbseoag
 * 示例：
 *
 * //自动加载类名以'Model' 开头的类, 加载：./lib/Model/*.class.php 文件
 * Autoload::home('./lib/')
 * ->match('\Model') //匹配以 \Model 开头的类
 * ->suffix('class.php') //类文件名后缀
 * ->register() //注册到加载函数
 *
 * $user = new \Model\User();  //实例化一个对象
 * echo Autoload::$file; //输出被加载类文件路径
 *
 * //注册并加载一个类
 * Autoload::home(__DIR__)->register()->load('Mysql');
 * echo Autoload::$file; //输出被加载类文件路径
 *
 * //仅仅生成文件路径
 * echo Autoload::home(__DIR__)->suffix('class.php')->make('Mysql');
 *
 * //输出加载失败的文件
 * echo Autoload::error();
 *
 */

Autoload::home(__DIR__)->register()->load('Debug');// Debug类中有几个函数,函数不会自动加载,所以这里手动加载 Debug类
Autoload::home('/e/develop/php/dev/')->register();


class Autoload {

    public $home = '.';
    public $match = '';
    public $suffix = '.php';
    public static $file = '';
    public static $error = [];

    private function __construct($home = '.') {

        //删除后边斜杠
        $this->home = rtrim($home, '\\/');
    }

    /**
     * 根目录
     * @param $value
     * @return $this
     */
    public static function home($value = '') {

        return new static($value);
    }


    /**
     * 匹配命名空间
     * @param $value
     * @return $this
     */
    public function match($value) {

        $this->match = trim($value, '/');
        return $this;
    }

    /**
     * 类名文件后缀，如 '.class.php'
     * @param $value
     * @return $this
     */
    public function suffix($value) {

        $this->suffix = '.'. ltrim($value, '.');
        return $this;
    }

    public function register() {

        spl_autoload_register(function($class) {

            $this->load($class);
        });

        return $this;
    }

    /**
     * 生成文件路径
     * @param $class
     * @return string
     */
    public function make($class) {

        //删除类名两边的斜杠 \
        $class = trim($class, '\\');

        self::$file = '';
        if(empty($this->match) || strpos($class, $this->match) === 0){
            self::$file = strtr($this->home . '/' . $class, '\\', '/') . $this->suffix;
        }
        return $this;

    }

    /**
     * 手动加载
     * @param $class
     */
    public function load(string $class): string {

        if(!empty($class)) $this->make($class);

        if(is_file(self::$file)){
            require_once self::$file;
        }else{
            self::$error[] = self::$file;
        }

        return self::$file;

    }

    /**
     * 动态创建对象
     * @param $class
     * @param $arguments
     * @return object
     * @throws ReflectionException
     */
    public static function create($class, $arguments) {

        $class = new \ReflectionClass($class);
        return $class->newInstanceArgs($arguments);
    }


    public static function error() {

        return print_r(self::$error, true);
    }


}

