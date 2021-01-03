demo(){
    #  if [ $# -gt 0 ];then
    #      exec 0<$1;#判断是否传入参数：文件名，如果传入，将该文件绑定到标准输入
    #  fi
    
    #  while read line
    #  do
    #      echo $line;
    #  done <&0;
    local index=1
    for arg in "$@";do echo "参数 $index:   '$arg'";let index+=1; done
    echo ${@: -2}
    echo ${@:1:$#-2}
    


   
}



# 打印参数列表
function args(){

    local index=1
    for arg in "$@"; do
        echo "参数${index}: '$arg'"
        let index+=1
    done


    local all=("$@")
    for((i=$#-1; i>=0; i--));  
    do  
        echo ${all[$i]}
    done  

}

string(){

    local str="11223344"
    echo 'str="11223344"'
    echo "\${#str}      ${#str}         长度"
    echo "\${str:3:5}   ${str:3:5}      按位置截取"
    echo "\${str#*3}    ${str#*3}       前截取后"
    echo "\${str##*3}   ${str##*3}      前截取后最长"
    echo "\${str%*3}     ${str%*3}      后截取前"
    echo "\${str%%*3}    ${str%%*3}     后截取前最长"
    echo "\${str/3/a}   ${str/3/a}      替换一次"
    echo "\${str//3/a}  ${str//3/a}     替换全部"
    echo "\${str/#11/a} ${str/#11/a}    前缀替换"
    echo "\${str/%44/a} ${str/%44/a}    后缀替换"

    [[ "$str" =~ "this" ]] && echo "$str contains this" 
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

    //[ -z ${num//0-9/} ]   把变量中的数字替换为空后 ，查看长度是否为0 ，是就表示 是数字

}

function ddd(){

cat <<EOF
$0             :脚本名称
$#             :参数个数
$*             :参数列表,空格分隔参数,无视引号
"$*"           :所有参数看作一个字符串
$@             :参数列表,空格分隔参数,无视引号
"$@"           :可以区分引号
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
$PS2           :第二提示符,出现在需要补充输入时，默认值为 ">"
$PS4           :第四提示符,使用 -x 选项调用脚本时，它会出现在每行开头，默认为"+"
EOF

}
