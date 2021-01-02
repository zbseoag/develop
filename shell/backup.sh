#!/usr/bin/env bash


# local cmd=""
# until [ "$cmd" == 'exit' ]
# do
#     read -p ":" cmd
#     [ "$cmd" == 'exit' ] && break
#     docker-helper $cmd

# done






comment(){

for file in $(ls); do
    echo $file
done


echo -n "Enter password :"
read password
while [ "$password" != "root" ]; do
    echo -n "Sorry, please try again :"
    read password
done    
    
echo "Wellcome!"




until who | grep "$1" > /dev/null; do
    sleep 10
done

echo -e '\a'
echo "*** $1 has just logged in ***"


a=abd
case "$a" in
    [Yy]|[Yy][Ee][Ss] ) echo "good";;
    [nN]* ) echo "bad";;
    * ) echo "error"
esac




text="global variable"
foo(){
    local text="local value"
    echo $text
}

echo $text
foo
echo $text



yes_or_no(){

    echo "你名字是 $* ?"
    while true
    do
        echo -n "输入yes 或者 no :"
        read x
        case "$x" in
            y|yes ) return 0;;
            n|no ) return 1;;
            *) echo "回答yes或no"
        esac
    done
}


echo "原始参数: $*"
if yes_or_no "$1"
then
    echo "你好 $1"
else
    echo "从不在意"
fi


foo=10
x=foo
eval y='$'$x


[ -f .profile ] && exit 0 || exit 1



foo="第一个变量"
bar="第二个变量"
export bar

./test1.sh

#test1.sh内容如下:
#!/bin/bash

echo "$foo"
echo "$bar"

x=10
x=`expr $x + 1`
x=$(expr $x + 1)
echo $x

printf "\a项目:%s \n进度:%d%% \n" "my project" 30 


echo 今天: `date`
set $(date)
echo 月份: $2



echo "$@"
while [ "$1" != "" ]
do
    shift
done

echo "整理之后: $@"

}


trap 'rm -f /tmp/my_tmp_file_$$' INT
echo createing file /tmp/my_tmp_file_$$
date > /tmp/my_tmp_file_$$

echo 'press interrupt (ctrl -c) to interrupt'

while [ -f /tmp/my_tmp_file_$$ ]
do
    echo file exists
    sleep 1
done

echo file no longer exists



trap INT
echo createing file /tmp/my_tmp_file_$$
date > /tmp/my_tmp_file_$$
echo press interrup control-c to interupt
while [ -f /tmp/tmp/my_tmp_file_$$ ]
do
    echo file exists
    sleep 1
done

echo we never get here

read -p "command:" command option

OLD_IFS=$IFS
IFS=" "
args=($option)
IFS=$OLD_IFS
option=${args[0]}


case $command in


    out) 

        if [ $option == '?' ];then
            echo 'out -file -pwd'
            read -p "out:" file pwd
        else
            file=${args[0]}; pwd=${args[1]};
        fi
        echo $pwd | sudo -S cp $file /media/sf_Downloads/
    ;;

    cp)
        if [ $option == '?' ];then
            echo 'cp -file -path -pwd'
            read -p "cp:" file path pwd
        else
            file=${args[0]}; path=${args[1]}; pwd=${args[2]};
        fi
        echo $pwd | sudo -S cp /media/sf_Downloads/$file $path
    ;;


    # update python version
    python) 

        if [ $option == '?' ];then
            echo 'upython -version -pwd'
            read -p "upython:" version pwd
        else
            version=${args[0]}; pwd=${args[1]};
        fi
        pythonup $version $pwd
    ;;

    install)
        if [ $option == '?' ];then
            echo 'install -package -directory'
            read -p "install:" package directory
        else
        
            package=${args[0]}; directory=${args[1]};
        fi
        install  $package $directory
    ;;

    test)
        #install '/home/zbseoag/Downloads/nginx-1.11.2.tar.gz' '/home/zbseoag/Documents/demo'
    ;;
    
esac





#!/bin/bash

menu_choice=""
current_cd=""
title_file="title.cdb"
tracks_file="tracks.cdb"
temp_file=/tmp/cdb.$$

trap 'rm -f $temp_file' EXIT


get_return(){
    echo -n "按一下,返回"
    read x
    return 0
}

get_confirm(){

    echo -n "你确定?"
    while true
    do
        read x
        case "$x" in
            y|yes   )
                return 0;;
            n|no    ) 
                echo "已取消"
                return 1
            *       )
                echo "请输入yes|no";;
        esac
    done
    
}


set_menu_choice(){
    clear
    echo "选项 :-"
    echo
    echo "  a) 添加"
    echo "  f) 查找"
    echo "  c) 计数"
    if [ "$cdcatnum" != "" ]
    then
        echo "  l) 列出 $cdtitle 的分类"
        echo "  r) 删除 $cdtitle"
        echo "  u) 更新 $cdtitle 的信息"
    fi
    echo "  q) 退出"
    echo
    echo -n "请选择"
    read menu_choice
    return
    
}


