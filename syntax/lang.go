package main //表示一个可独立执行的程序，每个 Go 应用程序都包含一个名为 main 的包

import "fmt"

var a bool
var b *int
var c []int
var d map[string] int
var e chan int
var f func(string) int
var g error //error 接口

var v_name = 20 //自动判定类型


// 因式分解关键字的写法一般用于声明全局变量，全局变量是允许声明但不使用
var (
    vname1 int = 21
    vname2 string
)

_, bbb = 5, 7 // 值 5 被抛弃，_ 实际上是一个只写变量，你不能得到它的值

const WIDTH float = 3.14

const (
    a = "abc"
    b = len(a) //函数必须是内置函数，否则编译不过
    Male = 2
)

const (
	a = iota   //0
	b          //1
	c          //2
	d = "ha"   //独立值，iota + 1 = 3
	e          //"ha"   iota + 1 = 4
	f = 100    //iota + 1 = 5
	g          //100  iota + 1 = 6
	h = iota   //7,恢复计数
	i          //8

)



func init(){
	
	intVal,intVal1 := 1,2 //注意 := 初始化声明，且必须放在函数体内

	fmt.Println("init")
}


func main(){

	fmt.Println("hellow")
}

//一个可以返回多个值的函数
func numbers()(int,int,string){
	a , b , c := 1 , 2 , "str"
	return a,b,c
}


/* 定义交换值函数*/
//  swap(&a, &b)
func swap(x *int, y *int) {
	var temp int
	temp = *x    /* 保持 x 地址上的值 */
	*x = *y      /* 将 y 值赋给 x */
	*y = temp    /* 将 temp 值赋给 y */
 }

 //函数 getSequence() ，返回另外一个函数。该函数的目的是在闭包中递增 i 变量
 /*
nextNumber := getSequence()
fmt.Println(nextNumber())
fmt.Println(nextNumber())
fmt.Println(nextNumber())
 */
 func getSequence() func() int{
	i:=0
	return func() int {
	   i+=1
	  return i  
	}
 }


 var balance = [5]float32{1000.0, 2.0, 3.4, 7.0, 50.0}
 var balance = [...]float32{1000.0, 2.0, 3.4, 7.0, 50.0}

 a = [3][4]int{  
	{0, 1, 2, 3} ,   /*  第一行索引为 0 */
	{4, 5, 6, 7} ,   /*  第二行索引为 1 */
	{8, 9, 10, 11},   /* 第三行索引为 2 */
   }

func aaa(param []int){

}

a := []int{10,100,200}
var i int
var ptr [3]*int;

for  i = 0; i < MAX; i++ {
   ptr[i] = &a[i] /* 整数地址赋值给指针数组 */
}

for  i = 0; i < MAX; i++ {
   fmt.Printf("a[%d] = %d\n", i,*ptr[i] )
}





/* 定义结构体 */
type Circle struct {
	radius float64
  }
  
  func main() {
	var c1 Circle
	c1.radius = 10.00
	fmt.Println("圆的面积 = ", c1.getArea())
  }
  
  //该 method 属于 Circle 类型对象中的方法
  func (c Circle) getArea() float64 {
	//c.radius 即为 Circle 类型对象中的属性
	return 3.14 * c.radius * c.radius
  }


      //range也可以用在map的键值对上。
	  kvs := map[string]string{"a": "apple", "b": "banana"}
	  for k, v := range kvs {
		  fmt.Printf("%s -> %s\n", k, v)
	  }


	  var countryCapitalMap map[string]string /*创建集合 */
	  countryCapitalMap = make(map[string]string)
  
	  /* map插入key - value对,各个国家对应的首都 */
	  countryCapitalMap [ "France" ] = "巴黎"
	  countryCapitalMap [ "Italy" ] = "罗马"
	  countryCapitalMap [ "Japan" ] = "东京"
	  countryCapitalMap [ "India " ] = "新德里"
  
	  /*使用键输出地图值 */
	  for country := range countryCapitalMap {
		  fmt.Println(country, "首都是", countryCapitalMap [country])
	  }
  


	import (
		"fmt"
	)
	
	type Phone interface {
		call()
	}

	type IPhone struct {
	}

	type NokiaPhone struct {
	}

	func (iPhone IPhone) call() {
		fmt.Println("I am iPhone, I can call you!")
	}
	
	func (nokiaPhone NokiaPhone) call() {
		fmt.Println("I am Nokia, I can call you!")
	}
	

	func main() {
		var phone Phone
	
		phone = new(NokiaPhone)
		phone.call()
	
		phone = new(IPhone)
		phone.call()
	
	}



	package main

import (
	"fmt"
	"time"
)

func main() {

	test3()

}

func test(){
	
}



//并发
func test1(){

	say := func(msg string){

		for i := 0; i < 5; i++{
			time.Sleep(100 * time.Microsecond)
			fmt.Println(msg)
		}

	}

	go say("hello")
	say("world")



}

//通道（channel）:是用来传递数据的一个数据结构
func test2(){

	sum := func(s []int, c chan int) {
		sum := 0
		for _, v := range s{
			sum += v
		}
		c <- sum // 把 sum 发送到通道 c
	}

	s := []int{7, 2, 8, -9, 4, 6}
	c := make(chan int)
	go sum(s[len(s) / 2:], c)
	go sum(s[:len(s) / 2], c)

	x, y := <-c, <-c
	fmt.Println(x, y)
}

//通道缓冲区
func test3(){

	//定义了一个可以存储整数类型的带缓冲通道,缓冲区大小为2
	ch := make(chan int, 2)

	// 因为 ch 是带缓冲的通道，我们可以同时发送两个数据,而不用立刻需要去同步读取数据
	ch <- 1 //向通道发送数据
	ch <- 2

	//获取这两个数据
	fmt.Println(<-ch, <-ch)

}

//遍历通道
func test4(){

	fibonaci := func(n int, c chan int) {

		x, y := 0, 1
		for i := 0; i < n; i++ {
			c <- x
			x, y = y, x + y

		}
		close(c) //关闭通道

	}

	c := make(chan int, 10)
	go fibonaci(cap(c), c)

	// range 函数遍历每个从通道接收到的数据，因为 c 在发送完 10 个
	// 数据之后就关闭了通道，所以这里我们 range 函数在接收到 10 个数据
	// 之后就结束了。如果上面的 c 通道不关闭，那么 range 函数就不
	// 会结束，从而在接收第 11 个数据的时候就阻塞了。

	for i := range c{
		fmt.Println(i)
	}

}




	