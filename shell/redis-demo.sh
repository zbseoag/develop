#!/usr/bin/env bash
:<<EOF
Redis 测试脚本
测试之前,先关闭所有 Redis 服务器:
sudo pkill -9 redis-server

关闭单台服务器
redis-cli -p <port> -a <requirepass>  shutdown

用法:
. ./demo-redis.sh
-connect

EOF



function shutdowns(){

    sleep 5
    for port in "$@"
    do
        redis-cli -p $port shutdown
    done

}





### ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
### 连接测试
### ========================================================================
### 
### ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function -connect(){

redis-server --port 6380 --daemonize yes
redis-cli -p 6380 --raw <<EOF

echo "测试开始..."
set uname "张三"
get uname
shutdown
quit

EOF

}


### ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
### 客户端执行一些命令
### ========================================================================
### 
### ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function -client(){

#启动服务器
redis-server --port 6380 --requirepass 123456 --daemonize yes

#连接1
redis-cli -p 6380 <<EOF
info
auth 123456
time
client setname "client-1"
client getname
EOF

#连接2
redis-cli -p 6380 <<EOF
auth 123456
client setname "client-2"
client list
client kill 127.0.0.1:45888
EOF

shutdowns 6380

}



### ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
### 主从复制
### ========================================================================
### 主服务器中写入数据,再从从服务器上读取数据
### ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function -copy(){

#启动主服务器
redis-server --port 6380 --daemonize yes
#启动从服务器
redis-server --port 6381 --replicaof 127.0.0.1 6380 --daemonize yes

#连接主服务器
redis-cli -p 6380 <<EOF

role
set uname "Tom"
get uname
quit

EOF

#连接从服务器
redis-cli -p 6381 <<EOF

role
get uname
replicaof no one

EOF

#关闭服务器
shutdowns 6380 6381


}


function -transaction(){

redis-server --port 6380 --daemonize yes

redis-cli -p 6380 --raw <<EOF
echo "设置并监视 books:"
echo "==================================================================================="
set books 1
watch books
echo "==================================================================================="

echo "调用 incr books 修改了 books 的值:"
echo "==================================================================================="
incr books
echo "==================================================================================="

echo "开始事务:"
echo "==================================================================================="
multi
set books 10
incr books
exec
get books
echo "==================================================================================="

echo "第二次测试 watch 之后, 不改变 books 的值:"
echo "==================================================================================="
watch books
incr books
UNWATCH books
watch books

multi
set books 10
incr books
exec

DISCARD
EOF

#关闭服务器
shutdowns 6380

}



function -string(){

echo <<EOF
expire key seconds
exists key
ttl key
set key value [ex seconds | px milliseconds] [nx|xx]

setnx key value
setex key seconds value
psetex key milliseconds value

getset key value
strlen key
append key value

setrange key offset value
getrange key start end
incr key
incrby key increment
incrbyfloat key increment
decr key
decrby key decrement

mset key value [key value …]
msetnx key value [key value …]
mget key [key …]

EOF

}


function -hash(){
redis-server --port 6380 --daemonize yes
redis-cli -p 6380 --raw <<EOF

HSET
HSETNX
HGET
HEXISTS
HDEL
HLEN
HSTRLEN
HINCRBY
HINCRBYFLOAT
HMSET
HMGET
HKEYS
HVALS
HGETALL
HSCAN

EOF

#关闭服务器
shutdowns 6380

}


function -list(){

redis-server --port 6380 --daemonize yes
redis-cli -p 6380 --raw <<EOF

LPUSH
LPUSHX
RPUSH
RPUSHX
LPOP
RPOP
RPOPLPUSH
LREM
LLEN
LINDEX
LINSERT
LSET
LRANGE
LTRIM
BLPOP
BRPOP
BRPOPLPUSH

EOF

#关闭服务器
shutdowns 6380

}


function -set(){

redis-server --port 6380 --daemonize yes
redis-cli -p 6380 --raw <<EOF
SADD
SISMEMBER
SPOP
SRANDMEMBER
SREM
SMOVE
SCARD
SMEMBERS
SSCAN
SINTER
SINTERSTORE
SUNION
SUNIONSTORE
SDIFF
SDIFFSTORE

EOF

#关闭服务器
shutdowns 6380

}


function -zset(){

redis-server --port 6380 --daemonize yes
redis-cli -p 6380 --raw <<EOF
ZADD
ZSCORE
ZINCRBY
ZCARD
ZCOUNT
ZRANGE
ZREVRANGE
ZRANGEBYSCORE
ZREVRANGEBYSCORE
ZRANK
ZREVRANK
ZREM
ZREMRANGEBYRANK
ZREMRANGEBYSCORE
ZRANGEBYLEX
ZLEXCOUNT
ZREMRANGEBYLEX
ZSCAN
ZUNIONSTORE
ZINTERSTORE

EOF

#关闭服务器
shutdowns 6380

}


function -geo(){

redis-server --port 6380 --daemonize yes
redis-cli -p 6380 --raw <<EOF
GEOADD
GEOPOS
GEODIST
GEORADIUS
GEORADIUSBYMEMBER
GEOHASH

EOF

#关闭服务器
shutdowns 6380

}


