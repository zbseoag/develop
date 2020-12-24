#!/usr/bin/env bash


function dockers(){

    local manage="$1"
    local args="$@"
    shift 1

    case "$manage" in

        'start'|'stop')
            [ "$@" == 'all' ] && args='$(docker ps -qa)'
        ;; 
        'rm') 
            [ "$@" == 'all' ] && args='-f $(docker ps -qa)'
        ;;
        'image') 
            [ "$@" == 'all' ] && args='$(docker image ls -qa)'
            [ "$@" == 'clear' ] && args='$(docker image ls -f "dangling=true" -q)'
        ;;
        'exec')
            [ $# == 1 ] && args=${args//$@/"-it $@ bash"} 
            [ $# == 2 ] && args=${args//$@/"-it $@"}
        ;;
        'ip')
            [ "$@" == 'all' ] && args='$(docker ps -q)'
            args="--format '{{.Name}}: {{.NetworkSettings.IPAddress}}' $args"
            manage='inspect' 
        ;;
        'pid')
            [ "$@" == 'all' ] && args='$(docker ps -q)'
            args="--format '{{.Name}}: {{.State.Pid}}' $args"
            manage='inspect'
        ;;
        'begin')
            args="--name=$1 -p $2"
            shift 2
            args="$args --network=localhost --restart=on-failure:2 -it $@"
            manage='run'
        ;;
        'info')  [ -z "$@" ] && docker info || docker info | grep "$@"
        ;;
        *) manage='';;

    esac
    
    echo docker $manage $args
    eval "docker $manage $args"

}



function docker-helper(){

    local cmd="$1"
    shift 1





    'about')        
        local c='.ContainerConfig'
        docker image inspect --format "{{printf \"端口:\t\t%s \nDir:\t\t%s \n环境变量:\t%s \n运行命令:\t%s\" $c.ExposedPorts $c.WorkingDir $c.Env $c.Cmd}}" "$@"
                    
    ;;

    'pid')
        if [ "$@" == 'all' ];then
            docker instect --format "{{.Name}}: {{.State.Pid}}" $(docker ps -q)
        else
            docker instect --format '{{.Name}}: {{.State.Pid}}' "$@"
        fi

    ;;

    *)  docker $cmd "$@";;
    esac

}

# local cmd=""
# until [ "$cmd" == 'exit' ]
# do
#     read -p ":" cmd
#     [ "$cmd" == 'exit' ] && break
#     docker-helper $cmd

# done




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

