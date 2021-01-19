#!/usr/bin/env bash
alias grep='grep --color=auto'
alias l='ls -CF'
alias la='ls -A'
alias lh='l | head'
alias ls='ls --color=auto'
alias ll='ls -alF'
alias make='make -j 4'
alias port='netstat -ap | grep'
alias real='realpath -s'
alias remove='apt purge'
alias up='cd ..'
alias update='apt update && apt list --upgradable'
alias open='vim'
alias wk='cd -'

function init(){
    echo 222
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


#类似于 whereis 命令，但列出内容更详细
function where(){

	local name="$1"
    [ -n "`which $name`" ]  && { color red "当前命令:"; which $name; } 
    [ -n "`type -a $name 2>/dev/null`" ] && { color red "命令列表:"; type -a $name; }
    [ "`whereis $name`" != "$name:" ] && { color red "目录列表:"; whereis $name; } || color red "$name 不存在"

}

function append(){
    sed -i "\$a $1" $2
}

function list(){
    apt list "$1*";
    apt list "lib$1*";
    apt search $1*;
}