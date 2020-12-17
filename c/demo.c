#include <unistd.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <stdio.h>
#include <stdlib.h>
#include <dirent.h>
#include <string.h>
#include <sys/mman.h>
#include <errno.h>
#include <curses.h>
#include <getopt.h>
#include <ctype.h>


//转成大写
void upper(){

	int ch;
	while((ch = getchar()) != EOF){

		putchar(toupper(ch));
	}

}



//文件复制(系统)
void copy(char *source, char *target){

	char content[1024];
	int in, out;
	int length;

	in = open(source, O_RDONLY);
	out = open(target, O_WRONLY | O_CREAT, S_IRUSR | S_IWUSR);

	while ((length = read(in, content, sizeof(content))) > 0){
		write(out, content, length);
	}

}


//文件复制
void fcopy2(char *source, char *target){

	int c;
	FILE *in, *out;
	in = fopen(source, "r");
	out = fopen(target, "w");

	while ((c = fgetc(in)) != EOF){
		fputc(c, out);
	}

}


//文件复制
void fcopy(char *source, char *target, int length){

	FILE *in = fopen(source, "r");
	FILE *out = fopen(target, "w");

	char content[length];

	while (fgets(content, length, in) != NULL){
		fputs(content, out);
	}


}

//打印目录结构
void print_dir(char *dir, int depth){

	DIR *dp;
	struct dirent *entry;
	struct stat statbuf;

	if ((dp = opendir(dir)) == NULL) printf("Error: %s\n", strerror(errno));

	chdir(dir);
	while ((entry = readdir(dp)) != NULL){

		lstat(entry->d_name, &statbuf);
		if (strcmp(".", entry->d_name) == 0 || strcmp("..", entry->d_name) == 0) continue;

		printf("%*s%s/\n", depth, "", entry->d_name);
		//进入更深一级目录
		if (S_ISDIR(statbuf.st_mode)) print_dir(entry->d_name, depth + 4);

	}
	//退回到上一级目录
	chdir("..");
	closedir(dp);

}


//打印参数列表
void print_argv(int argc, char *argv[]){

	char dest[100] = "参数: ";
	int i;
	for (i = 1; i < argc; i++){

		strcat(dest, argv[i]);
		if (i + 1 < argc) strcat(dest, ", ");
	}

	printf("%s\n", dest);

}

//打印整型
void pint(int num){
	printf("输出: %d \n", num);
}


void type_of_data_size(){

	printf("空(void):%d \n", sizeof(void));
	printf("字符型(char):%d \n", sizeof(char));
	printf("短整型(short):%d \n", sizeof(short));
	printf("整型(int):%d \n", sizeof(int));
	printf("长整型(long int):%d \n", sizeof(long int));
	printf("浮点型(float):%d \n", sizeof(float));
	printf("双精度(double):%d \n", sizeof(double));
	printf("长双精度(long double):%d \n", sizeof(long double));


}


//---------------------------------------------------------------------------------------------------------------------------------------------------------//
//---------------------------------------------------------------------------------------------------------------------------------------------------------//

void demo_memory(){

	#define NRECORDS (100)

	typedef struct{
		int integer;
		char string[24];
	} RECORD;


	RECORD record, *mapped;
	int i, f;
	FILE *fp;

	fp = fopen("temp/records.dat", "w+");
	for (i = 0; i < NRECORDS; i++){
		record.integer = i;
		sprintf(record.string, "RECORD-%d", i);
		fwrite(&record, sizeof(record), 1, fp);
	}
	fclose(fp);


	fp = fopen("temp/records.dat", "r+");
	fseek(fp, 43 * sizeof(record), SEEK_SET);
	fread(&record, sizeof(record), 1, fp);

	record.integer = 143;
	sprintf(record.string, "RECORD-%d", record.integer);

	fseek(fp, 43 * sizeof(record), SEEK_SET);
	fwrite(&record, sizeof(record), 1, fp);
	fclose(fp);



	f = open("temp/records.dat", O_RDWR);
	mapped = (RECORD *) mmap(0, NRECORDS * sizeof(record), PROT_READ | PROT_WRITE, MAP_SHARED, f, 0);
	mapped[43].integer = 243;
	sprintf(mapped[43].string, "RECORD-%d", mapped[43].integer);

	msync((void *) mapped, NRECORDS * sizeof(record), MS_ASYNC);
	munmap((void *) mapped, NRECORDS * sizeof(record));
	close(f);

}

