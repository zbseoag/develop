#!/usr/bin/env bash

#环境变量
export JAVA_HOME="/usr/lib/jvm/jdk-15.0.1"
PATH=$PATH:$JAVA_HOME/bin

#history 配置
#export HISTFILE=~/.bash_history #命令历史文件名
#export HISTTIMEFORMAT='%F ' #时间格式化
export HISTSIZE=5000 #命令历史列表总条数
export HISTFILESIZE=10000 #命令历史文件总条数
export HISTCONTROL="ignoreboth" #ignoredups：忽略重复命令； ignorespace：忽略空白开头的命令
export HISTIGNORE="pwd:history" #不记录的命令

alias home="cd ~"
alias bk="cd -"
alias up="cd .."

alias src="cd /src"
alias srv="cd /srv"
alias dpan="cd /mnt/d"
alias bin="cd /usr/local/bin"
alias sbin="cd /usr/local/sbin"
alias download="cd /mnt/d/warehouse/Download"
alias develop="cd /mnt/d/develop"
alias tarc="tar -C /src -xf"

alias update="sudo apt update && apt list --upgradable"
alias purge="sudo apt purge"

alias close.nautilus="killall nautilus"
alias apt.fix="sudo apt-get install -f "

alias git.add="git add -f"
alias git.rm="git rm -r --cached"
alias git.push="git add . &&  git commit  -m '日常更新'  &&  git push"


git.push.root(){

    git add -f /srv/etc  &&\
    git add -f /srv/bin &&\
    git add -f /srv/nginx1.9/etc &&\
    git add -f /srv/nginx1.9/nginx &&\
    git add -f /srv/php8.0/etc &&\
    git add -f /srv/php8.0/bin &&\
    git add -f /srv/php8.0/lib/php/extensions/debug-zts-20200930 &&\
    git commit -m "日常更新" &&\
    git push
}




string(){

    local str="11223344"
    echo 'str="11223344"'
    echo "\${#str}      ${#str}         长度"
    echo "\${str:3:5}   ${str:3:5}      按位置截取"
    echo "\${str#*3}    ${str#*3}       前截取后"
    echo "\${str##*3}   ${str##*3}      前截取后最长"
    echo "\${str%*3}     ${str%*3}        后截取前"
    echo "\${str%%*3}    ${str%%*3}       后截取前最长"
    echo "\${str/3/a}   ${str/3/a}      替换一次"
    echo "\${str//3/a}  ${str//3/a}     替换全部"
    echo "\${str/#11/a} ${str/#11/a}    前缀替换"
    echo "\${str/%44/a} ${str/%44/a}    后缀替换"

    # str="this is a string"
    # [[ $str =~ "this" ]] && echo "$str contains this" 
    # [[ $str =~ "that" ]] || echo "$str does NOT contain that"
}

