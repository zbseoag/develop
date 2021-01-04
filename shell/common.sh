#!/usr/bin/env bash
ROOTPH="/e/develop/shell"

#history 配置
#export HISTFILE=~/.bash_history #命令历史文件名
#export HISTTIMEFORMAT='%F ' #时间格式化
export HISTSIZE=5000 #命令历史列表总条数
export HISTFILESIZE=10000 #命令历史文件总条数
export HISTCONTROL="ignoreboth" #ignoredups：忽略重复命令； ignorespace：忽略空白开头的命令
export HISTIGNORE="pwd:history" #不记录的命令

#export JAVA_HOME="/d/usr/jdk15.0"
export PATH=$PATH:/d/usr/jdk15.0/bin:/d/usr/elasticsearch7.10/bin:/d/usr/kibana7.10/bin

alias python="python3.8"
alias ba="cd -"
alias up="cd .."
alias cd.bin="cd /usr/local/bin"
alias cd.sbin="cd /usr/local/sbin"

alias cdr="cd /"
alias cdc="cd /c"
alias cdd="cd /d"
alias cde="cd /e"
alias cd.src="cd /d/src"
alias cd.usr="cd /d/usr"
alias cd.download="cd /e/Download"
alias cd.develop="cd /e/develop"
alias cd.syntax="cd /e/develop/syntax"
alias cd.desktop="cd /desktop"
alias tar.src="tar -C /d/src -xvf"
alias tar.usr="tar -C /d/usr -xvf"
alias look="ps -aux | grep -v 'grep'| grep"

alias llbin="ll /usr/local/bin"
alias llsbin="ll /usr/local/sbin"

alias update="sudo apt update && apt list --upgradable"
alias purge="sudo apt purge"

alias ing="docker ps"
alias all="docker ps -a"
alias end="docker ps -f status=exited"

alias close.nautilus="killall nautilus"
alias apt.fix="sudo apt-get install -f "
alias all.users="cat /etc/passwd |cut -f 1 -d:"
alias port="netstat -ap | grep"
alias addr="show ip"

function list(){
    apt list "$1*";
    apt list "lib$1*";
}


function start(){

    local one="$1"
    case "$1" in
        'php')      sudo php-fpm;;
        'nginx')    sudo nginx;;
        'redis')    redis-server;;
        'web')      start php && start nginx;;      
        :*)         [ "$one" == ":all" ] && one="$(docker ps -q -f status=exited)" || one=${one#:}; [ -n "$one" ] && eval docker start $one;;
        *)          sudo service $one start;;
    esac
    
}

function stop(){

    local one="$1"
    case "$1" in
        'php')      sudo pkill -9 php-fpm;;
        'nginx')    sudo nginx -s stop;;
        'web')      stop php && stop nginx;;
        'redis')    redis-cli shutdown;;
        -*)         sudo pkill -9 ${one#-};; 
        :*)         [ "$one" == ":all" ] && one="$(docker ps -q)" || one=${one#:}; [ -n "$one" ] && eval docker stop $one;;
        *)          sudo service $one stop;;
    esac
}


function linux(){

    declare -A name
    name["unknow"]="我不知道"
    name["bullseye"]="Debian 11"
    name["buster"]="Debian 10"
    name["stretch"]="Debian 9"
    name["jessie"]="Debian 8"
    name["wheezy"]="Debian 7"
    name["alpine"]="Alpine Linux是一个由社区开发的基于musl和BusyBox的Linux操作系统"
    name["null"]="null"
    name["null"]="null"

    typeset -l index; local index="$1"
    [ -z "${name[$index]}" ] && index='unknow'
    echo $index: ${name[$index]}

}


