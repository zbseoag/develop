import org.junit.jupiter.api.Test;

import java.io.*;
import java.lang.reflect.Array;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.*;
import java.util.concurrent.Executors;
import java.util.concurrent.ScheduledExecutorService;
import java.util.concurrent.TimeUnit;
import java.util.function.*;
import java.util.regex.Matcher;
import java.util.regex.Pattern;
import java.util.stream.Collectors;
import java.util.stream.IntStream;
import java.util.stream.Stream;

/*
assert	断言条件是否满足
default
enum	枚举类型
implements	表示一个类实现了接口
instanceof	测试一个对象是否是某个类的实例
native	表示方法用非java代码实现
strictfp	浮点数比较使用严格的规则
synchronized	表示同一时间只能由一个线程访问的代码块
this	表示调用当前实例
throws	定义方法可能抛出的异常
transient	修饰不要序列化的字段
volatile	标记字段可能会被多个线程同时访问，而不做同步
List<? super Number> 类型通配符是 Nubmer 及其 父类
List<? extends Number> 类型通配符是 Nubmer 及其 子类

*/

public class MainTest {

    public static final  double PI = 3.14; //定义类常量

    //关键字 strictfp：使用严格的浮点计算来生成可再生的结果
    public static strictfp void book(){

        System.exit(0);
    }

    @Test
    public void test(){

        var generator = new Random();
        generator.nextInt(10000);

        final double PI2 = 10; //定义常量
        var a = 10;
        var b = 11;
        var c = 12;
        System.out.println(2 * a++);
        System.out.println(2 * a++);
        System.out.println(2 * ++b);
        System.out.println(a += b += c); //+= 是右结合运算符，所以先算右边的，等同于 a += (b += c)

    }

    @Test
    void main() throws Exception {

        ScheduledExecutorService scheduledThreadPool = Executors.newScheduledThreadPool(5);
        scheduledThreadPool.schedule(new Runnable() {

            @Override
            public void run() {
                System.out.println("delay 3 seconds");
            }
        }, 1, TimeUnit.SECONDS);



        scheduledThreadPool.scheduleAtFixedRate(new Runnable() {

            @Override
            public void run() {
                System.out.println("delay 0 seconds, and excute every 1 seconds");
            }
        }, 0, 1, TimeUnit.SECONDS);

        IntStream.of(1, 2, 3, 4, 5, 6, 7, 8, 9).filter(n -> n % 2 != 0).forEach(System.out::println);

        int sum = Stream.of(1, 2, 3, 4, 5, 6, 7, 8, 9).reduce(0, (acc, n) -> acc + n);
        System.out.println(sum); // 45

        Stream<String> stream = Stream.of("Apple", "", null, "Pear", "  ", "Orange");
        List<String> list = stream.filter(s -> s != null && !s.isBlank()).collect(Collectors.toList());
        System.out.println(list);

    }



    public static <K, V> Map<K, V> call(Integer id, Function<Integer, Map<K, V>> function){

        return function.apply(id);

    }



    @Test
    void callback(){

        Function<String, String> function = new Function<>(){
            @Override
            public String apply(String  input){

                StringBuffer sb = new StringBuffer();
                for (byte b : input.getBytes()) {
                    sb.append((char) (b + 48));
                }

                return sb.toString();
            }
        };

        System.out.println(function.apply("91151561"));


        Map<Integer, String> users = call(10, (id) -> {

            Map<Integer, String> map = new HashMap<>();
            map.put(id, "tom");
            return map;
        });

        System.out.println(users);

        //Consumer 消费型函数式接口,接受一个输入参数且无返回
        Consumer<Integer> consumer = new Consumer<>(){

            @Override
            public void accept(Integer integer){
                System.out.println(integer * 2);
            }
        };

        consumer.accept(3);

        //Predicate断言型函数式,一个输入参数，返回一个布尔值
        Predicate<Integer> predicate = new Predicate<Integer>(){
            @Override
            public boolean test(Integer integer){
                return !integer.equals(0);
            }
        };
        System.out.println(predicate.test(0));

        //Supplier供给型函数式接口, 无参数，返回一个结果,如 object.toString();
        String name = "tom";
        Supplier<Integer> supplier = new Supplier<Integer>(){
            @Override
            public Integer get(){
                return name.length();
            }
        };
        supplier.get();

        //BiFunction 两个输入，一个返回。
        BiFunction<Integer, Integer, Integer> biFunction = (num1, num2) -> num1 + num2;
        System.out.println(biFunction.apply(2, 3));  // 输出: 5

    }