function -bitmap(){

redis-server --port 6380 --daemonize yes
redis-cli -p 6380 --raw <<EOF
SETBIT
GETBIT
BITCOUNT
BITPOS
BITOP
BITFIELD

EOF

#关闭服务器
shutdowns 6380

}


function -database(){

redis-server --port 6380 --daemonize yes
redis-cli -p 6380 --raw <<EOF


EOF

#关闭服务器
shutdowns 6380

}



function -lua(){

# SCRIPT FLUSH ：清除所有脚本缓存
# SCRIPT EXISTS sha1 [sha1 …] ：根据给定的脚本校验和，检查指定的脚本是否存在于脚本缓存
# SCRIPT LOAD script ：将一个脚本装入脚本缓存，但并不立即运行它
# SCRIPT KILL ：杀死当前正在运行的脚本

redis-server --port 6380 --daemonize yes
redis-cli -p 6380 --raw <<EOF
eval "return {KEYS[1],KEYS[2],ARGV[1],ARGV[2]}" 2 key1 key2 argvs1 argvs2
eval "return redis.call('set','foo','bar')" 0
eval "return redis.call('set',KEYS[1],'bar')" 1 foo

eval "return {1,2,{3,'Hello World!'}}" 0
eval "return redis.call('get','foo')" 0

eval "return redis.pcall('get', 'foo')" 0
script load "return 'hello moto'"
evalsha "232fd51614574cf0867b83d384a5e898cfd24e5a" 0
script exists 232fd51614574cf0867b83d384a5e898cfd24e5a
script flush 
script kill

EOF

#关闭服务器
shutdowns 6380

}


function -redisBloom(){

redis-server --port 6380 --daemonize yes
redis-cli -p 6380 --raw <<EOF
bf.add user aaa
bf.add user bbb
bf.exists user aaa
bf.madd user ccc ddd eee fff
bf.mexists user aaa ccc eee zzzz

EOF
#关闭服务器
shutdowns 6380

}


function -tmp(){

redis-server --port 6380 --daemonize yes
redis-cli -p 6380 --raw <<EOF
SAVE
BGSAVE
BGREWRITEAOF
LASTSAVE

EOF

#关闭服务器
shutdowns 6380

}


function -config(){

redis-server --port 6380 --daemonize yes
redis-cli -p 6380 --raw <<EOF
CONFIG SET
CONFIG GET

CONFIG RESETSTAT

EOF

#关闭服务器
shutdowns 6380

}

function -debug(){

redis-server --port 6380 --daemonize yes
redis-cli -p 6380 --raw <<EOF
PING
ECHO
OBJECT
SLOWLOG
MONITOR
DEBUG OBJECT
DEBUG SEGFAULT

EOF

#关闭服务器
shutdowns 6380

}


function -inside(){

redis-server --port 6380 --daemonize yes
redis-cli -p 6380 --raw <<EOF
MIGRATE
DUMP
RESTORE
SYNC
PSYNC

EOF

#关闭服务器
shutdowns 6380

}



function -test(){

redis-server --port 6380 --daemonize yes
redis-cli -p 6380 --raw <<EOF


EOF

#关闭服务器
shutdowns 6380

}



function -ttl(){

redis-server --port 6380 --daemonize yes
redis-cli -p 6380 --raw <<EOF
EXPIRE
EXPIREAT
TTL
PERSIST
PEXPIRE
PEXPIREAT
PTTL

EOF

#关闭服务器
shutdowns 6380

}



function -hyperLogLog(){

redis-server --port 6380 --daemonize yes
redis-cli -p 6380 --raw <<EOF
pfadd nosql "redis" "mongodb" "memcached"
pfadd rdbms "mysql" "mssql" "postgresql" "sqlserver"

pfmerge all nosql rdbms

pfcount  nosql
pfcount  rdbms
pfcount  all

EOF

#关闭服务器
shutdowns 6380



}