//命令选项
#include <getopt.h>
#define _GNU_SOURCE
void getopt_demo(int argc, char *argv[]){


	int option;
	struct option options[] = {
		{"init", 0, NULL, 'i'},//{名称, 是否带参数0|1|[2], NULL, 对应的短选项}
	{"file", 1, NULL, 'f'},
	{"list", 2, NULL, 'l'},
	{"help", 0, NULL, 'h'},
	{0, 0, 0, 0}//结尾必须
	};

	while ((option = getopt_long(argc, argv, ":if:lr", options, NULL)) != -1){
		switch (option){
		case 'i':
		case 'l':
		case 'r':
			printf("option: %c\n", option);
			break;
		case 'f':
			printf("filename: %s\n", optarg);
			break;
		case ':':
			printf("option meeds a value\n");
			break;
		case '?':
			printf("未知选项: %c \n", optopt);
		default:
			printf("help");

		}
	}

	for (; optind < argc; optind++){
		printf("参数: %s \n", argv[optind]);
	}


}

//环境变量
void env_demo(int argc, char *argv[]){


	//遍历所有环境变量
	if (argc == 1){
		extern char **environ;

		while (*environ){
			printf("%s\n", *environ);
			environ++;
		}
		return;
	}

	char *name = argv[1];
	char *value = getenv(name);


	if (value){
		printf("环境变量: %s 的值是: %s\n", name, value);
	} else{
		printf("环境变量: %s 没有值\n", name);
	}

	if (argc == 3){
		value = argv[2];
		char *string = malloc(strlen(name) + strlen(value) + 2);
		strcpy(string, name);
		strcat(string, "=");
		strcat(string, value);
		// putevn("name=tom")
		if (putenv(string) != 0){
			fprintf(stderr, "调用 putenv() 失败 \n");
			free(string);
			exit(1);
		}

		value = getenv(name);
		if (value){
			printf("环境变量: %s 的新值: %s\n", name, value);
		} else{
			printf("环境变量: %s 的新值是 NULL?\n", name);
		}

	}



}



void demo_curses(){

	const char witch_one[] = " first witch ";
	const char witch_two[] = " second witch ";
	const char *scan_ptr;

	initscr();

	move(5, 15);
	attron(A_BOLD);
	printw("%s", "macbeth");
	attroff(A_BOLD);
	refresh();
	sleep(1);

	move(8, 15);
	attron(A_STANDOUT);
	printw("%s", "thunder and lightning");
	attroff(A_STANDOUT);
	refresh();
	sleep(1);

	move(10, 10);
	printw("%s", "whe shall we three meet again");
	move(11, 23);
	printw("%s", "in thunder lightning, or in rain?");
	move(13, 10);
	printw("%s", "when hosfsfdone");
	refresh();
	sleep(1);

	attron(A_DIM);
	scan_ptr = witch_one + strlen(witch_one) - 1;
	while (scan_ptr != witch_two){
		move(13, 10);
		insch(*scan_ptr--);

	}
	attroff(A_DIM);
	refresh();
	sleep(1);

	move(LINES - 1, COLS - 1);
	refresh();
	sleep(1);
	endwin();
	exit(EXIT_SUCCESS);


}

// 加法 x+ y
int add(int x, int y){
	return x + y;
}


//函数指针作为函数的参数
int math(int(*func)(int, int), int x, int y){
	return func(x, y);
}

//函数指针
void demo_func_pointer(){

	//函数指针(指向函数的内存地址)
	int(*add_p)(int, int) = add;

	int a = math(add, 10, 2);
	int b = add_p(15, 1);

	printf("a=%d, b=%d \n", a, b);
}

#define _XOPEN_SOURCE
#include <time.h>
void demo_time(){

	int i;
	time_t timestamp, timestamp2, timestamp3;

	time(&timestamp);
	timestamp3 = timestamp2 = time((time_t *) 0);//传递一个空指针

	printf("当前时间: timestamp=%ld timestamp2=%ld \n", timestamp, timestamp2);

	sleep(1);
	timestamp2 = time(&timestamp);
	printf("现在时间: timestamp=%ld timestamp2=%ld \n", timestamp, timestamp2);

	double a = difftime(timestamp, timestamp3);
	printf("两时间差: timestamp - timestamp3=%f \n", a);

	struct tm *date;
	date = gmtime(&timestamp);

	timestamp3 = mktime(date);//转时间戳
	printf("转时间戳: timestamp3=%ld \n", timestamp3);

	printf("今天日期: %02d-%02d-%02d %02d:%02d:%02d \n", date->tm_year, date->tm_mon + 1, date->tm_mday, date->tm_hour, date->tm_min + 1, date->tm_sec);

	printf("格式日期: %s%s", asctime(date), ctime(&timestamp));


	char buf[256];
	char *result;
	struct tm tm_pty2;

	date = localtime(&timestamp);
	strftime(buf, 256, "%A %d %B, %I:%M %p", date);
	printf("函数 strftime(): %s \n", buf);

	strcpy(buf, "Thu 26 July 2007, 17:53 will do fine");

	//date = &tm_pty2;
	//result = strptime(buf, "%a %b %d %Y, %R", date);
	//printf(" %s \n", result);





}


