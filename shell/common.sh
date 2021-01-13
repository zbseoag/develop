#!/usr/bin/env bash

#history 配置
export HISTTIMEFORMAT='%F ' #时间格式化
export HISTSIZE=100 #命令历史列表总条数
export HISTFILESIZE=10000 #命令历史文件总条数
export HISTCONTROL="ignoredups" #ignoredups：忽略重复命令； ignorespace：忽略空白开头的命令  ignoreboth
export HISTIGNORE="pwd:history" #不记录的命令

#export JAVA_HOME="/d/usr/jdk15.0"
export PATH=$PATH:/d/usr/jdk/bin:/d/usr/elasticsearch/bin:/d/usr/kibana/bin

alias ll="ls -alF"
alias wk="cd -"
alias up="cd .."
alias python="python3.8"
alias ec="echo"
alias re="return"
alias tar.src="tar -C /d/src -xvf"
alias tar.usr="tar -C /d/usr -xvf"
alias make="make -j $(nproc)"
alias update="sudo apt update && apt list --upgradable"
alias uninstall="sudo apt purge"
alias close.nautilus="killall nautilus"
alias apt.fix="sudo apt-get install -f "
alias all.users="cat /etc/passwd |cut -f 1 -d:"
alias port="netstat -ap | grep"
alias real="realpath -s"
alias ecarg='echo "(数量 $#):"; for item in "$@";do echo "$item,";done '
alias rearg='ecarg;return'
alias ecarglist='echo "============参数列表==============";local index=1;for arg in "$@";do echo "$index: $arg";let index+=1; done ;echo "=================================="'

alias ing="docker ps"
alias all="docker ps -a"
alias rm:="docker rm -f"

function init(){
    echo '
    function load(){ source ~/.bashrc; }
    ROOTPH=/e/develop/shell
    source $ROOTPH/common.sh
    ' >> ~/.bashrc
}

function path(){

    local path="${1//\\//}"
    path="${path/:/}"
    echo /${path,}
}

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

function parse.path(){
 
    declare -A all=`parse - 1 "$@"`
    set -- ${all[1]}

    local root="$1"
    local name="${1%%/*}"
    if  [ -n "$name"  -a  ! -d "$1" ];then
        
        case "${name/%:/}" in
            'c')        root=/c;;
            'd')        root=/d;;
            'e')        root=/e;;
            'stack')    root=~/oneinstack;;
            'src')      root=/d/src;;
            'usr')      root=/d/usr;;
            'bin')      root=/usr/local/bin;;
            'sbin')     root=/usr/local/sbin;;
            'desk')     root=/desktop;;
            'dev')      root=/e/develop;;
            'down')     root=/e/Download;;
            'test')     root=~/test;;
            'lib')      root=/e/php-library;;
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
    builtin cd `parse.path $@`
}

unalias ll
function ll(){
    eval ls -alF `parse.path $@`
}

function list(){
    apt list "$1*";
    apt list "lib$1*";
}


function start(){

    case "$1" in
        'php')      sudo php-fpm;;
        'nginx')    sudo nginx;;
        'redis')    redis-server;;  
        '')         start docker;;
        'web')      start php && start nginx;;
        *)          
            [ $# == 1 ] && {
                [ -n "`type $1 2>/dev/null`" ] && {  sudo service $1 start; return; }
            }
            [ "$1" == "all" ] && set -- "$(docker ps -qf status=exited)"
            [ -n "$@" ] && docker start $@
        ;;
    esac
}

function stop(){

    case "$1" in
        'php')      sudo pkill -9 php-fpm;;
        'nginx')    sudo nginx -s stop;;
        'web')      stop php && stop nginx;;
        '')         stop docker;;
        -*)         sudo pkill -9 ${1#-};;
        *)          
            [ $# == 1 ] && {
                [ -n "`type $1 2>/dev/null`" ] && {  sudo service $1 stop; return; }
            }
            [ "$1" == "all" ] && set -- "$(docker ps -q)"
            [ -n "$@" ] && docker stop $@
        ;;   
    esac
}

