#!/usr/bin/env bash


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