    /*
    Enumeration 枚举接口中定义了一些方法，通过这些方法可以枚举对象集合中的元素。
 */
    @Test
    void Enumeration(){

        Enumeration<String> days;
        Vector<String> dayNames = new Vector<String>();
        dayNames.add("Sunday");
        dayNames.add("Monday");
        days = dayNames.elements();
        while (days.hasMoreElements()){
            System.out.println(days.nextElement());
        }

    }


    /*
    Vector 类实现了一个动态数组。和 ArrayList 很相似而不同的：
    Vector 是同步访问的。包含了许多传统的方法，这些方法不属于集合框架。
    Vector 主要用在事先不知道数组的大小，或者只是需要一个可以改变大小的数组的情况。
    */
    @Test
    void Vector(){

        Vector vector = new Vector(3, 2);
        System.out.println("初始化： size: " + vector.size() + " capacity: " + vector.capacity());

        vector.addElement(Integer.valueOf(1));
        vector.addElement(Integer.valueOf(2));
        vector.addElement(Integer.valueOf(3));
        vector.addElement(Integer.valueOf(4));

        System.out.println("size: " + vector.size() + " capacity: " + vector.capacity());

    }



    /**
     * 位集合 Bitset类创建一种特殊类型的数组来保存位值。BitSet中数组大小会随需要增加。这和位向量（vector of bits）比较类似。
     */

    void BitSet(){

        BitSet bits1 = new BitSet(16);
        BitSet bits2 = new BitSet(16);

        for(int i=0; i<16; i++){

            if((i%2) == 0) bits1.set(i);
            if((i%5) != 0) bits2.set(i);
        }

        System.out.println("Initial pattern in bits1: ");
        System.out.println(bits1);
        System.out.println("\nInitial pattern in bits2: ");
        System.out.println(bits2);

        // AND bits
        bits2.and(bits1);
        System.out.println("\nbits2 AND bits1: ");
        System.out.println(bits2);

    }

    @Test
    void Stack(){

        Stack<Integer> st = new Stack<Integer>();
        st.push(1);
        st.push(2);
        st.pop();

    }


    private void sort(List<String> names){

        Collections.sort(names, new Comparator<String>() {
            @Override
            public int compare(String s1, String s2) {
                return s1.compareTo(s2);
            }
        });

    }

    private void sortJava8(List<String> names){

        Collections.sort(names, (s1, s2) -> s1.compareTo(s2));
    }



    public static byte 		BYTE;
    public static int 		INT;
    public static float 	FLOAT;
    public static char 		CHAR;
    public static String 	STRING;

    public static char[] 	CHARS 	= {'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'};
    public static byte[] 	BYTES 	= {65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80};
    public static String[] 	STRINTS = {"AAA", "BBB", "CCC", "DDD", "EEE", "FFF"};

    public static File FILE 			= new File("d:/data.txt");
    public static String 	INPUT_FILE 		= "d:/input.txt";
    public static String 	OUTPUT_FILE 	= "d:/output.txt";

    public static Date DATE = new Date();

    public static void reset() {

        BYTE 	= 0;
        INT 	= 0;
        FLOAT	= 0.0f;
        CHAR 	= '\u0000';
        STRING 	= null;
    }





