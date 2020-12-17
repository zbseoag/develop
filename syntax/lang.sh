#!/bin/bash

cat <<EOF
\$0             :脚本名称
\$#             :参数个数
\$*             :参数列表,空格分隔参数,无视引号, "\$*" 所有参数看作一个字符串
\$@             :参数列表,空格分隔参数,无视引号, "\$@" 可以区分
\$!             :进程 PID
\$_             :上一条命令最后一个参数值
\$$             :脚本自身的 PID
\$PPID          :父进程 PID
\$?             :上一条命令的退出状态码,0表成功,非0表失败
\$BASH          :Bash 程序路径
\$BASH_VERSION  :Bash 版本号
\$EUID          :有效用户 ID
\$UID           :用户 ID 号
\$GROUPS        :用户所属的组
\$HOSTNAME      :主机名称
\$IFS           :域分隔符,默认为空白符
\$OLDPWD        :上一个工作目录
\$PS1           :主提示符
\$PS2           :第二提示符,出现在需要补充输入时，默认值为 ">"
\$PS4           :第四提示符,使用 -x 选项调用脚本时，它会出现在每行开头，默认为"+"
EOF




comment(){

name=tom

echo "我的名字: $name"
echo "脚本文件(\$0): $0"
echo "第一个参数(\$1): $1"
echo "参数列表(\$*): $*"
echo "参数列表(\$@): $@"
echo "共计参数个数((\$#)): $#"
echo "请输入一个新名称: "
unset name
read name
echo "新名称: $name"
echo "脚本执行完成"


echo -n "Is it morning?(yes or no) :"
read answer

if [ "$answer" = "yes" ];then
    echo Good morning
elif [ "$answer" = "no" ];then
    echo Good afternoon
else
    echo Sorroy please enter yes or no
    exit 1
fi



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





exit 0









#!/bin/bash
. ./func.sh

read -p "command:" command option

OLD_IFS=$IFS
IFS=" "
args=($option)
IFS=$OLD_IFS
option=${args[0]}


case $command in

    see) 

        if [ $option == '?' ];then
            echo 'see -pwd'
            read -p "see:" pwd
        else
            pwd=${args[0]};
        fi
        echo $pwd | sudo -S ls -alF /media/sf_Downloads/
    ;;

    # copy file to windows
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






#获取第一个参数作为命令,并截取第一位开始
cmd=${1:1}
#获取第二个参数
container_id=$2


pid(){

    echo `docker inspect --format "{{ .State.Pid }}" $1`
}


#函数是否存在
if [ "$(type -t $cmd)" != "function" ] ; then
     echo "$cmd command is not exist!"
    exit 1
fi

#调用
$cmd $container_id




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







#!/bin/bash

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





#!/bin/bash

command=$1
arguemnt=$2

#输出环境变量
show(){

    case "$arguemnt" in
        'path' ) echo $PATH | awk -F ':' '{for(i=1;i<=NF;i++){print "  " $i;}}';;
        * ) echo "do nothing!"
    esac
}

#添加到bin目录
add-bin(){

   sudo ln -s $(pwd)/$arguemnt /usr/local/bin/$arguemnt
   echo "add $(pwd)/$arguemnt to /usr/local/bin/$arguemnt"
   
}




#windows换行转linux换行
trim-rn(){
    sudo sed -i 's/\r$//' $arguemnt
}


if [ $command == "--help" ]; then

	echo "用法: tool 命令 参数..."
	exit 0
		
elif [ "$(type -t $command)" == "function" ] ; then
    #函数是否存在
	$command
	
else
     echo "$command 命令不存在"
	 exit 0
	 
fi