insert_title(){
    echo $* >> $title_file
    return
    
}


insert_track(){
    echo $* >> $tracks_file
    return

}

add_record_tracks(){
    
    echo "输入这张CD的 track 信息"
    echo "输入完成后,按q退出"
    cdtrack=1
    cdtitle=""
    
    while [ "$cdtitle" != q ];do
    
        echo -n "Track $cdtrack, track title?"
        read tmp
        cdttitle=${tmp%%, *}
        if [ "$tmp" != "$cdtitle" ];then
            echo "sorry, no commas allowd"
            continue
        fi
        if [ -n "$cdttitle" ];then
            if [ "$cdttitle" != "q" ];then
               insert_track $cdcatnum, $cdtrack, $cdttitle 
            fi
        else
            cdtrack=$((cdtrack-1))
        fi
        cdtrack=$((cdtrack+1))
    done     

}

add_records(){
    
    echo -n "enter catalog name"
    read tmp
    cdcatnum=${tmp%%,*}


}





if [ "$1" == "--help" ];then
    echo "run-c \$file"
    exit 0;
fi

if [ "$1" == "" ];then
    file="test.c"
else
    file=$1".c"
fi

echo "当前运行: $file";

gcc $file -o /tmp/a.out && /tmp/a.out



#输出环境变量
show(){

    case "$arguemnt" in
        'path' ) echo $PATH | awk -F ':' '{for(i=1;i<=NF;i++){print "  " $i;}}';;
        * ) echo "do nothing!"
    esac
}



#windows换行转linux换行
trim-rn(){
    sudo sed -i 's/\r$//' $arguemnt
}




function cover(){

    set -- $(getopt -u -o rh --long rm,help -- $@)
    #echo $@   
    local rm=false
    for i in "$@"
    do
        case "$1" in
        -r|--rm)    rm=true;;
        -h|--help)
echo "cover [option] <source> [target]
功能：用一个 source 文件覆盖 target 文件，如果省略 target，则根据 source.bak 自动推导
用法：
    cover source.conf target.conf
    cover config.conf.bak 
选项:
    -r, --rm    删除源文件
    -h, --help  帮助"; return 0
        ;;
        --)         shift 1; break;;
        *)          echo "无效选项：$1"; return 0 ;;
        esac
        shift 1

    done

    local source=$1
    local target=$2
    [ -z "$target" ] && target="${source%.bak}"
    sudo bash -c "cat $source > $target"

    [ $rm == 'true' ] && { [ "$?" -eq 0 ] && sudo rm $source; }

}



function mk.file(){

    eval set -- `getopt -o t:e:r:dh -l help -- "$@"`

    local _tpl=''
    local _ext='conf'
    local _run=''
    local _dir=false

    for i in "$@"
    do
        case "$1" in
        -t)  _tpl="$2"; shift 1;; #处理了带值参数,所以多跳一下.
        -e)  _ext="$2"; shift 1;;
        -r)  _run="$2"; shift 1;;
        -d)  _dir=true;;

        -h|--help)
echo "mk.file [option] port1 [port2 ...]
功能：根据模板批量创建配置文件, 模板文件中的 {port} 会被替换
用法：
    mk.file -t cluster.tpl 32768 32769 32770
    mk.file -t cluster.tpl -r "redis-serer :file --daemonize yes" 32768 32769 32770

选项:
    -t          模板文件
    -e          文件扩展名如 ini conf
    -r          运行的命令
    -d          是否生成二级目录
    -h, --help  帮助"; return 0
;;

        --)    shift 1; break;;#终止时,最后跳一下
         *)    echo "无效选项：$1"; return 0 ;;
        esac
        shift 1 #每次处理完一个参数,跳一下
    done

    local file=''
    local command=''
    for port in "$@"
    do

        if [ $_dir == 'true' ];then
            mkdir -p $port
            file="$port/$port.$_ext"
        else
            file="$port.$_ext"
        fi

        cat "$_tpl" > $file
        sed -i -e "s/{port}/$port/" $file
      
        if [ -n "$_run" ];then
            command=${_run/':file'/$file};
            echo "$command";
            eval "$command"
        else
            echo $file
        fi 
   
    done

}




git log -p -2
git log --stat
git log --pretty=oneline #short，full,fuller
    
git log --pretty=format 常用的选项
# 选项	说明
# %H
# 提交的完整哈希值

# %h
# 提交的简写哈希值
# %T
# 树的完整哈希值
# %t
# 树的简写哈希值
# %P
# 父提交的完整哈希值
# %p
# 父提交的简写哈希值
# %an
# 作者名字
# %ae

# 作者的电子邮件地址

# %ad

# 作者修订日期（可以用 --date=选项 来定制格式）

# %ar

# 作者修订日期，按多久以前的方式显示

# %cn

# 提交者的名字

# %ce

# 提交者的电子邮件地址

# %cd

# 提交日期

# %cr

# 提交日期（距今多长时间）

# %s

# 提交说明

    git log --pretty=format:"%h - %an, %ar : %s"
    git log --pretty=format:"%h %s" --graph


