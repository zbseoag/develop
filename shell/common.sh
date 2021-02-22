#!/usr/bin/env bash

#history 配置
export HISTTIMEFORMAT='%F ' #时间格式化
export HISTSIZE=100 #命令历史列表总条数
export HISTFILESIZE=10000 #命令历史文件总条数
export HISTCONTROL="ignoredups" #ignoredups：忽略重复命令； ignorespace：忽略空白开头的命令  ignoreboth
export HISTIGNORE="pwd:history" #不记录的命令

export JAVA_HOME="/d/usr/jdk"
export PATH=$PATH:/d/usr/jdk/bin:/d/usr/elasticsearch/bin:/d/usr/kibana/bin:/d/usr/zookeeper/bin

alias ll="ls -alF"
alias wk="cd -"
alias up="cd .."
alias python="python3.8"
alias pip="pip3"
alias tar.src="tar -C /d/src -xvf"
alias tar.usr="tar -C /d/usr -xvf"
alias make="make -j $(nproc)"
alias update="sudo apt update && apt list --upgradable"
alias remove="sudo apt purge"
alias upgrade="sudo apt upgrade -y"
alias remove="sudo apt remove"
alias close.nautilus="killall nautilus"
alias apt.fix="sudo apt-get install -f "
alias all.users="cat /etc/passwd |cut -f 1 -d:"
alias port="netstat -ap | grep"
alias real="realpath -s"
alias ecarg='echo "(数量 $#):"; for item in "$@";do echo "$item,";done '
alias rearg='ecarg;return'
alias eclist='echo "============参数列表==============";local index=1;for arg in "$@";do echo "$index: $arg";let index+=1; done ;echo "=================================="'

alias ing="docker ps"
alias all="docker ps -a"
alias rmc="docker rm -f"
alias rmi="docker rmi"

unalias ll


function init.sys(){

echo 'function open(){ sudo vim "$@"; }
function load(){ source ~/.bashrc; }
SHELL_PATH=/e/develop/shell
source $SHELL_PATH/common.sh '
>> ~/.bashrc

}


function init.wsl(){

echo '[automount] 
enabled = true 
root = /
options = "metadata,umask=22,fmask=11" 
mountFsTab = false
' | sudo tee /etc/wsl.conf

}

####################################################################################
# xargs 的改进修正了无法调用自定义函数
# echo "a b" | xeach myfunc --option  #分别运行 myfunc --option a; myfunc --option b
####################################################################################
function xeach(){

  if [ "`type -t $1`" == 'function' ];then
    export -f $1
  else
    local cmd=`which $1`
    [ -n "$cmd" ] && { shift 1; set -- $cmd "$@"; }
  fi

  local command="$@"
  xargs -d ' ' -n1 -I '?' bash -c "$command ?"

}

####################################################################################
# 批量运行命令：
# xfor <command> one tow ... #分别运行 command one; command two;...
####################################################################################
function xfor(){
  local func="$1"
  shift 1
  for item in "$@";do
    eval $func $item
  done

}

####################################################################################
# 路径转换
# window 路径转 linux 路径
####################################################################################
function path(){
  local path="${1//\\//}";
  path="${path,}";
  path="/${path/:/}";
  echo \"${path//\/\//\/}\";
}