helper(){

    #取倒数第二个参数
    local second=${@:(-2):1}
    local cmd="$@"
    #如果倒数第二个参数不是以 - 开头，则在它前面添加 --help
    [ "${second:0:1}" != "-" ] && cmd=${@/$second/"$second --help"}

    #最后的参数是要匹配的
    local last=${@: -1}
    #从命令最后将最后哪个的参数前面加上 |grep --
    execute ${cmd/%$last/"|grep -- $last"}

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


# sudo dpkg --get-selections | awk '/i386/{print $1}'
# sudo apt-get remove --purge `dpkg --get-selections | awk '/i386/{print $1}'`
# sudo dpkg --remove-architecture i386	  //移除i386架构
# sudo dpkg --print-foreign-architectures //显示已启用的异质体系结构

#xrandr
#cvt 1920x1080
#xrandr --newmode "1920x1080_60.00"  173.00  1920 2048 2248 2576  1080 1083 1088 1120 -hsync +vsync
#xrandr --addmode HDMI-1 "1920x1080_60.00"

if [ -z "`which xdg-open`" ];then
function xdg-open(){ explorer.exe .; }
fi


function tobin(){
    for one in "$@"; do
        local file=`realpath $one`;
        sudo ln -s $file /usr/local/bin
    done
}

function tosbin(){

    for one in "$@"; do
        local file=`realpath $one`;
        sudo ln -s $file /usr/local/sbin
    done

}


function config(){

    local kw=`echo "$*" | sed 's/[ ][ ]*/.*/g'`
    if [ -z "$kw" ];then
        ./configure --help
    else
        eval "./configure --help | grep -P '$kw'"
    fi

}

alias ps.find="ps -aux | grep -v 'grep'| grep"
alias ps.stop="sudo pkill -9"
alias net.port="netstat -ap | grep"

alias php.start="sudo php-fpm"
alias php.stop="sudo pkill -9 php-fpm"

alias nginx.start="sudo nginx"
alias nginx.stop="sudo nginx -s stop"
alias http.start="php.start && nginx.start"
alias http.stop="php.stop && nginx.stop"

alias system.boot.terminal="sudo systemctl set-default multi-user.target"
alias system.boot.desktop="sudo systemctl set-default graphical.target"
alias all.users="cat /etc/passwd |cut -f 1 -d:"

function docker.source.into(){ docker cp /etc/apt/sources.list $1:/etc/apt/sources.list; docker cp $ROOTPH/linux/common.sh $1:/; }

function docker.cp.into(){ docker cp $2 $1:/; }
function docker.cp.out(){ docker cp $1:$2 /home/$USER; }
function docker.append.bashrc(){ echo -e '\nsource /common.sh' >> /etc/bash.bashrc; bash; }


function tty.title(){
    ORIGN_PS1=${ORIGN_PS1:-$PS1}
    export PS1="$ORIGN_PS1\033]0;$*\007"
}


function docker.open(){

    local container="$1"
    local infile="$2"
    local outfile=/tmp/`basename $infile`
    docker cp $container:$infile $outfile && open $outfile || return $?

    local update=""
    local times=1
    echo -e "\033[31m回车更新, exit 退出\033[0m"
    until [ "$update" == 'exit' ]
    do
        read -p "第 $times 次更新:" update
        [ "$update" == 'exit' ] && break
        docker cp $outfile $container:$infile || { echo "更新失败！"; return 1; }
        times=`expr $times + 1`
    done
    
}



function load(){

    case "$1" in
        'docker') source "$ROOTPH/docker.sh" ;;
        'install')

                [ -z `which sudo` ] && apt install -y sudo

                local software=""
                until [ "$software" == 'exit' ]
                do
                   
                    echo -en "\033[31m查找: \033[0m"
                    read software

                    [ "$software" == 'exit' ] && break
                    
                    apt list "$software*";
                    apt list "lib$software*";

                    echo -en "\033[31m安装: \033[0m"
                    read software

                    if [ "$software" == 'next' ];then
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


# 打印参数列表
function p(){

    local index=1
    for arg in "$@"; do
        echo "参数${index}: '$arg'"
        let index+=1
    done

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
        'bin')          ll /usr/local/bin;;
        'sbin')         ll /usr/local/sbin;;

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
                sudo apt install -y $software
            elif [ -n "`which yum`" ];then
                sudo yum install -y $software
            fi
        ;;
    esac

}



function array(){

    local data=(${1//''/}) #字符串转数组
    local option=$2

    shift 2
    case "$option" in
        'len')              echo ${#data[@]};;
        'cut')              if [ -z "$2" ];then echo ${data[@]:$1}; else echo ${data[@]:$1:$2}; fi;;
        'find')             echo ${data[@]#$1};;
        'find-long')        echo ${data[@]##$1};;
        'find-end')         echo ${data[@]%$1};;
        'find-end-long')    echo ${data[@]%%$1};;
        'replace-head')     echo ${data[@]/#$1/$2};;
        'replace-end')      echo ${data[@]/%$1/$2};;
        'replace')          echo ${data[@]/$1/$2};;
        *)                  echo ${data[@]};;
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


function isrun(){

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


