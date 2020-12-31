import static java.lang.System.out;

import java.io.Console;
import java.io.IOException;
import java.io.PrintWriter;
import java.math.BigInteger;
import java.nio.charset.StandardCharsets;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.time.LocalDate;
import java.util.*;
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

    public static void liu() throws IOException {

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


    public static void main(String[] args) throws IOException {

        cls();

    }




}