function -stream(){
:<<EOF
消息队列相关命令：

XADD key ID field value [field value ...] 添加一条消息到队列
xadd channel1 * msg1-tony "Hello everyone."


XREAD [COUNT count] [BLOCK milliseconds] STREAMS key [key ...] id [id ...] 以阻塞或非阻塞方式获取消息列表
xread BLOCK 100 STREAMS channel1 $ #从最后一条开始
xread BLOCK 100 STREAMS channel1 1528703048021-0 #读取一条消息

XRANGE key start end [COUNT count] 获取消息列表，会自动过滤已经删除的消息
xrange channel1 - + #第一条到最后一条
xrange channel1 1528703048021-0 + #读取从某一条消息之后的所有消息



XTRIM - 对流进行修剪，限制长度
XDEL key ID [ID ...] 删除消息
XLEN key 获取消息条数

XREVRANGE key end start [COUNT count] 反向获取消息列表，ID 从大到小


消费者组相关命令：
XGROUP CREATE - 创建消费者组
XREADGROUP GROUP - 读取消费者组中的消息
XACK - 将消息标记为"已处理"
XGROUP SETID - 为消费者组设置新的最后递送消息ID
XGROUP DELCONSUMER - 删除消费者
XGROUP DESTROY - 删除消费者组
XPENDING - 显示待处理消息的相关信息
XCLAIM - 转移消息的归属权
XINFO - 查看流和消费者组的相关信息；
XINFO GROUPS - 打印消费者组的信息；
XINFO STREAM - 打印流信息


XADD mystream * name Sara surname OConnor
XADD mystream * field1 value1 field2 value2 field3 value3

XLEN mystream
XRANGE mystream - +


创建并添加消息
xadd channel1 * create-channel null
xadd channel1 * msg1-tony "Hello everyone."
xadd channel1 * msg2-tony "I am a big Redis fan." msg3-tony "Hope we can learn from each other.:-)"

读取
xread BLOCK 100 STREAMS channel1 $
xread BLOCK 100 STREAMS channel1 1528703048021-0

xrange channel1 1528703061087-0 +
xrange channel1 - +



XADD memberMessage * user kang msg Hello
XADD memberMessage * user zhong  msg nihao
XREAD streams memberMessage 0 #第一条开始读取 在阻塞模式下 '$' 表示最新的消息


消息ID由"时间戳-序号"组成


MULTI
XADD mq * msg 1 
XADD mq * msg 2
XADD mq * msg 3
XADD mq * msg 4
XADD mq * msg 5
EXEC

# 创建消费组 mqGroup
XGROUP CREATE mq mqGroup 0 # 为消息队列 mq 创建消费组 mgGroup
参数0，表示从第一条消息开始消费。除了支持CREATE外，还支持SETID设置起始ID，DESTROY销毁组，DELCONSUMER删除组内消费者等操作


# 消费者A，消费第1条
XREADGROUP group mqGroup consumerA count 1 streams mq > #消费组内消费者A，从消息队列mq中读取一个消息
参数 > 表示未被组内消费的起始消息，参数count 1表示获取一条


# 消费者A，消费第2条
127.0.0.1:6379> XREADGROUP GROUP mqGroup consumerA COUNT 1 STREAMS mq > 

# 消费者B，消费第3条
127.0.0.1:6379> XREADGROUP GROUP mqGroup consumerB COUNT 1 STREAMS mq > 

# 消费者A，消费第4条
127.0.0.1:6379> XREADGROUP GROUP mqGroup consumerA count 1 STREAMS mq > 

# 消费者C，消费第5条
127.0.0.1:6379> XREADGROUP GROUP mqGroup consumerC COUNT 1 STREAMS mq > 

# 查看队列中的等待列表
XPENDING mq mqGroup 
1) (integer) 5 # 5个已读取但未处理的消息
2) "1553585533795-0" # 起始ID
3) "1553585533795-4" # 结束ID
4) 1) 1) "consumerA" # 消费者A有3个
      2) "3"
   2) 1) "consumerB" # 消费者B有1个
      2) "1"
   3) 1) "consumerC" # 消费者C有1个
      2) "1"


XPENDING mq mqGroup - + 10 # 使用 start end count 选项可以获取详细信息
1) 1) "1553585533795-0" # 消息ID
   2) "consumerA" # 消费者
   3) (integer) 1654355 # 已读取时长(IDLE) 即从读取到现在经历了1654355ms
   4) (integer) 5 # 消息被读取了5次，delivery counter
2) 1) "1553585533795-1"
   2) "consumerA"
   3) (integer) 1654355
   4) (integer) 4

XPENDING mq mqGroup - + 10 consumerA #获某个消费者的Pending列表


XACK mq mqGroup 1553585533795-0 # 通知某条消息处理完成


# 转移超过3600s的消息1553585533795-1到消费者B的Pending列表
XCLAIM mq mqGroup consumerB 3600000 1553585533795-1


# 消息1553585533795-1已经转移到消费者B的Pending中。
127.0.0.1:6379> XPENDING mq mqGroup - + 10
1) 1) "1553585533795-1"
   2) "consumerB"
   3) (integer) 84404 # 注意IDLE，被重置了
   4) (integer) 5 # 注意，读取次数也累加了1次


delivery counter 太多的消息,我们可以定义为死信,然后就只能删除,分两步.
# 删除队列中的消息
XDEL mq 1553585533795-1
# 通知该条消息处理完成
XACK mq mqGroup 1553585533795-1


Xinfo stream mq #查看队列信息
Xinfo groups mq #消费组信息
XINFO CONSUMERS mq mqGroup #消费者组成员信息



EOF




}

SAVE
BGSAVE
BGREWRITEAOF


save 60 1000
appendonly yes

为现有的 AOF 文件创建一个备份。
使用 Redis 附带的 redis-check-aof 程序，对原来的 AOF 文件进行修复
redis-check-aof --fix


怎么从 RDB 持久化切换到 AOF 持久化?
为dump.rdb 创建备份
执行以下两条命令：
CONFIG SET appendonly yes
CONFIG SET save ""