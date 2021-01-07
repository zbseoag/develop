function test1(){


    echo tet;

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


