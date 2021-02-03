<?php

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

class RedisSent {

    protected array $sentinel = [];

    public function __construct($host, ?int $port = null, float $timeout = 0, ?string $persistent = null, int $retryInterval = 0, float $readTimeout = 0){

        if(is_array($host)){
            foreach($host as $item){
                $this->sentinel[] = new RedisSentinel(...$item);
            }
        }else{
            $this->sentinel[] = new RedisSentinel($host, $port, $timeout, $persistent, $retryInterval, $readTimeout);
        }

    }

    public function master(string $master) {

        foreach($this->sentinel as $key => $sentinel){

            try{
                return $sentinel->master($master);
            }catch(\RedisException $e){
                if(!isset($this->sentinel[$key+1])) throw $e;
                continue;
            }
        }

    }

}


$sentinel = new RedisSent([['127.0.0.1', 6379, 2.5], ['127.0.0.1', 6380, 2.5]]);

p($redis = $sentinel->master('mymaster'));

sleep(10);

p($redis = $sentinel->master('mymaster'));