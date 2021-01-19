<?php

$redis = new Redis();
$redis->connect('localhost', 6379);
$redis->auth('app123456'); //密码验证


function timeWindow($rule, $redis, $zkey){


    foreach ($rule as $key => $item){

        $score  = time();
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



while(1){

    timeWindow($rules, $redis, 'aaaa' . 1);
    sleep(1);
}