#修正前的次的提交
git commit --amend
#取消暂存 CONTRIBUTING.md 文件
git reset HEAD CONTRIBUTING.md
#撤消修改
git checkout -- CONTRIBUTING.md

git remote -v

git remote add pb https://github.com/paulboone/ticgit
git remote -v
git fetch pb
git push origin master
git remote show origin
git remote rename pb paul
git remote remove paul
git tag --list

git config --list --show-origin


git config --global alias.ci commit
git config --global alias.unstage 'reset HEAD --'
git config --global alias.last 'log -1 HEAD'

git config --global alias.visual '!gitk'


    git diff --staged
    git diff --cached
    git difftool --tool-help

git branch
git branch --merged | --no-merged #没有给定提交或分支名作为参数时， 分别列出已合并或未合并到 当前 分支的分支



远程仓库名字 “origin” 在 Git 中并没有任何特别的含义一样。 
当你运行 git init 时默认的起始分支名字，原因仅仅是它的广泛使用， “origin” 是当你运行 git clone 时默认的远程仓库名字。
如果你运行 git clone -o booyah，那么你默认的远程分支名字将会是 booyah/master。



git ls-remote origin
git remote show origin
git remote add
git fetch teamone
git push origin serverfix
git push origin serverfix:awesomebranch #来将本地的 serverfix 分支推送到远程仓库上的 awesomebranch 分支
git config --global credential.helper cache #避免每次输入密码

git merge origin/serverfix #将远程分支合并入当前分支
git checkout -b serverfix origin/serverfix #将远程分支合并到本地新建分支

git checkout -b <branch> <remote>/<branch>
git checkout --track origin/serverfix #相当于 git checkout -b serverfix origin/serverfix

git checkout serverfix #如果本地没有该分支，就去拉取线上分支


git branch --set-upstream-to|-u  origin/serverfix #设置当前本地分支跟踪远程分支

当设置好跟踪分支后，可以通过简写 @{upstream} 或 @{u} 来引用它的上游分支。 
所以在 master 分支时并且它正在跟踪 origin/master 时，
如果愿意的话可以使用 git merge @{u} 来取代 git merge origin/master

变基过程
git checkout experiment
git rebase master #变基
git checkout master #切换到 master
git merge experiment #合并
git branch -d client

git rebase master server #使用 server 分支变基


取出 client 分支，找出它从 server 分支分歧之后的补丁， 然后把补丁在 master 分支上重放一遍，让 client 看起来像直接基于 master 修改一样
git rebase --onto master server client 


git branch -vv #查看设置的所有跟踪分支
git fetch --all #抓取所有的远程仓库

git push origin --delete serverfix


1、创建分支开发新需求
git checkout -b iss53 #创建并切换 iss53 分支

2、接到线上紧急 bug 要处理。在切换工作之前，要留意你的工作目录和暂存区里那些还没有被提交的修改。 在你切换分支之前，保持好一个干净的状态。 
git checkout master #切换加 master分支
git checkout -b hotfix #创建并切换 hotfix 分支
3、线上bug处理完成，并布署
git checkout master #切换回主分支
git merge hotfix #将hotfix 分支合并到主分支
git branch -d hotfix #删除 hotfix 分支

4、继续之前的需求开发
git checkout iss53 #切换回 iss53 分支
git merge master #将 msater 分支合并到当前分支，当然也可以等 iss53 分支功能开发完成合并入 msater 分支

5、需求开发完成
git checkout master #切换回 master 分支
git merge iss53 #将 iss53 分支合并到当前分支
git branch -d iss53 #删除 iss53 分支

6、如果合并遇到冲突
git status #找到冲突文件
git mergetool #用外部合并工具



function __command_lists(){

docker image ls --filter="reference=:latest"
docker search --filter="is-official=true" alpine
docker search --filter="is-automated=true" alpine
docker image inspect ubuntu:20.10

docker image ls --digests ubuntu:20.10

docker image history ubuntu:20.10
docker run --restart=unless-stopped -d ubuntu:20.10

docker network ls
docker network inspect bridge
ip link show docker0
docker network inspect bridge | grep bridge.name
docker network create -d bridge localnet

brctl show
docker run -d --name=c1 --network localnet ubuntu:20.10 sleep 1d
docker port c1
docker network inspect localnet --format '{{json .Containers}}'
docker run -it --name=c2 --network localnet ubuntu:20.10 bash
docker.container.init c2
ping c1


# sudo dpkg --get-selections | awk '/i386/{print $1}'
# sudo apt-get remove --purge `dpkg --get-selections | awk '/i386/{print $1}'`
# sudo dpkg --remove-architecture i386	  //移除i386架构
# sudo dpkg --print-foreign-architectures //显示已启用的异质体系结构

#xrandr
#cvt 1920x1080
#xrandr --newmode "1920x1080_60.00"  173.00  1920 2048 2248 2576  1080 1083 1088 1120 -hsync +vsync
#xrandr --addmode HDMI-1 "1920x1080_60.00"

}



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