####################################################################################
# 参数解析，分割成选项列表和参数列表
# parse <default> <retain> "$@" 
# <default> 默认选项，没有则用'-' 占位
# <retain> 保留最后多少位作为参数 
# 示例  parse - 1 "$@"
####################################################################################
function parse(){
    
    local defalut="$1"
    local retain="$2"
    local option=""
    local args=""
    shift 2;

    local arr=( "$@" )
    local length=${#arr[@]}
    for i in "${!arr[@]}"; do
        [ -n "`echo ${arr[$i]} | grep -P '\s*\w+\s+\s*'`" ] && arr[$i]="\"${arr[$i]}\""
    done

    [ $length -eq "$retain" ] && args=${arr[@]:$length - $retain}
    [ $length -gt "$retain" ] && { option=${arr[@]:0:$length - $retain}; args=${arr[@]:$length - $retain}; }
    [ -z "$option" -a "$defalut" != '-' ] && option="$defalut"

    echo "([0]='$option' [1]='$args')"

}


####################################################################################
# 自定义解析路径，方便快速切换目录
####################################################################################
function parse.path(){
 
    declare -A all=`parse - 1 "$@"`
    set -- ${all[1]}

    local root="$1"
    local name="${1%%/*}"

    if  [ -n "$name" -a "$name" != '-' -a  ! -d "$1" ];then
        
        case "${name/%:/}" in
            ?)          root="/${name/%:/}";;
            'src')      root=/d/src;;
            'usr')      root=/d/usr;;
            'bin')      root=/usr/local/bin;;
            'sbin')     root=/usr/local/sbin;;
            'desk')     root=/c/Users/admin/Desktop;;
            'dev')      root=/e/develop;;
            'down')     root=/e/Download;;
            'test')     root=~/test;;
            'etc')      root=/d/etc;;
            'tmp')      root=/tmp;;
        esac
        root="$root${1/#$name/}"
    fi
    echo ${all[0]} $root
}


function forargs(){
    for arg in "$@";do 
        echo "参数 $index: '$arg'";
        let index+=1; 
    done
}

function cd(){
    
    if [[ "$1" =~ ':' ]] && [ -n "${1#*:}" ];then

        local work=( ${1/:/' '} )
        local run="`docker ps -q --filter status=running --filter "name=${work[0]}"`"
        [ -z "$run" ] && run="`docker ps -q --filter status=running --filter "id=${work[0]}"`"
        [ -z "$run" ] && docker start ${work[0]}

        shift 1
        [ $# == 0 ] && set -- bash
        docker exec -itw ${work[1]} ${work[0]} $@
    else
        builtin cd `parse.path $@`
    fi

    
}


function ll(){

    set -- "`parse.path $@`"
    if [ -f "$@" -a -n "`readlink $@`" ]; then
        echo -en "$@ \t-->\t"; readlink $@;
        ll `readlink $@`
    else
        eval ls -alF $@
    fi

}

function list(){
    apt list "$1*";
    apt list "lib$1*";
}

####################################################################################
# 服务运行两种命令形式，如：
# srv <name> [start]|stop 表示启动/停止服务
# srv start|stop <name> 表示启动/停止 dokcer 容器
####################################################################################
function srv(){

    case "$1" in
        'zkServer')
            case "$2" in
                'start')  zkServer.sh stop  2>/dev/null; zkServer.sh start;;
                'stop')   zkServer.sh;;
                *)        srv zkServer start;;
            esac 
        ;;
        php*)
            set -- "php-fpm${1#php}" $2
            case "$2" in
                'start')  sudo pkill -9 $1 2>/dev/null; sudo $1;;
                'stop')   sudo pkill -9 $1;;
                *)        srv $1 start;;
            esac 
        ;;
        'nginx')
            case "$2" in    
                'start'|'') sudo nginx -s stop 2>/dev/null; sudo nginx;;
                'stop')     sudo nginx -s stop;;
                *)          sudo nginx -s $2;;
            esac
        ;;
        'start'|'stop')
   
            [ "$2" == "all" ] && set -- $1 $(docker ps -qf status=exited)
            [ -n "$2" ] && {
                if [ "$1" == 'start' ];then
                    docker restart $2
                else
                    docker "$@"
                fi
            }
        ;;
        *)  
            # mysql 转变名字
            #[ "$1" == 'mysql' ] && { shift 1; set -- mysqld $@; }

            [ $# == 1 ] && set -- $1 'start'
            [ "$2" == 'start' ] && sudo service "$1" stop 1>/dev/null 2>&1
            sudo service "$@" 1>/dev/null
        ;;        
    esac

}


####################################################################################
# 同时启动多个服务如：
# start php nginx mysql 表示启动服务
# start -c php nginx mysql 选项 -c 表示启动的是容器
####################################################################################
function start(){
    local c=0
    [ "$1" == '-c' ] && { c=1; shift 1; }
    [ -z "$1" ] && set -- docker
    for item in "$@";do
        if [ "$c" == 1 ];then
            srv start $item
        else
            srv $item start
        fi
    done
}