function see(){

    local cmd=""
    local end=0
    echo "------------------------------------------------------------------------------------------"
    for arg in "$@"; do
    
        if [ "$end" == 0 ];then
            cmd="$cmd $arg"
            shift 1
            [ "${arg:0:1}" == "-" ] && { [ -z "$1" ] && eval $cmd  || { end=1; cmd="$cmd | grep --"; } }
        else
            if [ "${arg:0:1}" == "-" -a "${arg:0:2}" != "--" ];then
                arg="${arg:1}"
                for (( i = 0; i < ${#arg}; i = i + 1 )) do
                    eval $cmd "-${arg:$i:1}"
                    echo "------------------------------------------------------------------------------------------"
                done
            else
                eval $cmd $arg
                echo "------------------------------------------------------------------------------------------"
            fi

        fi

    done

}



function tobin(){
    for one in "$@"; do
        local file=`realpath $one`;
        sudo ln -fs $file /usr/local/bin
    done
}

function tosbin(){

    for one in "$@"; do
        local file=`realpath $one`;
        sudo ln -fs $file /usr/local/sbin
    done

}


function config(){

    local kw=`echo "$*" | sed 's/[ ][ ]*/.*/g'`
    kw=`echo "$kw" | sed 's/-/\\\-/g'`
    if [ -z "$kw" ];then
        ./configure --help
    else
        eval "./configure --help | grep -P '$kw'"
    fi

}


function title(){
    ORIGN_PS1=${ORIGN_PS1:-$PS1};
    export PS1="$ORIGN_PS1\033]0;$*\007"
}


function load(){

    case "$1" in
        'install')

                [ -z `which sudo` ] && apt install -y sudo

                local software=""
                until [ "$software" == 'exit' ]
                do
                   
                    echo -en "\033[31m查找: \033[0m"
                    read software

                    [ "$software" == 'exit' -o -z "$software" ] && break
                    
                    apt list "$software*";
                    software="lib$software"
                    apt list "$software*";

                    echo -en "\033[31m安装: \033[0m"
                    read software

                    if [ "$software" == 'next' -o -z "$software" ];then
                        continue
                    elif [ "$software" == 'exit' ];then
                        break
                    fi

                    sudo apt install -y $software && echo $software >> ~/install.log

                done
        ;;

        *)      exec bash;
    esac
    
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


### 打开文件或目录 ###
function open(){

    option="$1"
    case "$option" in
        'source')   execute vim /etc/apt/sources.list;;
        'udirs')    echo '/etc/xdg/user-dirs.defaults || ~/.config/user-dirs.dirs';;
        'profile')  code /etc/profile;;
        'bashrc')   code /etc/bash.bashrc;;
        :*)
            local con="${option#:}"
            local file="/tmp/$con`basename $2`"
            mkfdir $file
            docker cp $con:$2 $file 2>/dev/null
            open $file
        ;;
        '') explorer.exe .;; #xdg-open $PWD;;
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

function push(){

    option="$1"
    case "$option" in
        :*)     local conter="${option#:}"; docker cp /tmp/$conter/`basename $2` $conter:$2 || { echo "上传失败"; return 1; };;   
        'dev')  (echo "推送 dev ..." && cd.develop && push);;
        'lib')  (echo "推送 lib ..." && cd /e/php-library && push);;
        'all')  push dev && push lib;;
        '')     push .;;
        *)      git add $@ && git commit -m '日常更新' && git push;;
    esac

}

function show(){

    local two="$2"
    case "$1" in
        'name')         uname -a;;
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
        'name')         cat /etc/issue;;
        'ip')           [ -z "$two" ] && two='eth0'; ifconfig $two | grep 'inet ' | sed 's/.*inet\s//g' | sed 's/\snet.*//g';;
        *)              lsb_release -a;;
    esac

}


function where(){

	local name="$1"
    [ -n "`which $name`" ]  && { color red "当前命令:"; which $name; } 
    [ -n "`type -a $name 2>/dev/null`" ] && { color red "命令列表:"; type -a $name; }
    [ "`whereis $name`" != "$name:" ] && { color red "目录列表:"; whereis $name; } || color red "$name 不存在"

}

function install(){

    local ext="${1##*.}"
    case "$ext" in
        'deb')  sudo dpkg -i $1;;
        'rpm')  sudo rpm -i $1;;
        *) 
            if [ -n "`which apt`" ];then
                sudo apt install -y $1 && echo $1 >> ~/install.log
            elif [ -n "`which yum`" ];then
                sudo yum install -y $1 && echo $1 >> ~/install.log
            fi
        ;;
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


function is(){

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

run(){

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

function del(){

    local name="$1"
    local image="$2"
    if [[ "$name" =~ ':' ]];then

        name=(${name//:/' '})
        [ -z "${name[2]}" ] && { name[2]=${name[1]}; name[1]=1; }

        local one=${name[0]}
        for(( i=0; i<${name[2]}; i++))
        do  
            docker rm -f "${image%:*}-$one"
            one=`expr $one + ${name[1]}`
        done

    elif [[ "$name" =~ ',' ]];then

        name=(${name//,/' '})
        for i in ${name[*]}
        do
            docker rm -f $i
        done
    else
        docker rm -f $name
    fi
  
}


function exe(){

    local one="$1"
    shift 1
    case "$one" in
        *) [ $# == 0 ] && docker exec -it $one bash || docker exec -it $one $@ ;;
    esac
}

function img(){

    local one="$1"
    shift 1
    case "$one" in

        'clean')    docker image rm $(docker image ls -f "dangling=true" -q);;
        *)  
            local c='.ContainerConfig';
            docker image inspect --format "{{printf \"端口:\t%s \n目录:\t%s \n命令:\t%s \n入口:\t%s \" $c.ExposedPorts $c.WorkingDir $c.Cmd $c.Entrypoint}}" $one
        ;;
       
    esac

}


function get(){

    local format
    case "$1" in
        'ip')   format='{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}';;
        'port') format='{{range $p, $conf := .NetworkSettings.Ports}}{{$p}} {{end}}';;
        'json') format="{{json .${2^}}}"; shift 1;;
    esac

    local con=${2:-"$(docker ps -q)"}
    [ -n "$con" ] && docker inspect --format="{{.Name}}: $format" $con

}

function rename(){
    sudo mv $1 `dirname $1`/$2
}