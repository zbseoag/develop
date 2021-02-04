<?php
declare(strict_types=1);

function timeWindow($rule, $redis, $zkey) {

    foreach($rule as $key => $item){

        $score = time();
        $redis->multi();
        $redis->zRemRangeByScore($zkey, 0, $score - $key);//移除窗口以外的数据
        $redis->zRange($zkey, 0, -1, true);
        $record = $redis->exec();

        p($record);
        if(empty($record[1]) || count($record[1]) < $item){
            $redis->zAdd($zkey, $score, mt_rand(1000, 9999) . substr($score, -5));
            p('==================================================> true');
            return true;

        }else{
            continue;
        }

    }

    p(false);
    return false;
}

$rules = array(
    10 => 2,
    20 => 3,
);

//
//while(1){
//
//    timeWindow($rules, $redis, 'aaaa' . 1);
//    sleep(1);
//}
//

class RedisSent extends RedisSentinel {

    protected array $sentinel = [];

    public function __construct($host, ?int $port = null, float $timeout = 0, ?string $persistent = null, int $retryInterval = 0, float $readTimeout = 0){

        if(is_array($host)){
            foreach($host as $item){
                $this->sentinel[] = new parent(...$item);
            }
        }else{
            $this->sentinel[] = new parent($host, $port, $timeout, $persistent, $retryInterval, $readTimeout);
        }

    }

    public function getMasterRedis(string $master,  $timeout = 0.0, $reserved = null, $retryInterval = 0, $readTimeout = 0.0) {

        foreach($this->sentinel as $key => $sentinel){

            try{
                $master = $sentinel->master($master);
                $redis = new Redis();
                $redis->connect($master['ip'], $master['port'], $timeout, $reserved, $retryInterval, $readTimeout);
                return $redis;

            }catch(\RedisException $exception){
                continue;
            }
        }
        throw $exception;

    }

}

class a{


    public function demo(...$argv){

        switch(func_num_args()){
            case 1: $this->_demo($argv[0], 1,  3);
                break;
            case 2: $this->_demo(1, $argv[0], $argv[1]);
                break;
            default:
                $this->_demo(...$argv);
        }


    }

    protected function _demo($a, $b, $c){

        p($a, $b, $c);

    }

}


(new a())->demo(1);


//$sentinel = new RedisSent([['127.0.0.1', 6379, 2.5], ['127.0.0.1', 6380, 2.5]]);
//
//$redis = $sentinel->getMasterRedis('mymaster');
//
//$redis->set('name', '444');
//p($redis->get('name'));



/*
*  Final类通常会阻止扩展子类
*/
final class Parents {
    public $parentvar;
    public $name;

    function __construct() {
        $this->parentvar = get_class().'_value';
    }

    function parentfunc() {
        echo get_class().'类->parentfunc方法输出： '.$this->parentvar;
    }
}

//动态定义一个继承与ParentC的类 DynamicSon
$DefClass = new \Componere\Definition( 'DynamicSon', 'Parents');

$DefClass->addMethod( 'dynamicfunc', new \Componere\Method(function($parm = null){
    echo "dynamicfunc( $parm ) \n";
}));

// 注册
$DefClass->register();

//  实例化动态子类,并访问它自己的和继承的成员.
$dynamicSon = new DynamicSon;
$dynamicSon->parentfunc();//调用父类方法
$dynamicSon->dynamicfunc( 'haha');

//var_dump( $dynamicSon );



$a = Obj::std(['name' => 'tom', 'age']);
print_r($a);