####################################################################################
# 同时停止多个服务如：
# stop php nginx mysql 表示停止服务
# stop -c php nginx mysql 选项 -c 表示停止的是容器
####################################################################################
function stop(){
    local c=0
    [ "$1" == '-c' ] && { c=1; shift 1; }
    [ -z "$1" ] && set -- docker
    for item in "$@";do
        if [ "$c" == 1 ];then
            srv stop $item
        else
            srv $item stop
        fi
    done
}

function linux(){

    declare -A name
    name["unknow"]="我不知道"
    name["bullseye"]="Debian 11"
    name["buster"]="Debian 10"
    name["stretch"]="Debian 9"
    name["alpine"]="Alpine Linux是一个由社区开发的基于musl和BusyBox的Linux操作系统"
    name["null"]="null"

    typeset -l index; local index="$1"
    [ -z "${name[$index]}" ] && index='unknow'
    echo $index: ${name[$index]}

}

function see(){

    local content=""
    while read line
    do
        content="$content\n$line";
    done <&0;

    echo "------------------------------------------------------------------------------------------"
    for item in "$@"; do

        if [ "${item:0:1}" == "-" -a "${item:0:2}" != "--" ];then

                item="${item:1}"
                for (( i = 0; i < ${#item}; i = i + 1 )) do
                    echo -e $content | grep -- "^-${item:$i:1}"
                    echo "------------------------------------------------------------------------------------------"
                done
        else
                echo -e $content | grep -- $item
                echo "------------------------------------------------------------------------------------------"
        fi

    done
   
}


#批量将文件链接到 bin 目录，如果参数是一个目录，则链接目录中的所有文件
function tobin(){

    [ $# == 1 -a  -d "$1"  ]  && { cd $1;  set -- `ls $1`; }
    for item in "$@"; do
        [ ${item:0:1} != '/'  ] && item="`pwd`/$item"
        [ ! -f "$item" ]  && item="/d/bin/$item"
        [ -f "$item" ] && sudo ln -fsv $item /usr/local/bin
    done

}

function tosbin(){

    [ $# == 1 -a  -d "$1"  ]  &&  { cd $1; set -- `ls $1`;  }
    for item in "$@"; do
        [  ${item:0:1} != '/'  ] && item="`pwd`/$item"
        [ ! -f "$item" ]  && item="/d/bin/$item"
        [ -f "$item" ] && sudo ln -fsv $item /usr/local/sbin
    done

}


function lnk(){

    local target="$1"
    local link="$2"
    [ ${1:0:1} != '/'  ] && target="`pwd`/$1"
    [ ${2:0:1} != '/'  ] && link="`pwd`/$2"
    sudo ln -isv $target $link
}



function config(){

    if [ -z "$1" ];then
        ./configure --help > config.txt; open config.txt;  return
    elif [ "$1" == '--help' ];then
        ./configure --help; return
    fi

    local word="$1"
    until [ -z "$word" ]
    do
        set -- $word
        [ -n "$1" ] && {
            echo "------------------------------------------------------------------------------------------"
            for kw in "$@"; do

                kw=`echo "$kw" | sed 's/-/\\\-/g'`
                eval "./configure --help | grep -P '$kw'"
                echo "------------------------------------------------------------------------------------------"
            done
        }
        echo -en "\033[31m查找: \033[0m"
        read word
    done

}

function title(){
    ORIGN_PS1=${ORIGN_PS1:-$PS1};
    export PS1="$ORIGN_PS1\033]0;$*\007"
}

function md5(){
    echo -n $1 | md5sum | cut -d ' ' -f1
}

function color(){

    local index="$1"; shift 1
    declare -A color=([black]=30 [red]=31 [green]=32 [yellow]=33 [blue]=34 )
    [ -z "${color[$index]}" ] && index='black'
    echo -e "\033[${color[$index]}m$@\033[0m"
}


function execute(){

    local option="$1"
    if [ "$option" == '-b' ];then
        shift 1
        #获取命令名
        local cmd="$(echo $@ | cut -d ' ' -f1)"
        #替换以执行内建命令
        cmd=${@//"$cmd"/"builtin $cmd"}
    else
        local cmd="$@"
    fi

    color red ">> $cmd";
    eval "$cmd"

}

function mkfdir(){
    local dir=`dirname $1`
    [ -d "$dir" ] || mkdir $dir
}

####################################################################################
#打开文件或目录
#可以打开 docker 容器中的文件，如： open :container /tmp/test.txt  注：参数要冒号开头
#会把容器中的文件复制到 /tmp/<container>/ 目录中，如果复制为成功，则在该目录新建。
#可以再结合一个 push :container /tmp/test.txt 就会找到  /tmp/<container>/test.txt 并复制到容器中
#如果没有参数，表示打开当前目录
#由于我是用的 WSL 打开当前目录的命令 explorer.exe . 请换成 xdg-open 命令
####################################################################################
function open(){

    option="$1"
    case "$1" in
        '-source')  code /etc/apt/sources.list;;
        '-profile') code /etc/profile;;
        '-bashrc')  code /etc/bash.bashrc;;
        '-vhost')   code /etc/nginx/conf.d/default.conf;;
        '-common')  code "$SHELL_PATH/common.sh";;
        '')         explorer.exe .;; #xdg-open $PWD;;
        *:*)
            set -- "${1%:*}" "${1#*:}"
            local file="/tmp/$1/`basename $2`"
            mkfdir $file
            docker cp $1:$2 $file 2>/dev/null
            open $file
        ;;

        *)

            mime=`file --mime-type $option | awk '{print $2}'`
            #如果是符号链接
            if [ $mime == 'inode/symlink' ];then
                option=`readlink -e $option`
                mime=`file --mime-type $option | awk '{print $2}'`
            fi

            if [ -n "`echo $mime | grep -E '^(text/|inode/x-empty|application/json)'`" ];then
                #用编辑器打开
                code $option

            elif [ "$mime" = 'inode/directory' ];then
                cd $option && explorer.exe .
                #xdg-open $option

            elif [ "$mime" != 'cannot' ];then
                #默认方式打开
                color red "文件类型：$mime"
                explorer.exe $option
                #xdg-open $option
                
            else
                color red "新建: $option"
                [ ! -w . ] && sudo touch $option
                code $option
            fi

        ;;

    esac

}


