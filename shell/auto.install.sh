#!/usr/bin/env bash

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
            sudo mysql_secure_installation
            sudo mysql
            #ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'very_strong_password';
            #GRANT ALL PRIVILEGES ON *.* TO 'administrator'@'localhost' IDENTIFIED BY 'very_strong_password';
        ;;


        'docker')
            sudo apt install docker  docker.io
            sudo usermod -aG docker $USER
            docker --version
            sudo touch /etc/docker/daemon.json
            sudo sed -i '$a { "registry-mirrors": ["http://hub-mirror.c.163.com"] }' /etc/docker/daemon.json
            sudo systemctl restart docker

        ;;

        'docker-compose')
            sudo curl -L "https://github.com/docker/compose/releases/download/1.27.4/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
            sudo chmod +x /usr/local/bin/docker-compose
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


        'ssh-server')
            sudo apt install openssh-server &&\
            sudo sed -i -e 's/^#\s*Port.*/Port 22/' -e 's/^#\s*PermitRootLogin.*/PermitRootLogin yes/' /etc/ssh/sshd_config  &&\
            sudo systemctl restart ssh
            sudo systemctl enable ssh
            #sudo systemctl disable ssh

        ;;

        *) color red "没有安装步骤" ;;

    esac



}

