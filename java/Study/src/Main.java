import static java.lang.System.out;

import java.awt.*;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.io.Console;
import java.io.IOException;
import java.io.PrintWriter;
import java.math.BigInteger;
import java.net.URI;
import java.nio.charset.StandardCharsets;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.util.*;
import java.time.*;
import java.util.List;
import java.util.function.BiFunction;
import java.util.stream.Stream;

enum Size { Small, Medium, Large};

public class Main {

    public static final  double PI = 3.14; //定义类常量

    public static <E> void p(E... args){
        for(E i:args)  out.println(i);
    }

    //strictfp：使用严格的浮点计算来生成可再生的结果
    public static strictfp void book(){

        System.exit(0);
    }

    public static void lang(){

        final double PI2 = 10; //定义常量
        var a = 10;
        var b = 11;
        var c = 12;
        p(2 * a++, 2 * ++b);
        p(a += b += c); //+= 是右结合运算符，所以先算右边的，等同于 a += (b += c)

    }

    public static void string() throws IOException {
        String greet = "ABCDEF";
        p(greet.substring(2, 3));
        p(String.join(",", "a", "b", "c"));
        p("java|".repeat(3));
        p("hello".equalsIgnoreCase("HELLO"));
        p(greet.substring(2, 3) == "C"); //false
        p(greet.compareTo("ABC"));
        p(greet.codePointCount(0, greet.length()));

        p(" ".isEmpty(), " ".isBlank()); //空格不为空
        p("a".startsWith("bcd")); //是否以某个字符串开头
        p("abc".replace("a", "c"));

//        Scanner in = new Scanner(System.in);
//        System.out.print("what is your name? :");
//        String name = in.nextLine();
//        String firstname = in.next();
//        p(name, firstname);

//        Console con = System.console();
//        String name2 = con.readLine("your name :");
//        char[] passwd = con.readPassword("you passwd :");
        out.printf("%tc", new Date());

            //文件读写
//        Scanner in2 = new Scanner(Path.of("myfile.txt"), StandardCharsets.UTF_8);
//        PrintWriter out2 = new PrintWriter("myfile.txt", StandardCharsets.UTF_8);
//        p(System.getProperty("user.dir"));

        //javac -Xlint:fallthrough Test.java
//        Lab:
//        break Lab; //只能跳出语句块，还无法跳入。
        BigInteger aaa = new BigInteger("123456");
        aaa.add(new BigInteger("10"));
        p(BigInteger.ONE, BigInteger.valueOf(1 + 2 + 3));


    }


    public static void array(){

        int[] a = {1,3,4,5};
        int[] b = new int[10];
        var c = new int[10];
        int[] d = new int[]{45};
        p(Arrays.toString(b));
        for(int item : a) p(item);
        Arrays.sort(a);
        a = Arrays.copyOf(a, 2 * a.length);//一般用于数组扩容
        p(a);
        int r = (int) (Math.random() * 10);
        double[][] balan = {{1,2,3}, {4,5,6}};


        final int NMAX = 10;
        int[][] odds = new int[NMAX+1][];
        for(int n = 0; n <= NMAX; n++) odds[n] = new int[n+1];
        for(int n = 0; n < odds.length; n++){
            for(int k=0; k<odds[n].length; k++){

                int lottOdds = 1;
                for(int i = 1; i <= k; i++) lottOdds = lottOdds * (n - i +1) / i;
                odds[n][k]=lottOdds;

            }
        }

        for(int[] row:odds){
            for(int odd :row){
                out.printf("%4d", odd);
            }
            out.println();
        }




    }

    public static void cls(){
        //jdeprscan 工具

        LocalDate a = LocalDate.now();
        p(a.getYear());

        LocalDate b = a.plusDays(400);
        p(b.getYear());

        p(Objects.requireNonNullElse(null, "unknow"));
        //Objects.requireNonNull(null, "不允许为 null");


    }

    public static void stream() throws IOException {

        var content = new String(Files.readAllBytes(Paths.get("alice.txt")), StandardCharsets.UTF_8 );
        List<String> words = List.of(content.split("\\PL+"));

        long count = 0;
        for(String w : words){
            if(w.length() > 12){
                count++;
            }
        }

        count = words.stream().filter(w -> w.length() > 12).count();
        count = words.parallelStream().filter(w -> w.length() > 12).count();

        Stream<String> words2 = Stream.of(content.split("\\PL+"));
        Stream<String> song = Stream.of("aaa", "bbb");
        Stream<String> enpty = Stream.empty();

    }

    public static void interfaces(){

        /*  为什么不直接提供 compareTo 方法，而要去实现 Comparable 接口呢？
        因为Java 是强类型语言，遇到 a.compareTo(b)  需要明确对象方法确实存在。*/

        interface Aintface{
            public static void putout(){ System.out.print(11);   }
            default int isEmpty(){ return 0; } //默认实现方法
            default int isEmptys(){ return 0; } //默认实现方法

        }

        class Employee implements Comparable<Employee>, Aintface{

            public int salary = 0;
            @Override
            public int compareTo(Employee o) {
                //compareTo 应当和 equals 方法兼容,除非没有明确方法能确定哪个数更大
                return Double.compare(salary, o.salary);
            }

            //当实现的多个接口中，有相当的 default 方法，则必须解决冲突。
            //超类优先级大于接口，所以它们之间不会有同名的方法冲突
            public int isEmpty(){
               return  Aintface.super.isEmpty();
            }
        }
        Employee a = new Employee();
        Aintface.putout();


        class TimePrint implements ActionListener{

            @Override
            public void actionPerformed(ActionEvent e) {
                System.out.println(Instant.ofEpochMilli(e.getWhen()));
                Toolkit.getDefaultToolkit().beep();
            }
        }

        class LengthComparator implements Comparator<String>{

            @Override
            public int compare(String o1, String o2) {
                return o1.length() - o2.length();
            }
        }

        String[] friends = {"peter", "paul", "Mary"};
        Arrays.sort(friends, new LengthComparator());

        class Emp implements Cloneable{

            public Emp clone() throws CloneNotSupportedException{
                return (Emp) super.clone();
            }
        }

//        Java 中有很多封装代码块的接口，如：ActionListener、Comparator，而 lambda 表达式与之兼容。
//        对于只有一个抽象方法的接口，需要接口对象时，可以提供一个 lambda 表达式。这种只有一个抽象方法的接口被称为函数式接口。
        Comparator<String> comp = (first, second) -> first.length() - second.length();
        ActionListener listen = event-> out.println(111);
        Arrays.sort(friends, (first, second) -> first.length() - second.length());

        //在 java.util.function 包中定义了很多非常通用的函数式接口。
        BiFunction<String, String, Integer> c = (first, second) -> first.length() - second.length();


        //方法引用
       // var t = new Timer(100, event -> System.out::println);
        //list.removeIf(e -> e == null);  list.removeIf(Objects::isNull);


    }


    public static void main(String[] args) throws IOException {

        interfaces();

    }




}