####################################################################################
# 推送命令
# push [path] [message]
# 不带参数时等于 git push
# 带 [path] 表示添加注释并提交到服务器
# push <container>:<file> 结合上面 open ，再把文件推送到容器中
####################################################################################
function push(){

    case "$1" in
        *:*)    set -- "${1%:*}" "${1#*:}"; docker cp /tmp/$1/`basename $2` $1:$2;;   
        'dev')  (cd dev; push .);;
        'lib')  (cd lib; push .);;
        'all')  push dev; push lib;;
        '')     git push;;
        *)      git add $1; git commit -m ${2:-'日常更新'}; git push;;
    esac
}

function pull(){

    #如果只有一个参数，并且该参数不是选项，则识别为 docker 命令
    [ $# == 1 -a "${1:0:1}" != '-' -a -z "${1#*:}" ] && set - "$1:latest"
    case "$1" in
        *:*|*@*)   docker pull $@;;
        *)         git pull $@;;
    esac
}

####################################################################################
# 文件重命令
# name <file> [newname]
# 如果只有一个参数，会自动截取最后一段后缀，作为新文件名
# name ~/nginx.conf.default # 等同于 name ~/nginx.conf.default nginx.conf
####################################################################################
function name(){
  local file=`basename $1`
  [ $# == 1 ] && set -- $1 ${file%.*}
  sudo mv $1 `dirname $1`/$2
}

####################################################################################
#综合了 apt install 和 dpkg -i 安装软件
#如果不带任何参数，则进入查找安装过程
#如：install 回车，输入 mysql，会列出包名，输入要安装的包名或输入 exit 退出
####################################################################################
function install(){

    if [ $# == 0 ];then

        [ -z `which sudo` ] && apt install -y sudo
        local software=""
        until [ "$software" == 'exit' ]
        do
            
            echo -en "\033[31m查找: \033[0m"
            read software

            [ "$software" == 'exit' -o -z "$software" ] && break
            set -- $software
            apt list "$1*";
            apt list "lib$1*";

            echo -en "\033[31m安装: \033[0m"
            read software
            if [ "$software" == 'next' -o -z "$software" ];then
                continue
            elif [ "$software" == 'exit' ];then
                break
            fi
            sudo apt install -y $software && echo $software >> ~/.install.log

        done

    else

        local ext="${1##*.}"
        local log=~/.install.log
        case "$ext" in
            'deb')      sudo dpkg -i $1;;
            'rpm')      sudo rpm -i $1;;
            'list')     cat $log;;
            'docker')   curl -fsSL https://get.docker.com -o get-docker.sh && sudo sh get-docker.sh && sudo usermod -aG docker $USER;;
            'ssh-server')
                    sudo apt install openssh-server &&\
                    sudo sed -i -e 's/^#\s*Port.*/Port 22/' -e 's/^#\s*PermitRootLogin.*/PermitRootLogin yes/' /etc/ssh/sshd_config  &&\
                    sudo systemctl restart ssh
                    sudo systemctl enable ssh
                    #sudo systemctl disable ssh
            ;;
            *) 
                if [ -n "`which apt`" ];then
                    sudo apt install -y $1 && echo $1 >> $log
                elif [ -n "`which yum`" ];then
                    sudo yum install -y $1 && echo $1 >> $log
                fi
            ;;
        esac

    fi

}
#dpkg --configure -a

