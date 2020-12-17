
#include "csapp.h"

void echo(int connfd){
	printf("Int: %d\n", connfd);
}

void sigchld_handler(int sig){

    while(waitpid(-1, 0, WNOHANG) > 0);
    return;
}

int main(int argc, char **argv){

    int listenfd, connfd, port;

    socklen_t clientlen = sizeof(struct sockaddr_in);

    struct sockaddr_in clientaddr;

    if(argc != 2){

		fprintf(stderr, "usage: %s <port>\n", argv[0]);
		exit(0);
    }

    port = atoi(argv[1]);

    Signal(SIGCHLD, sigchld_handler);

    listenfd = Open_listenfd(port);

    while(1){

		connfd = Accept(listenfd, (SA *) &clientaddr, &clientlen);

		//如果在父进程中
		if (Fork() == 0){
			Close(listenfd); /* Child closes its listening socket */
			echo(connfd);    /* Child services client */ //line:conc:echoserverp:echofun
			Close(connfd);   /* Child closes connection with client */ //line:conc:echoserverp:childclose
			exit(0);         /* Child exits */
		}
		Close(connfd); 
	
	
    }


}

