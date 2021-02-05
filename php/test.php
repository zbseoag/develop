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






$a = new SplDoublyLinkedList;
$arr = [1, 2, 3, 4, 5, 6, 7, 8, 9];
for($i = 0; $i < count($arr); $i++){
    $a->add($i, $arr[$i]);
}

$a->push(11); //push method
$a->add(10, 12); //add method must with index
$a->shift(); //remove array first value
$a->unshift(1); //add first value

$a->rewind(); //initial from first

echo 'SplDoublyLinkedList array last/top value ' . $a->top() . " \n";
echo 'SplDoublyLinkedList array count value ' . $a->count() . " \n";
echo 'SplDoublyLinkedList array first/top value ' . $a->bottom() . " \n\n";

while($a->valid()){ //check with valid method
    echo 'key ', $a->key(), ' value ', $a->current(), "\n"; //key and current method use here
    $a->next(); //next method use here
}

$a->pop(); //remove array last value
print_r($a);
$s = $a->serialize();
echo $s;