function show(){

    local two="$2"
    case "$1" in
        'uname')        uname -a;;
        'name')         cat /etc/issue;;
        'command')      compgen -ac;;
        'arch')         arch;; #处理器架构
        'zombie')       ps -A -o stat,ppid,pid,cmd | grep -e '^[Zz]';; #显僵死进程
        'status')       sudo systemctl status ;;
        'dmidecode')    dmidecode -q;;#显示硬件系统部件(SMBIOS/DMI)
        'issue')        cat /etc/issue;;
        'cpu')          cat /proc/cpuinfo;;
        'interrupts')   cat /proc/interrupts;;#显示中断
        'memory')       cat /proc/meminfo;;#显示内存
        'swap')         cat /proc/swaps;;
        'mount')        cat /proc/mounts;;
        'pci')          lspci -tv;;#显示 PCI 设备
        'usb')          lsusb -tv;;#显示 USB 设备
        'netdev')       cat /proc/net/dev;;#网络适配器
        'hda')          hdparm -i /dev/hda;;#磁盘架构特性
        'sda')          hdparm -tT /dev/sda;;#测试性读取操作
        'code')         iconv -l;;#显示可用编码
        'df')           df -h;;#显示
        'host')         cat /etc/hosts;;
        'ip')           [ -z "$two" ] && two='eth0'; ifconfig $two | grep 'inet ' | sed 's/.*inet\s//g' | sed 's/\snet.*//g';;
        path)           echo $PATH | awk -F ':' '{for(i=1;i<=NF;i++){print "  " $i;}}';;
        *)              lsb_release -a;;
    esac

}

####################################################################################
#复制文件,如果目录不存在,则自动创建
####################################################################################
function copy(){
    if [ $# == 1 ];then
        sudo cp $1 "$1.bak"
    else
        local dir="${@: -1}"
        [ "${dir: -1}" == "/" ] || dir="`dirname $dir`" #获取目录名
        [ ! -d "$dir" ] && { color green "新建目录: $dir"; mkdir -p $dir 2>/dev/null || sudo mkdir -p $dir; }
        sudo cp $@
    fi
}

function is(){

    case "$1" in
        'run')

            local name=$2
            local ip=$3

            if [ -z "$ip" ];then
                name="`ps -ef | grep -P \"$name\" | grep -v 'grep'`"

            else
                #如果是纯数字,表示则表示端口
                [ -n "`echo $ip | grep -P '^\d+$'`" ] && ip="\*:$ip"

                name="$name\s+.*${ip}$"
                name="`ps -aux | grep -P \"$name\"`"

            fi

            [ -n "$name" ] && echo $name
        ;;
        *)
            if test $@; then
                echo 'yes'
            else
                echo 'no'
            fi
        ;;

    esac

}