/*
公鸡一个五块钱，母鸡一个三块钱，小鸡三个一块钱，现在要用一百块钱买一百只鸡，问公鸡、母鸡、小鸡各多少只？

*/
void demo_m(){

	int x, y, z; //分别表示公鸡,母鸡,小鸡

	//穷举法
	for (x = 0; x <= 100 / 5; x++){
		for (y = 0; y <= 100 / 3; y++){
			for (z = 0; z <= 100; z++){

				//总价格100,且z是3的倍数,且总数量也是100
				if (5 * x + 3 * y + z / 3 == 100 && z % 3 == 0 && x + y + z == 100){
					printf("公鸡 %2d 只，母鸡 %2d 只，小鸡 %2d 只\n", x, y, z);
				}

			}
		}
	}

}


#include <math.h>

//求100以内勾股数: 只要穷举 a 和 b,就能相应得出 c,然后再判断是否满满足条件即可
void demo_pai(){

	int a, b, c, max = 100;
	printf("100内的勾股数：\n   a    b    c \n");
	for (a = 1; a <= max; a++){

		for (b = a + 1; b <= max; b++){

			c = (int) sqrt(a*a + b * b);
			if (a + b > c && a + c > b && b + c > a && a * a + b * b == c * c && c <= 100){
				printf("%4d %4d %4d \n", a, b, c);
			}
		}
	}

}

#include <sys/types.h>
#include <sys/wait.h>
void demo_ps(){

	//system("ps -ax");
	//execl("/bin/ps", "ps", "ax", 0);

	printf("fork 程序开始... \n");

	pid_t pid = fork();
	char *message;
	int code, n;

	switch (pid){
	case -1:
		perror("fork 失败!");
		exit(1);
	case 0:
		message = "子进程";
		n = 5;
		code = 37;
		break;
	default:
		message = "父进程";
		n = 3;
		code = 0;
	}

	for (; n > 0; n--){
		printf("%s \n", message);
		sleep(1);
	}

	//如果在父进程中
	if (pid != 0){

		int status;
		pid_t child_pid = wait(&status);//暂停父进程,直到子进程结束为止,返回子进程的pid

		//执行到这里,表示子进程已完成.
		printf("子进程已完成: PID=%d \n", child_pid);

		int exited = WIFEXITED(status);
		if (exited){
			printf("子进程正常结束: 退出状态=%d  退出码=%d \n", exited, WEXITSTATUS(status));
		} else{
			printf("子进程异常终止! \n");
		}

	}
	//退出码
	exit(code);

}

void demo_exec(int argc, char *argv[]){

	char *file;
	if (argc != 2){
		fprintf(stderr, "缺少文件名参数 \n");
		exit(1);
	}

	file = argv[1];
	freopen(file, "r", stdin);

	execl("./func", "func", 0);

	perror("无法执行 ./func \n");
	exit(3);
}


#include <signal.h>
void sig_handler(int signuum){

	//在信号处理程序中，尽量不要调用与标准IO相关的不可重入的函数
	printf("捕获到信号:%d \n", signuum);
}

void demo_signal(){

	struct sigaction  act;

	act.sa_handler = sig_handler; //注册信号处理函数
	act.sa_flags = 0;//将标志设置为0默认

	sigemptyset(&act.sa_mask);//将信号处理函数执行期间掩码设置为空,以保证没有信号被屏蔽,即所有信号都将被接收

	//注册信号处理程序
	if (sigaction(SIGINT, &act, NULL) < 0){
		fprintf(stderr, "调用 sigaction() 出错");
		perror("系统错误:");
		exit(1);
	}

	while (1){

		//pause();//让程序挂起，等待接收终端SIGINT信号(ctrl+c)
		kill(getpid(), SIGINT); sleep(1);//每隔一秒重复发送SIGINT信号
	}

}

void demo_linklist(){

	struct weapon {

		int atk;
		int price;
		struct weapon *next;

	}a, b, c, *head, *p;

	a.price = 100;
	a.atk = 1000;

	b.price = 200;
	b.atk = 2000;

	c.price = 300;
	c.atk = 3000;

	head = &a;

	a.next = &b;
	b.next = &c;
	c.next = NULL;

	p = head;
	while (p != NULL){
		printf("价格:%d  攻击力:%d \n", p->price, p->atk);
		p = p->next;
	}


}

