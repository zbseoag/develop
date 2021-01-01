#!/usr/bin/env bash

demo(){

    #  if [ $# -gt 0 ];then
    #      exec 0<$1;#判断是否传入参数：文件名，如果传入，将该文件绑定到标准输入
    #  fi
    
    #  while read line
    #  do
    #      echo $line;
    #  done <&0;

    local cmd=""
    local end=0
    echo "------------------------------------------------------------------------------------------"
    for arg in "$@"; do
        
        if [ "$end" == 0 ];then
            cmd="$cmd $arg"
            [ "${arg:0:1}" == "-" ] && { cmd="$cmd | grep --"; end=1; }
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



# 打印参数列表
function p(){

    local index=1
    for arg in "$@"; do
        echo "参数${index}: '$arg'"
        let index+=1
    done

}

#history 配置
#export HISTFILE=~/.bash_history #命令历史文件名
#export HISTTIMEFORMAT='%F ' #时间格式化
export HISTSIZE=5000 #命令历史列表总条数
export HISTFILESIZE=10000 #命令历史文件总条数
export HISTCONTROL="ignoreboth" #ignoredups：忽略重复命令； ignorespace：忽略空白开头的命令
export HISTIGNORE="pwd:history" #不记录的命令

#环境变量
export JAVA_HOME="/usr/lib/jvm/jdk-15.0.1"
PATH=$PATH:$JAVA_HOME/bin:/d/elasticsearch7.10/bin:/d/kibana7.10/bin
ROOTPH="/e/develop/shell"

alias python="python3.8"
alias ba="cd -"
alias up="cd .."
alias cd.bin="cd /usr/local/bin"
alias cd.sbin="cd /usr/local/sbin"

alias cdc="cd /c"
alias cdd="cd /d"
alias cde="cd /e"
alias cd.src="cd /d/src"
alias cd.download="cd /e/Download"
alias cd.develop="cd /e/develop"
alias cd.syntax="cd /e/develop/syntax"
alias cd.desktop="cd /desktop"
alias tar.src="tar -C /d/src -xvf"
alias tar.dpan="tar -C /d -xvf"

alias llbin="ll /usr/local/bin"
alias llsbin="ll /usr/local/sbin"

alias update="sudo apt update && apt list --upgradable"
alias purge="sudo apt purge"

alias close.nautilus="killall nautilus"
alias apt.fix="sudo apt-get install -f "

alias git.add="git add -f"
alias git.rm="git rm -r --cached"
alias git.push="git add . &&  git commit  -m '日常更新'  &&  git push"
alias xdg-open="explorer.exe ."
alias ps.find="ps -aux | grep -v 'grep'| grep"
alias ps.stop="sudo pkill -9"
alias net.port="netstat -ap | grep"

alias php.start="sudo php-fpm"
alias php.stop="sudo pkill -9 php-fpm"
alias nginx.start="sudo nginx"
alias nginx.stop="sudo nginx -s stop"

alias http.start="php.start && nginx.start"
alias http.stop="php.stop && nginx.stop"
alias all.users="cat /etc/passwd |cut -f 1 -d:"


apt.list(){
    local software="$1"
    apt list "$software*";
    software="lib$software"
    apt list "$software*";
}

let(){ 
	if [ -z "$2" ];then
		set -- $@ start
	fi
	sudo service $@;

}

start(){
    let docker start
}
stop(){
    let docker stop
}


linux.name(){

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
    name["null"]="null"
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




git.config.init(){

    git config --global user.name "zbseoag"
    git config --global user.email "zbseoag@163.com"
    git config --global core.editor code
    #git config --global core.editor "'C:/Program Files/Notepad++/notepad++.exe' -multiInst -notabbar -nosession -noPlugin"
    git config --list
    git config user.name
    
}


function git.init(){

    git init &&\
    git add .  &&\
    git commit -m "first commit" &&\
    git branch -M main &&\
    git remote add origin $1 &&\
    git push -u origin main
}

function git.remote(){
    git remote add origin $1 &&\
    git branch -M main &&\
    git push -u origin main

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
        echo "./configure --help | grep -P '$kw'"
        eval "./configure --help | grep -P '$kw'"
    fi

}



function tty.title(){
    ORIGN_PS1=${ORIGN_PS1:-$PS1}
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


        *)      exec bash;;
    esac
    
}

function my.passwd(){

    php -r "echo  '*' . substr(strtolower(md5('$1')) . strtoupper(md5('$1')), -36, 8) . '*' ;";
    echo
}


function color(){

    local index="$1"
    shift 1
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



### 打开文件或目录 ###
function open(){

    option="$1"
    shift 1
    case "$option" in
        'terminal')         gnome-terminal;;
        'sources')          execute vim /etc/apt/sources.list;;
        'user-dirs')        echo '/etc/xdg/user-dirs.defaults || ~/.config/user-dirs.dirs';;
        '')                 xdg-open $PWD;;
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

            elif [ "$mime" = 'inode/directory' -o "$mime" != 'cannot' ];then
                #默认方式打开
                color red "文件类型：$mime"
                xdg-open $option
            
            else
                color red "新建: $option"
                [ ! -w . ] && sudo touch $option
                code $option
            fi

        ;;

    esac


}