function restart(){

    case "$1" in
        'php')      stop php && start php;;
        'nginx')    stop nginx && start nginx;;
        'web')      stop web && start web;;
        '')         restart docker;;
        *)          
            [ $# == 1 ] && {
                [ -n "`type $1 2>/dev/null`" ] && {  sudo service $1 restart ; return; }
            }
            [ "$1" == "all" ] && set -- "$(docker ps -q)"
            [ -n "$@" ] && docker restart $@ 
        ;;   
    esac
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

    [ $# == 1 -a  -d "$1"  ]  && { cd $1;  set -- `ls $1`;  }
    for item in "$@"; do
        [  ${item:0:1} != '/'  ] && item="`pwd`/$item"
        [  -f "$item" ] && sudo ln -fsv $item /usr/local/bin
    done

}

function tosbin(){

    [ $# == 1 -a  -d "$1"  ]  &&  { cd $1; set -- `ls $1`;  }
    for item in "$@"; do
        [  ${item:0:1} != '/'  ] && item="`pwd`/$item"
        [  -f "$item" ] && sudo ln -fsv $item /usr/local/sbin
    done

}


function lnk(){

    local target="$1"
    local link="$2"
    [ ${1:0:1} != '/'  ] && target="`pwd`/$1"
    [ ${2:0:1} != '/'  ] && link="`pwd`/$2"

    sudo ln -isv $target $link

}

function change(){
    local file=/d/usr/$1
    rm $file && ln -sv /d/usr/$1$2 $file
}


function config(){

    [ -z "$1" ] && {
        ./configure --help > config.txt
        open config.txt
        return
    }
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



function mywd(){

    php -r "echo '*' . substr(strtolower(md5('$1')) . strtoupper(md5('$1')), -36, 8) . '*' ;";
    echo
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


#打开文件或目录
#可以打开 docker 容器中的文件，如： open :container /tmp/test.txt  注：参数要冒号开头
#会把容器中的文件复制到 /tmp/<container>/ 目录中，如果复制为成功，则在该目录新建。
#可以再结合一个 push :container /tmp/test.txt 就会找到  /tmp/<container>/test.txt 并复制到容器中
#如果没有参数，表示打开当前目录
#由于我是用的 WSL 打开当前目录的命令 explorer.exe . 请换成 xdg-open 命令

function open(){

    option="$1"
    case "$1" in
        '-source')  code /etc/apt/sources.list;;
        '-profile') code /etc/profile;;
        '-bashrc')  code /etc/bash.bashrc;;
        '-php')     code "`dirname $(dirname $(which php))`/etc/php.ini";;
        '-nginx')   code "`dirname $(dirname $(which nginx))`/conf/nginx.conf";;
        '-common')  code "$ROOTPH/common.sh";;
        '-demo')    code "$ROOTPH/demo.sh";;
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


#可以推送 git
#可以结合上面 open 可以再把文件推送到容器
function push(){

    case "$1" in
        *:*)    set -- "${1%:*}" "${1#*:}"; docker cp /tmp/$1/`basename $2` $1:$2;;   
        'dev')  (cd dev; push .);;
        'lib')  (cd lib; push .);;
        'all')  push dev; push lib;;
        '')     git push;;
        *)      git add $@; git commit -m '日常更新'; git push;;
    esac
}

#文件重命令
#第二个参数只是单纯表示新名字，如：rename /home/old.txt new.txt
function rename(){
    sudo mv $1 `dirname $1`/$2
}


#综合了 apt install 和 dpkg -i 安装软件
#如果不带任何参数，则进入查找安装过程
#如：install 回车，输入 mysql，会列出包名，输入要安装的包名或输入 exit 退出

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
        *)              lsb_release -a;;
    esac

}


#复制文件,如果目录不存在,则自动创建
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