    public static void base() throws ParseException, IOException {

        char ch1 = '\u039a';
        char[] ch2 = {'a', 'b', 'c'};
        Character ch3 = 'A';


        System.out.println(Character.hashCode('a'));

        char[] carr = {'h', 'e' , 'l'};
        String str = new String(carr);
        str.concat("append");

        String fs = String.format("%f %d %s", 12.0, 18, "tom");

        String[] arr = new String[0];
        String[] arr2 = {};
        int[] iarr = new int[10];
        iarr[0] = 1;
        iarr[1] = 2;
        System.out.println(iarr);


        System.out.println((new SimpleDateFormat("E yyyy.MM.dd 'at' hh:mm:ss a zzz")).format(DATE));
        System.out.println(String.format("%tc", DATE));
        System.out.printf("%1$s %2$tB %2$td, %2$tY", "printf:", DATE);


        SimpleDateFormat ft = new SimpleDateFormat ("yyyy-MM-dd");
        System.out.println(ft.parse("2020-07-06 12:02:14"));

        long start = System.currentTimeMillis();

        Calendar c1 = Calendar.getInstance();

        c1.set(2009, 6 - 1, 12);//把Calendar对象c1的年月日分别设这为：2009、6、12
        c1.set(Calendar.YEAR,2008);
        c1.add(Calendar.DATE, 10);


        int year = c1.get(Calendar.YEAR);
        int month = c1.get(Calendar.MONTH) + 1;
        int hour = c1.get(Calendar.HOUR_OF_DAY);
        int minute = c1.get(Calendar.MINUTE);
        int day = c1.get(Calendar.DAY_OF_WEEK);// 获得星期几（注意（这个与Date类是不同的）：1代表星期日、2代表星期1、3代表星期二，以此类推）


        GregorianCalendar gcalendar = new GregorianCalendar();
        System.out.println(year = gcalendar.get(Calendar.YEAR));
        gcalendar.isLeapYear(year);//是否闰年


        String string = "This order was placed for QT3000! OK?";
        Pattern pattern = Pattern.compile("(.*)(\\d+)(.*)");

        Matcher matcher = pattern.matcher(string);

        if (matcher.find()) {
            System.out.println("Found: " + matcher.group(0) );
            System.out.println("Found: " + matcher.group(1) );
            System.out.println("Found: " + matcher.group(2) );
        } else {
            System.out.println("NO MATCH");
        }

        //查找位置
        Pattern pattern2 = Pattern.compile("\\bcat\\b");
        Matcher matcher2 = pattern2.matcher("cat cat cat cattie cat");

        int count = 0;
        while(matcher2.find()) {
            count++;
            System.out.printf("匹配第  %d 次  start:%d end:%d", count, matcher2.start(), matcher2.end());
        }


        Pattern pattern3 = Pattern.compile("foo");
        Matcher matcher3 = pattern3.matcher("fooooo");
        System.out.println("fooooo 中 查找 foo = lookingAt(): "+ matcher3.lookingAt());
        System.out.println("fooooo 中 完全匹配 foo = matches(): " + matcher3.matches());


        Pattern p = Pattern.compile("a*b");
        // 获取 matcher 对象
        Matcher m = p.matcher("aabfooaabfooabfoob");
        StringBuffer sb = new StringBuffer();
        while(m.find()){
            m.appendReplacement(sb, "-");
        }
        //print(sb.toString());
        //m.appendTail(sb);
        System.out.println(sb.toString());


        char[] achear = {'a', 'b'};
        System.out.println(achear);


        //从控制台不断读取字符直到用户
	       char c;
	       BufferedReader br = new  BufferedReader(new InputStreamReader(System.in));
	       System.out.print("Enter characters, 'q' to quit.");
	       do {
	          c = (char) br.read();
	          System.out.print(c);
	       } while(c != 'q');


	       System.out.write('Z');
	       System.out.write('\n');

	       BufferedReader bReader = new BufferedReader(new InputStreamReader(System.in));
	       String string2;
	       System.out.print("Enter line of text and enter 'end' to quit.");
	       do {
	    	   str = bReader.readLine();
	    	   System.out.print(str);
	       }while(!str.equals("end"));




    }