function where(){

	local name="$1"
    [ -n "`which $name`" ]  && { color red "当前命令:"; which $name; } 
    [ -n "`type -t $name`" ] && { color red "命令列表:"; type -a $name; }
    [ "`whereis $name`" != "$name:" ] && { color red "相关目录:"; whereis $name; }

}

#运行容器
function run(){

    local option="--restart=on-failure:1 -v /d/bin:/usr/local/bin -v /e:/e -v /d:/d `echo "$@" | sed -r 's/--name(=|\s)(\w|,)+/#name#/'`" #默认选项 on-failure:1  always
    local name=`echo "$@" | grep -oP '((?<=--name=)|(?<=--name\s))(\w|,)+'`
    [ -z "$name" ] && { name='test'; option="#name# $option"; } #如果没有名字，则取名 test

    if [[ "$name" =~ ',' ]];then
        name=(${name//,/' '})
        for i in ${name[*]}
        do
            docker run -d $(echo $option | sed -r "s/#name#/ --name=$i /")
        done
    else
        [ "$name" == "test" -o "$name" == "demo" ] && docker rm -f $name > /dev/null 2>&1
        docker run $(echo $option | sed -r "s/#name#/ --name=$name /")
    fi

}



function use(){
    title "在 $1 命令中"
    local command="$1"
    until [ -z "$command" ]; do
        echo -en "\033[31m> $command \033[0m"
        read args
        [ "$args" == 'exit' ] && break
        $command $args
    done
}


function image(){

    case "$1" in
        'clean')    docker image rm $(docker image ls -f "dangling=true" -q);;
        '')         set -- 'ls -a';;
        *)
            [ $# == 1 ] && set -- inspect $1
        ;; 
    esac
    docker image $@
}


function look(){

    local format
    case "$1" in
        'ip')   format='{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}';;
        'port') format='{{range $p, $conf := .NetworkSettings.Ports}}{{$p}} {{end}}';;
        'json') format="{{json .${2^}}}"; shift 1;;
    esac

    local con=${2:-"$(docker ps -q)"}
    [ -n "$con" ] && docker inspect --format="{{.Name}}: $format" $con

}


function tools(){

   python $ROOTPH/common.py $@

:<<EOF
#!/usr/bin/env python
# -*- coding: UTF-8 -*-
import sys

def hello(a, b, c):
    print(a)


if __name__ == '__main__':
    eval(sys.argv[1])(*sys.argv[2:])

EOF


}




function git.init(){

    git init &&\
    git add .  &&\
    git commit -m "first commit" &&\
    git branch -M main &&\
    git remote add origin git@github.com:zbseoag/$1 && \
    git push -u origin main

}


alias git.rm="git rm -r --cached"
git.config(){

    git config --global user.name "zbseoag"
    git config --global user.email "zbseoag@163.com"
    git config --global core.editor code
    #中文乱码
    git config --global core.quotepath false
    #git config --list
    #git config user.name
}


git.remote(){
    git remote add origin $1 &&\
    git branch -M main &&\
    git push -u origin main

}

function append(){
    sed -i "\$a $1" $2
}

