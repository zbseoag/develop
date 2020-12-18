<?php
/**

 //使用PHP 标准库实现
class Observable implements SplSubject{

    private $storage;

    function __construct(){
        $this->storage = new SplObjectStorage();
    }

    function attach(SplObserver $observer){
        $this->storage->attach($observer);
    }

    function detach(SplObserver $observer){
        $this->storage->detach($observer);
    }

    function notify(){

        foreach ($this->storage as $obj) {
            $obj->update($this);
        }
    }

}

abstract class Observer implements SplObserver {

    private $observable;

    function __construct(Observable $observable){

        $this->observable = $observable;
        $observable->attach($this);
    }

    function update(SplSubject $subject){

        if ($subject === $this->observable){
            $this->doUpdate($subject);
        }
    }

    abstract function doUpdate(Observable $observable);
}



class ConcreteObserver extends Observer{

    function doUpdate(Observable $observable){

    }
}

 *
 */




class Book {

    private $observers = array();

    public $content = '';

    //添加观察者
    public function attach($observer){
        $this->observers[] = $observer;
    }

    //删除观察者
    public function detach($observer){

        $key = array_search($observer, $this->observers, true);
        if($key) unset($this->observers[$key]);
    }

    //通知观察者
    public function notify(){

        foreach ($this->observers as $obj){
            $obj->update($this);
        }
    }

    //设置内容
    public function setContent($content){

        $this->content = $content;
    }


}



class Person {

    public $name;

    public function __construct($name){
        $this->name = $name;
    }

    public function update($observable){

        echo $this->name . ' 看到: ' . $observable->content . "\n";
    }

}

//创建被监视对象
$book = new Book();

//创建两个观察者
$person1 = new Person('张三');
$person2 = new Person('李四');

//添加两个观察者
$book->attach($person1);
$book->attach($person2);


//被监视对象发生改变
$book->setContent('无论我们最后生疏到什么样子，曾经我对你的好，是真心的。');

//通知所有观察者
$book->notify();