/*
#include <pthread.h>
#define _REENTRANT

char message[] = "Hellow world";
void *thread_function(void *arg){
	printf("thread_function is running. Argument was %s \n", (char *) arg);
	sleep(3);
	strcpy(message, "Bye!");
	pthread_exit("Think you for the CPU time");
}
void demo_thread(){

	int res;
	pthread_t a_thread;
	void *thread_result;

	res = pthread_create(&a_thread, NULL, thread_function, (void *) message);
	if(res != 0){
		perror("Thread creation failed");
		exit(EXIT_FAILURE);

	}

	printf("线程已加入， 它返回 %s \n", (char *) thread_result);
	printf("消息是 %s \n", message);
	exit(EXIT_SUCCESS);


}
*/


int main(int argc, char *argv[]){


	//print_argv(argc, argv);
	//print_dir("/home/zbseoag/Downloads", 0);
	//fcopy("file.in", "file.out", 1024);
	//demo_memory();
	//getopt_demo(argc, argv);
	//env_demo(argc, argv);
	//demo_curses();

	//demo_pai();
	//demo_ps();
	//demo_exec(argc, argv);
	//demo_signal();

	//demo_thread();
	exit(0);

}







////////////////////////////////////////////////
///////////////////////////////////////////////////////


#include <malloc.h>
struct weapon {

	int atk;
	int price;
	struct weapon *next;

};

struct weapon* create(){
	
	struct weapon * head;
	struct weapon *p1, *p2;

	int n = 0;

	p1 = p2 = (struct weapon *) malloc(sizeof(struct weapon));

	scanf("%d, %d", &p1->price, &p1->atk);
	head = NULL;

	while (p1->price != 0) {
		n++;
		if (n == 1) head = p1;
		else p2->next = p1;

		p2 = p1;
		p1 = (struct weapon*) malloc(sizeof(struct weapon));

		scanf("%d,%d", &p1->price, &p1->atk);
	}
	p2->next = NULL;
	return (head);

}



#include <stdio.h>
#include <stdarg.h>

/*
    批量打印字符串
    p("%s", "is", "a", "demo", "3338", "sfsf", NULL);
*/
void p(char type[], ...) {

	va_list args;
	char *arg;

	va_start(args, type);

	char format[20] = "[p(%d)]: ";

	strcat(format, type);
	strcat(format, "\n");
	int i = 1;
	while(1){

		arg = va_arg(args, char *);
		if (arg == NULL) break;
		printf(format, i++, arg);
	}

	va_end(args);

}


/*
    打印数组
    int a[3][3] = {{1,2},{3,4},{5,6,7}};
    parr(&a, 3, 3);
*/
void parr(int *a, int n, int m){

    int i, j;
    char *str;
    for (i = 0; i < n; i++){
        for (j = 0; j < m; j++){

            str = (a + i * m + j);
            if (*str == 0) continue;
            printf("%d ", *str);

        }
        printf("\n");
    }

}




#include <stdio.h>
#include <malloc.h>

struct weapon {
	int price;
	int atk;
	struct weapon *next;
};

struct weapon *create() {

	struct weapon *head;
	struct weapon *p1, *p2;
	int n = 0;
	p1 = p2 = (struct weapon *) malloc(sizeof(struct weapon));
	scanf("%d,%d", &p1->price, &p1->atk);
	head = NULL;


};



#include <stdio.h>
#define R "abc"
#define ADD(a, b) (a + b)

typedef int* p;
typedef unsigned long size_t;

p q = NULL;

typedef struct stu{ } stu_t;


struct weapon {

	char name[20];
	int act;
	int price;
};


struct {

	char name[20];
	int act;
	int price;
} var_weapon;


union data {

	int a;
	char b;
	int c;
};



int main(){


	printf("%s\n", R);
	printf("%d\n", ADD(10, 10));

	printf("%lu\n", sizeof(struct weapon));

	
	struct weapon weapon1 = {"weapon_name", 100, 5000};
	struct weapon weapon2[2] = {{"weapon_name_1", 100, 101}, {"weapon_name_2",200, 202}};

	printf("%s \t %s \n", weapon1.name, weapon2[1].name);

	struct weapon *w;
	w = &weapon1;

	printf("%s \t %s \n", (*w).name, w->name);


	w = weapon2;
	printf("%s \n", w->name);
	w++;//as weapon2 + 1 => weapon2[1]
	printf("%s \n", w->name);


	union data data1;
	data1.a = 10;
	data1.b = 'c';

	printf("%p \t %p \n", &data1.a, &data1.c);
	printf("%c \t %c \t %c \n", data1.a, data1.b, data1.c);

	return 0;
}