function auto.install(){

    local software=$1

    if [ -z "$software" ];then
        PS3="请选择安装: "
        select software in -init docker docker-compose redis lua zookeeper ssh-server
        do
            if [ -n "$software" ];then
                echo "开始安装 $software ..."; break
            fi
        
        done
    fi

    cd $PACKAGE
    case "$software" in

        '-init') sudo apt install gcc libssl-dev curl wget;;

        '--mysql')

            sudo apt update
            sudo apt install mysql-server
            sudo systemctl status mysql
            sudo mysql
            #ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'very_strong_password';
            #GRANT ALL PRIVILEGES ON *.* TO 'administrator'@'localhost' IDENTIFIED BY 'very_strong_password';
        ;;

        'docker') curl -fsSL https://get.docker.com -o get-docker.sh && sudo sh get-docker.sh && sudo usermod -aG docker $USER;;
        'docker-compose')
            sudo curl -L "https://github.com/docker/compose/releases/download/1.27.4/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose  &&\
            sudo chmod +x /usr/local/bin/docker-compose  &&\
            docker-compose --version
        ;;

        'redis')
            
            local package='redis-6.0.9.tar.gz'
            local dir=${package%.tar*}
            #sudo apt install gcc libssl-dev

            #如果目录不存在
            if [ ! -d "$dir" ];then
                #如果包不存在则下载
                if [ ! -f "$package" ];then  curl -R -O "http://download.redis.io/releases/${package}"; fi
                #解压
                tar -xvf $package
            fi
            rm $package
            cd $dir &&\
            make clean &&\
            #sudo make MALLOC=libc BUILD_TLS=yes prefix=$PROGRAM/redis install
            sudo make
        
        ;;

        "zookeeper")

            local package='apache-zookeeper-3.6.2-bin.tar.gz'
            local dir=${package%.tar*}

            if [ ! -f "$package" ];then curl -R -O https://mirrors.bfsu.edu.cn/apache/zookeeper/zookeeper-3.6.2/${package}; fi
            rm -rf $dir
            sudo tar -xvf $package &&\
            sudo mv $dir $PROGRAM/$software  &&\
            cd $PROGRAM/$software
        ;;


        *) color red "没有安装步骤" ;;

    esac


}




