<?php
//Swoole\Coroutine::create 等价于go函数
//Coroutine\Channel 可以简写为chan
//Swoole\Coroutine::defer可以直接用defer

//$redis = new Redis();
//$redis->connect('127.0.0.1', 6379);
//$redis->auth(['user' => 'phpredis', 'pass' => 'phpredis]);
//$redis->swapdb(0, 1);
//$redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);	  // Don't serialize data

//$redis->ping(444);
//$redis->echo(322);
//p($redis->acl('USERS'));
//$redis->bgRewriteAOF();
//$redis->bgSave();
//$redis->config('GET', '*max-*-entries*');
//$redis->config('SET', 'dir', '/var/run/redis/dumps/');
//$count = $redis->dbSize();
//$redis->flushAll();
//$redis->flushDb();
//$redis->slaveOf('10.0.1.7', 6379);
//// Will redirect, and actually make an SETEX call
//$redis->set('key','value', 10);
//// Will set the key, if it doesn't exist, with a ttl of 10 seconds
//$redis->set('key', 'value', ['nx', 'ex'=>10]);
//
//// Will set a key, if it does exist, with a ttl of 1000 milliseconds
//$redis->set('key', 'value', ['xx', 'px'=>1000]);
//
//
//$redis->setEx('key', 3600, 'value'); // sets key → value, with 1h TTL.
//$redis->pSetEx('key', 100, 'value'); // sets key → value, with 0.1 sec TTL.
//
//$redis->setNx('key', 'value'); /* return TRUE */
//$redis->setNx('key', 'value'); /* return FALSE */
//$redis->del(['key3', 'key4']); /* return 2 */
//$redis->unlink(['key1', 'key2']);
//
//$redis->exists('NonExistingKey'); /* 0 */
//
//
//$redis->mset(['foo' => 'foo', 'bar' => 'bar', 'baz' => 'baz']);
//$redis->exists(['foo', 'bar', 'baz']); /* 3 */
//$redis->exists('foo', 'bar', 'baz'); /* 3 */
//
//$redis->incr('key1', 10);   /* 14 */
//
//$redis->incrBy('key1', 10); /* 24 */
//$redis->mGet(['key1', 'key2', 'key3']);
//$redis->getSet('x', 'lol');
//$key = $redis->randomKey();
//$redis->select(0);
//
//$redis->set('x', '42');	// write 42 to x
//$redis->move('x', 1);
//$redis->rename('x', 'y');
//$redis->expire('x', 3);
//$redis->expireAt('x', time() + 3);
//$redis->keys('user*');
$redis->type('key');
$redis->set('a', 22222);

$redis->set('key', 'string value');
$redis->getRange('key', 0, 5); /* 'string' */
$redis->getRange('key', -5, -1); /* 'value' */

$redis->set('key', 'Hello world');
$redis->setRange('key', 6, 'redis'); /* returns 11 */
$redis->get('key'); /* "Hello redis" */
$redis->strlen('key'); /* 5 */

$redis->set('key', "\x7f"); // this is 0111 1111
$redis->getBit('key', 0); /* 0 */
$redis->getBit('key', 1); /* 1 */



/* Without enabling Redis::SCAN_RETRY (default condition) */
$it = NULL;
do {
    $arr_keys = $redis->scan($it);
    if ($arr_keys !== FALSE) {
        foreach($arr_keys as $str_key) {
            echo "Herekey: $str_key\n";
        }
    }
} while ($it > 0);


$redis->setOption(Redis::OPT_SCAN, Redis::SCAN_RETRY);
$it = NULL;
while ($arr_keys = $redis->scan($it)) {
    foreach ($arr_keys as $str_key) {
        echo "key: $str_key\n";
    }
}


$obj_cluster = new RedisCluster();