function isrun(){

    local cmd="$1"; shift 1
    case "$cmd" in
        'run')

            local name=$1
            local ip=$2

            if [ -z "$ip" ];then
                name="`ps -aux | grep -P \"$name\" | grep -v 'grep'`"

            else
                #如果是纯数字,表示则表示端口
                [ -n "`echo $ip | grep -P '^\d+$'`" ] && ip="\*:$ip"

                name="$name\s+.*${ip}$"
                name="`ps -aux | grep -P \"$name\"`"

            fi

            [ -n "$name" ] && echo $name
        ;;

    esac

}

#类似于 whereis 命令，但列出内容更详细
function where(){

	local name="$1"
    [ -n "`which $name`" ]  && { color red "当前命令:"; which $name; } 
    [ -n "`type -a $name 2>/dev/null`" ] && { color red "命令列表:"; type -a $name; }
    [ "`whereis $name`" != "$name:" ] && { color red "目录列表:"; whereis $name; } || color red "$name 不存在"

}

function run(){

    #默认选项，加上所有参数除了最后两个参数以外
    local option="--network=network --restart=on-failure:2 ${@:1:$#-2}"

    set -- ${@: -2}
    local name="$1"
    local image="$2"

    #如果是 start[:step]:count 如：1:2:3 表示从 1开始，步长为2 启动3个镜像，1:5 等价于 1:1:5
    #如果是 one,tow,three 就表示启动多台，名字分别就是 one tow three
    #启动多台时，自动带上 -d 表示在后台运行
    if [[ "$name" =~ ':' ]];then

        name=(${name//:/' '})
        [ -z "${name[2]}" ] && { name[2]=${name[1]}; name[1]=1; }

        local one=${name[0]}
        for(( i=0; i<${name[2]}; i++))
        do  
            docker run $option -d --name="${image%:*}-$one" $image
            one=`expr $one + ${name[1]}`
        done

    elif [[ "$name" =~ ',' ]];then

        name=(${name//,/' '})
        for i in ${name[*]}
        do
            docker run $option -d --name="$i" $image
        done
    else
        docker run $option --name="$name" $image
    fi

}


function exe(){
    [ $# == 1 ] && { 
        local run="`docker ps -q --filter status=running --filter "name=$1"`"
        [ -z "$run" ] && run="`docker ps -q --filter status=running --filter "id=$1"`"
        [ -z "$run" ] && docker start $1
        set -- "-it $@ bash"
    }
    [ $# == 2 ] && set -- "-it $@"
    docker exec $@

}


function image(){

    local one="$1"
    shift 1
    case "$one" in

        'clean')    docker image rm $(docker image ls -f "dangling=true" -q);;
        '')         docker images;;
        *)  
            local c='.ContainerConfig';
            docker image inspect --format "{{printf \"端口:\t%s \n目录:\t%s \n命令:\t%s \n入口:\t%s \" $c.ExposedPorts $c.WorkingDir $c.Cmd $c.Entrypoint}}" $one
        ;;
       
    esac

}


function info(){

    local format
    case "$1" in
        'ip')   format='{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}';;
        'port') format='{{range $p, $conf := .NetworkSettings.Ports}}{{$p}} {{end}}';;
        'json') format="{{json .${2^}}}"; shift 1;;
    esac

    local con=${2:-"$(docker ps -q)"}
    [ -n "$con" ] && docker inspect --format="{{.Name}}: $format" $con

}


function let(){

   python $ROOTPH/common.py $@

}



function into(){

    local command="$1"
    until [ -z "$command" -o  "$command" == 'exit' ]
    do
        read -p ":" args
        [ "$args" == 'exit' ] && break
        set -- $args
        $command $@

    done

}


function git.init(){

    git init &&\
    git add .  &&\
    git commit -m "first commit" &&\
    git branch -M main &&\
    git remote add origin git@github.com:zbseoag/$1 && \
    git push -u origin main

}