function show(){

    case "$1" in
        'version')      execute lsb_release -a;;
        'name')         execute uname -a;;
        'command')      execute compgen -ac;;
        'arch')         arch;; #处理器架构
        'zombie')       execute "ps -A -o stat,ppid,pid,cmd | grep -e '^[Zz]'" ;; #显僵死进程
        'status')       execute sudo systemctl status ;;
        'dmidecode')    execute dmidecode -q;;#显示硬件系统部件(SMBIOS/DMI)
        'issue')        execute cat /etc/issue;;
        'cpu')          execute cat /proc/cpuinfo;;
        'interrupts')   execute cat /proc/interrupts;;#显示中断
        'memory')       execute cat /proc/meminfo;;#显示内存
        'swap')         execute cat /proc/swaps;;
        'mount')        execute cat /proc/mounts;;
        'pci')          execute lspci -tv;;#显示 PCI 设备
        'usb')          execute lsusb -tv;;#显示 USB 设备
        'netdev')       execute cat /proc/net/dev;;#网络适配器
        'hda')          execute hdparm -i /dev/hda;;#磁盘架构特性
        'sda')          execute hdparm -tT /dev/sda;;#测试性读取操作
        'code')         execute iconv -l;;#显示可用编码
        'ip')           ifconfig ens32 | grep 'inet ' | sed 's/.*inet\s//g' | sed 's/\snet.*//g';;#显示ip
        'df')           execute df -h;;#显示
        'name')         cat /etc/issue;;
        *)              execute 'lsb_release -a && uname -a';;
    esac

}


function where(){

	local name="$1"
    [ -n "`which $name`" ]  && { color red "当前命令:"; which $name; } 
    [ -n "`type -a $name 2>/dev/null`" ] && { color red "命令列表:"; type -a $name; }
    [ "`whereis $name`" != "$name:" ] && { color red "目录列表:"; whereis $name; } || color red "$name 不存在"

}


function install(){

    local software="$1"
    local ext="${software##*.}"

    case "$ext" in
        'deb')  sudo dpkg -i $software;;
        'rpm')  sudo rpm -i $software;;
        *) 
            if [ -n "`which apt`" ];then
                sudo apt install -y $software && echo $software >> ~/install.log
            elif [ -n "`which yum`" ];then
                sudo yum install -y $software && echo $software >> ~/install.log
            fi
        ;;
    esac

}


#复制文件,如果目录不存在,则自动创建
#copy file /dir/newfile
#copy file /dir/path/
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

    local cmd="$1"
    shift 1
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



function dock(){

    local manage="$1"
    shift 1
    case "$manage" in
        'init')
            echo sed -i "s/deb.debian.org/mirrors.aliyun.com/g" /etc/apt/sources.list
            echo apt update 
            echo apt install iputils-ping
        ;;

        'run')
            #${@: -1}
            for one in "$@"; do
                shift 1
                [ "$one" == '-' ] && break
                option="$option $one"
            done

            local image="$1"
            shift 1
            for name in "$@"; do
                docker run --network network --restart=on-failure:2 $option --name "${image%:*}-$name" $image
            done
        
        ;;

        'open')
            local container="$1"
            local infile="$2"
            local outfile=/tmp/$1/`basename $infile`
            [ -d "/tmp/$1" ] || mkdir /tmp/$1
            docker cp $container:$infile $outfile && open $outfile || return $?
        ;;

        'push')
            local container="$1"
            local infile="$2"
            local outfile=/tmp/$1/`basename $infile`
            docker cp $outfile $container:$infile || { echo "更新失败！"; return 1; }
        ;;

        'rm')
            local image="$1"
            shift 1
            for name in "$@"; do
                docker rm -f "${image%:*}-$name"
            done
        ;;

        'exec')
            [ $# == 1 ] && docker exec -it $1 bash || docker exec -it $@
        ;;

        'ip')
            local container="$1"
            [ -z "$container" ] && container='docker ps -q'
            docker inspect --format '{{.Name}}: {{.NetworkSettings.Networks.network.IPAddress}}' $($container)
        ;;
        'info')
            [ -z "$@" ] && docker info || docker info | grep "$@"
        ;;

        'image')        
            local c='.ContainerConfig'
            docker image inspect --format "{{printf \"端口:\t%s \n目录:\t%s \n命令:\t%s \n入口:\t%s \" $c.ExposedPorts $c.WorkingDir $c.Cmd $c.Entrypoint}}" $1
        ;;
        'start')
            docker start $(docker ps -qa)
        ;;
        'stop')
            docker stop $(docker ps -qa)
        ;;
        'pid')
            local container="$1"
            [ -z "$container" ] && container='docker ps -q'
            docker inspect --format '{{.Name}}: {{.State.Pid}}' $($container)
        ;;

        'clear')
            docker image rm $(docker image ls -f "dangling=true" -q)
        ;;
        'all')
            docker ps -a
        ;;
    esac

}

