#!/usr/bin/env bash

function ip(){

    if [ "$@" == 'all' ];then
        docker inspect --format '{{.Name}}: {{.NetworkSettings.IPAddress}}' $(docker ps -q)
    else
        docker inspect --format '{{.NetworkSettings.IPAddress}}' "$@"
    fi
}

function start(){

    if [ "$@" == 'all' ];then
        docker start  $(docker ps -qa)
    else
        docker start "$@"
    fi
    
}

function image(){
    local c='.ContainerConfig'
    docker image inspect --format "{{printf \"端口:\t\t%s \nDir:\t\t%s \n环境变量:\t%s \n运行命令:\t%s\" $c.ExposedPorts $c.WorkingDir $c.Env $c.Cmd}}" "$@"
}

function stop(){
    if [ "$@" == 'all' ];then
        docker stop $(docker ps -q)
    else
        docker stop "$@"
    fi
}


function pid(){

    if [ "$@" == 'all' ];then
        docker instect --format "{{.Name}}: {{.State.Pid}}" $(docker ps -q)
    else
        docker instect --format '{{.Name}}: {{.State.Pid}}' "$@"
    fi
}

function info(){
    [ -z "$@" ] && docker info || docker info | grep "$@"
}

function exe(){
    docker exec -it "$@"
}



function install(){

    apt update
    for command in "$@"; do

        case "$command" in
            'ps')       apt install -y procps;;
            'ping')     apt install -y iputils-ping;;
        esac

    done

}




function __command_lists(){

docker image ls --filter="reference=:latest"
docker search --filter="is-official=true" alpine
docker search --filter="is-automated=true" alpine
docker image inspect ubuntu:20.10

docker image ls --digests ubuntu:20.10

docker image history ubuntu:20.10
docker run --restart=unless-stopped -d ubuntu:20.10

docker network ls
docker network inspect bridge
ip link show docker0
docker network inspect bridge | grep bridge.name
docker network create -d bridge localnet

brctl show
docker run -d --name=c1 --network localnet ubuntu:20.10 sleep 1d
docker port c1
docker network inspect localnet --format '{{json .Containers}}'
docker run -it --name=c2 --network localnet ubuntu:20.10 bash
docker.container.init c2
ping c1


}