function lang(){
echo '===============================================================================================
$0             :脚本名称
$#             :参数个数
$@             :参数列表,空格分隔,展开引号
$*             :参数列表,空格分隔,展开引号
"$*"           :所有参数合成一个字符串(1个参数)
"$@"           :可以区分引号(原始数量)
$!             :进程 PID
$_             :上一条命令最后一个参数值
$$             :脚本自身的 PID
$PPID          :父进程 PID
$?             :上一条命令的退出状态码,0表成功,非0表失败
$BASH          :Bash 程序路径
$BASH_VERSION  :Bash 版本号
$EUID          :有效用户 ID
$UID           :用户 ID 号
$GROUPS        :用户所属的组
$HOSTNAME      :主机名称
$IFS           :域分隔符,默认为空白符
$OLDPWD        :上一个工作目录
$PS1           :主提示符
$PS2           :第二提示符,出现在需要补充输入时，默认值为 >
$PS4           :第四提示符,使用 -x 选项调用脚本时，它会出现在每行开头，默认为 +

===============================================================================================
${#str}                         字符长度
${str:3:5}                      按位置截取
${str#*head} ${str##*head}      从前面截断
${str%end*} ${str%%end*}        从后面截断
${str/find/replace}             替换一次
${str//find/replace}            替换全部
${str/#beginwith/replace}       前缀替换
${str/%endwith/replace}         后缀替换
${string -     new}             string  是否已存在？
${string :-    new}             string  是否不为空？
${string +     new}             string  是否不存在？
${string :+    new}             string  是否为空？
${string :+    new}             string  是否为空？
${string =     new}             string  是否已存在？会改变 string 的值
${string :=    new}             string  是否不为空？会改变 string 的值
${string ?     new}             string  是否已存在？会报错
${string :?    new}             string  是否不为空？会报错
[[ "$str" =~ "this" ]]          是否包含

typeset -u|-i name
name='asdasdas'
echo $name

${parameter^|^^pattern}
${parameter,|,,pattern}

===============================================================================================
${data[@]}                      所有元素
${#data[@]}                     长度
${data[@]:start:count}          位置截取,取 start 以及后面 count 个元素
${data[@]#$1} ${data[@]##$1}    查找
${data[@]%$1} ${data[@]%%$1}    从后面查找
${data[@]/$1/$2}                替换
${data[@]/#$1/$2}               replace-head 
${data[@]/%$1/$2}               replace-end

===============================================================================================
/etc/xdg/user-dirs.defaults  ~/.config/user-dirs.dirs
[ -z ${num//0-9/} ]   把变量中的数字替换为空后 ，查看长度是否为0 ，是就表示 是数字



===============================================================================================
declare -A array=([black]=30 [red]=31)
${@: -2}    最后两个参数
${@:1:$#-2} 取到倒数第二个参数为止

local arr=( "$@" )
for i in "${!arr[@]}"; do
    [ -n "`echo ${arr[$i]} | grep -P '\s*\w+\s+\s*'`" ] && arr[$i]="\"${arr[$i]}\""
done

for item in "${arr[@]}";do
     echo $item,
done

for i in "${!arr[@]}"; do 
    echo "${arr[$i]}"
done 




==============================================================================================='

}


#  if [ $# -gt 0 ];then
#      exec 0<$1;#判断是否传入参数：文件名，如果传入，将该文件绑定到标准输入
#  fi

#  while read line
#  do
#      echo $line;

#  done <&0;

function dock(){

    local manage="$1"
    shift 1
    case "$manage" in
        'init')
            echo sed -i "s/deb.debian.org/mirrors.aliyun.com/g" /etc/apt/sources.list
            echo apt update 
            echo apt install iputils-ping
        ;;

        'info')
            [ -z "$@" ] && docker info || docker info | grep "$@"
        ;;


        'pid')
            local container="$1"
            [ -z "$container" ] && container='docker ps -q'
            docker inspect --format '{{.Name}}: {{.State.Pid}}' $($container)
        ;;

        'all')
            echo -e "镜像   \t\t网络   \t\t端口   \t\t\t\t状态   \t\t名称"
            docker ps -a --format '{{.Image}}   \t{{.Networks}}   \t{{.Ports}}   \t\t{{.Status}}   \t{{.Names}}'
        ;;
    esac

    

}




###########################################################
##########################################################

# local cmd=""
# until [ "$cmd" == 'exit' ]
# do
#     read -p ":" cmd
#     [ "$cmd" == 'exit' ] && break
#     docker-helper $cmd

# done


function __comment(){

x=`expr $x + 1`

for file in $(ls); do
    echo $file
done

foo=10
x=foo
eval y='$'$x

cdtrack=$((cdtrack-1))

until who | grep "$1" > /dev/null; do
    sleep 10
done

case "$a" in
    [Yy]|[Yy][Ee][Ss] ) echo "good";;
    [nN]* ) echo "bad";;
    * ) echo "error"
esac


#gcc $file -o /tmp/a.out && /tmp/a.out

#windows换行转linux换行
#sudo sed -i 's/\r$//' $arguemnt


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



function change.etc(){

    local newpath=`basename $(dirname $1)`
    [ -d $newpath ] && sudo mkdir -p $newpath
    cp -r $1 /d/etc/$newpath &&\
    sudo rm -rf $1 &&\
    sudo ln -s /d/etc/$newpath ${1%/}

}

function init.cp(){
    docker cp ~/.bashrc $1:/root/.bashrc
}

function init.install(){
    apt update && apt install -y sudo procps vim iputils-ping curl
}

function repository(){

    case "$1" in
        'composer')
            if [ "$2" == 'reset' ];then
                composer config -g --unset repos.packagist   
            else 
                composer config -g repo.packagist composer https://packagist.phpcomposer.com
            fi
        ;;                
    esac
    
}

# sudo dpkg --get-selections | awk '/i386/{print $1}'
# sudo apt-get remove --purge `dpkg --get-selections | awk '/i386/{print $1}'`
# sudo dpkg --remove-architecture i386	  //移除i386架构
# sudo dpkg --print-foreign-architectures //显示已启用的异质体系结构

#xrandr
#cvt 1920x1080
#xrandr --newmode "1920x1080_60.00"  173.00  1920 2048 2248 2576  1080 1083 1088 1120 -hsync +vsync
#xrandr --addmode HDMI-1 "1920x1080_60.00"



function reset.idea(){

    rm -rf /c/Users/admin/AppData/Roaming/JetBrains/PhpStorm{$1}/eval && \
    rm /c/Users/admin/AppData/Roaming/JetBrains/PhpStorm{$1}/options/other.xml

    #rm -rf /c/Users/admin/AppData/Roaming/JetBrains/Idea{$1}/eval && \
    #rm /c/Users/admin/AppData/Roaming/JetBrains/Idea{$1}/options/other.xml
    #似乎不用删除注册表项：HKEY_CURRENT_USER\Software\JavaSoft\Prefs\jetbrains\phpstorm

}

