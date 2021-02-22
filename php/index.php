<?php
//Swoole\Coroutine::create 等价于go函数
//Coroutine\Channel 可以简写为chan
//Swoole\Coroutine::defer可以直接用defer

/*
 * 题目描述：在有序数组中找出两个数，使它们的和为 target。
 * Input: numbers={2, 7, 11, 15}, target=9
 * Output: index1=1, index2=2
 * 使用双指针，一个指针指向值较小的元素，一个指针指向值较大的元素。指向较小元素的指针从头向尾遍历，指向较大元素的指针从尾向头遍历。
如果两个指针指向元素的和 sum == target，那么得到要求的结果；
如果 sum > target，移动较大的元素，使 sum 变小一些；
如果 sum < target，移动较小的元素，使 sum 变大一些。
数组中的元素最多遍历一次，时间复杂度为 O(N)。只使用了两个额外变量，空间复杂度为 O(1)。

*/
function twoSum($array, $target){

    $i = 0; $j = count($array) - 1;
    while($i < $j){
        $sum = $array[$i] + $array[$j];
        if($sum == $target){
            return [++$i, ++$j];
        }else if($sum < $target){
            $i++;
        }else{
            $j--;
        }
    }
    return null;
}

p(twoSum([2, 7, 11, 15], 9));

//Input: 5
//Output: True
//Explanation: 1 * 1 + 2 * 2 = 5
function judgeSquareSum(int $target){

    $i = 0;
    $j = (int) sqrt($target);
    $all = [];
    while($i <= $j){
        $powSum = pow($i, 2) + pow($j, 2);
        if($powSum == $target){
            $all[] = [$i, $j];
            $i++;
        }else if($powSum < $target){
            $i++;
        }else{
            $j--;
        }
    }
    return empty($all)? false : $all;

}


p(judgeSquareSum(65));


function hasCycle(SplDoublyLinkedList $linkedlist){
    $l1 = $linkedlist;
    $l2 = $linkedlist->next();
    while($l1 != null && $l2 != null && $l2->next() != null){
        if($l1 == $l2) return true;
        $l1 = $l1->next();
        $l2 = $l2->next()->next();//如果是闭环，则 $l2 追赶 $l1 总能赶上
    }
    return false;
}


stop();

function binarySearch($nums, $key) {

    $l = 0;
    $h = count($nums) - 1;
    while ($l <= $h) {
        $m = $l + intdiv(($h - $l), 2);
        if ($nums[$m] == $key) {
            return $m;
        } else if ($nums[$m] > $key) {
            $h = $m - 1;
        } else {
            $l = $m + 1;
        }
    }
    return -1;
}
p(PHP_FLOAT_MAX);
p(binarySearch([4,5,6,7,8,9], 8));

exit;

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->auth(['user' => 'phpredis', 'pass' => 'phpredis']);
$redis->swapdb(0, 1);
$redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);	  // Don't serialize data

$redis->ping(444);
$redis->echo(322);
p($redis->acl('USERS'));
$redis->bgRewriteAOF();
$redis->bgSave();
$redis->config('GET', '*max-*-entries*');
$redis->config('SET', 'dir', '/var/run/redis/dumps/');
$count = $redis->dbSize();
$redis->flushAll();
$redis->flushDb();
$redis->slaveOf('10.0.1.7', 6379);
// Will redirect, and actually make an SETEX call
$redis->set('key','value', 10);
// Will set the key, if it doesn't exist, with a ttl of 10 seconds
$redis->set('key', 'value', ['nx', 'ex'=>10]);

// Will set a key, if it does exist, with a ttl of 1000 milliseconds
$redis->set('key', 'value', ['xx', 'px'=>1000]);


$redis->setEx('key', 3600, 'value'); // sets key → value, with 1h TTL.
$redis->pSetEx('key', 100, 'value'); // sets key → value, with 0.1 sec TTL.

$redis->setNx('key', 'value'); /* return TRUE */
$redis->setNx('key', 'value'); /* return FALSE */
$redis->del(['key3', 'key4']); /* return 2 */
$redis->unlink(['key1', 'key2']);

$redis->exists('NonExistingKey'); /* 0 */


$redis->mset(['foo' => 'foo', 'bar' => 'bar', 'baz' => 'baz']);
$redis->exists(['foo', 'bar', 'baz']); /* 3 */
$redis->exists('foo', 'bar', 'baz'); /* 3 */

$redis->incr('key1', 10);   /* 14 */

$redis->incrBy('key1', 10); /* 24 */
$redis->mGet(['key1', 'key2', 'key3']);
$redis->getSet('x', 'lol');
$key = $redis->randomKey();
$redis->select(0);

$redis->set('x', '42');	// write 42 to x
$redis->move('x', 1);
$redis->rename('x', 'y');
$redis->expire('x', 3);
$redis->expireAt('x', time() + 3);
$redis->keys('user*');
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


// Without enabling Redis::SCAN_RETRY (default condition)
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


//$redis = new Redis();
//$redis->connect('172.18.0.2', 6379);
//$redis->set('name', 'tom');
//echo $redis->get('name');
$obj_cluster = new RedisCluster(NULL, array('172.18.0.2:6379', '172.18.0.3:6379'), 1.5, 1.5, true);
$obj_cluster->multi();
$obj_cluster->get('mykey1');
$obj_cluster->set('mykey1', 'new_value');
$obj_cluster->get('myotherkey1');

print_r($obj_cluster->exec());

$obj_cluster->set('{hash1}key1', 111);
$obj_cluster->set('otherkey', 444);
//p($obj_cluster->mget(['{hash1}key1',  'otherkey']));

foreach($obj_cluster->_masters() as $arr_master){

    $a = $obj_cluster->echo($arr_master, 'Hello: ' . implode(':', $arr_master));
    p($a);
}

p($obj_cluster->echo('mykey', 'Hello World!'));

$sentinel = new RedisSentinel('127.0.0.1', 6379, 2.5);

$redis = $sentinel->master('mymaster');
print_r($redis);


$clean = str_replace(chr(0), '', $input);

$filepath = "$homedir/$userfile";
if(!ctype_alnum($username) || !preg_match('/^(?:[a-z0-9_-]|\.(?!\.))+$/iD', $userfile)){
    die('Bad username/filename');
}


$gen = (function () {
    try {
        yield 1;
    } catch (Exception $e) {
        echo $e->getMessage();
    }
})();

$gen->throw(new Exception('gen throw exception'));



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

$map = new \Ds\Map(['a' => 1, 'b' => 2, 'c' => 3]);
var_dump($map);