    public static void ioStream() throws IOException {


	       ByteArrayInputStream bArray1 = new ByteArrayInputStream(BYTES);
	       ByteArrayInputStream bArray2 = new ByteArrayInputStream(BYTES, 1, 3);

	       while((INT = bArray1.read())!= -1) {System.out.print((char) INT); }
	       reset();


	       ByteArrayOutputStream output = new ByteArrayOutputStream(12);

	       while(output.size()!= 10 ){output.write(System.in.read());}


	       for(byte item : output.toByteArray()){ System.out.print((char) item); }

	       ByteArrayInputStream input = new ByteArrayInputStream(output.toByteArray());

          while((INT = input.read())!= -1){ System.out.print((char) INT);}
          input.reset();

          reset();



		   DataInputStream 	input3 	= new DataInputStream(new FileInputStream(INPUT_FILE));
		   DataOutputStream output3 = new DataOutputStream(new FileOutputStream(OUTPUT_FILE));


			while((STRING = input3.readLine()) != null){

				String u = STRING.toUpperCase();

				System.out.println(u);

				output3.writeBytes(u + "  ,");
			}

			input3.close();
			output3.close();


	       InputStream f1 = new FileInputStream(INPUT_FILE);
	       InputStream f2 = new FileInputStream(new File(INPUT_FILE));

	     OutputStream output10 = new FileOutputStream(OUTPUT_FILE);

	      for(byte item : BYTES){
	    	  output10.write((char) item);
	      }
	      output10.close();


		  reset();
	      InputStream input10 = new FileInputStream(INPUT_FILE);
	      input10.available();

	      while((INT = input10.read()) != -1){
	         System.out.print((char) input10.read());
	      }

	      input10.close();


		FileOutputStream fop = new FileOutputStream(FILE);

		OutputStreamWriter writer = new OutputStreamWriter(fop, "UTF-8");

		writer.append("中文输入"); writer.append("\r\n"); writer.append("English");

		writer.close();
		fop.close();


		FileInputStream fip = new FileInputStream(FILE);
		InputStreamReader reader = new InputStreamReader(fip, "UTF-8");

		StringBuffer sb = new StringBuffer();

		while (reader.ready()) {
			sb.append((char) reader.read());

		}

		System.out.println(sb.toString());

		reader.close();
		fip.close();



		File file = new File("Hello1.txt");

	      file.createNewFile();

	      FileWriter writer2 = new FileWriter(file);

	      // 向文件写入内容
	      String data = "This\nis\nan\nexample\nhehe haha";
	      writer2.write(data);
	      writer2.flush();
	      writer2.close();

	      FileReader fr = new FileReader(file);

	      char[] chars = new char[data.length()];
	      fr.read(chars);

	      for(char c : chars) { System.out.print(c); }

	      fr.close();


        String dirname = "/tmp/user/java/bin";
        File file2 = new File(dirname);
        // 现在创建目录
        file2.mkdirs();
        file2.isDirectory();
        file2.list();


    }

    public static void arrayToCollection() throws IOException{

	      String[] data = {"aaa", "bbb", "ccc"};
	      for(String item : data){
	          System.out.print(item);
	      }

	      List<String> list = Arrays.asList(data);

	      for(String item : list){
	          System.out.print(item);
	      }

    }

    @Test
    void arraycopy(){

        int[] a = {1, 2, 3};
        int newLength = 10;

        Class cl = a.getClass();
        if(!cl.isArray()) System.exit(1);

        Class componentType = cl.getComponentType(); //返回数组类型
        Object newArray = Array.newInstance(componentType, newLength);//创建新数组
        System.arraycopy(a, 0, newArray, 0, Math.min(Array.getLength(a), newLength));//复制元素到新数组

        System.out.println(newArray);

    }



}
