import bean.ReverseList;
import bean.Student;
import bean.User;
import bean.UserComparator;
import interfaces.Aintface;
import org.junit.jupiter.api.Test;

import java.awt.*;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.io.Console;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.PrintWriter;
import java.math.BigInteger;
import java.nio.charset.StandardCharsets;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.time.DayOfWeek;
import java.time.Instant;
import java.time.LocalDate;
import java.util.List;
import java.util.Queue;
import java.util.*;
import java.util.function.BiFunction;
import java.util.stream.Stream;

import static java.lang.System.out;

public class ClassTest{

    public static void string() throws IOException{

        String greet = "ABCDEF";
        System.out.println(greet.substring(2, 3));
        System.out.println(String.join(",", "a", "b", "c"));
        System.out.println("java|".repeat(3));
        System.out.println("hello".equalsIgnoreCase("HELLO"));
        System.out.println(greet.substring(2, 3) == "C"); //false
        System.out.println(greet.compareTo("ABC"));
        System.out.println(greet.codePointCount(0, greet.length()));

        System.out.println(" ".isEmpty()); //空格不为空
        System.out.println(" ".isBlank()); //空格不为空
        System.out.println("a".startsWith("bcd")); //是否以某个字符串开头
        System.out.println("abc".replace("a", "c"));

        Scanner in = new Scanner(System.in);
        out.print("what is your name? :");
        String name = in.nextLine();
        String firstname = in.next();
        System.out.println(name);
        System.out.println(firstname);

        Console con = System.console();
        String name2 = con.readLine("your name :");
        char[] passwd = con.readPassword("you passwd :");
        out.printf("%tc", new Date());

        //文件读写
        Scanner in2 = new Scanner(Path.of("myfile.txt"), StandardCharsets.UTF_8);
        PrintWriter out2 = new PrintWriter("myfile.txt", StandardCharsets.UTF_8);
        System.out.println(System.getProperty("user.dir"));

        //javac -Xlint:fallthrough Test.java
//        Lab:
//        break Lab; //只能跳出语句块，还无法跳入。
        BigInteger aaa = new BigInteger("123456");
        aaa.add(new BigInteger("10"));
        System.out.println(BigInteger.ONE);
        out.println(BigInteger.valueOf(1 + 2 + 3));


    }


    public static void array(){

        int[] a = {1,3,4,5};
        int[] b = new int[10];
        var c = new int[10];
        int[] d = new int[]{45};
        System.out.println(Arrays.toString(b));
        for(int item : a) System.out.println(item);
        Arrays.sort(a);
        a = Arrays.copyOf(a, 2 * a.length);//一般用于数组扩容
        System.out.println(a);
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
            System.out.println();
        }


    }

    @Test
    public void LocalDate(){

        //jdeprscan 工具
        LocalDate a = LocalDate.now();
        System.out.println(a.getYear());

        LocalDate b = a.plusDays(400);
        System.out.println(b.getYear());

        System.out.println(Objects.requireNonNullElse(null, "unknow"));
        //Objects.requireNonNull(null, "不允许为 null");

    }

    @Test
    public void stream() throws IOException {

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

    //与 ArrayList 相比，LinkedList 的增加和删除对操作效率更高，而查找和修改的操作效率较低。
    //使用 ArrayList 的情况，频繁访问列表中的某一个元素，只需要在列表末尾进行添加和删除元素操作。
    //以下情况使用 LinkedList :
    //你需要通过循环迭代来访问列表中的某些元素。
    //需要频繁的在列表开头、中间、末尾等位置进行添加和删除元素操作。

    @Test
    public void LinkedList(){

        LinkedList<Integer> list = new LinkedList<>();
        list.addFirst(0);
        list.add(1);//追加
        list.add(0, 2);//插入到指定位置
        list.addLast(3);
        list.add(5);
        list.set(1, 3);//修改

        list.contains(3);//是否包含指定元素
        list.indexOf(3); //返回元素首次出现的位置
        list.lastIndexOf(3);  //返回元素最后出现的位置


        list.peek(); //返回当前元素
        list.element();//返回当前元素，带异常 NoSuchElementException
        list.poll();//弹出当前元素
        list.remove(); //弹出当前元素，带异常 NoSuchElementException
        list.offer(4); // 将指定元素添加到此列表的末尾

        list.removeFirstOccurrence(2);
        System.out.println(list);
        list.clear();

        System.out.println(list.getFirst());
        System.out.println();

    }


    @Test
    public void interfaces(){

        /*  为什么不直接提供 compareTo 方法，而要去实现 Comparable 接口呢？因为Java 是强类型语言，遇到 a.compareTo(b)  需要明确对象方法确实存在。*/
        class Employee implements Comparable<Employee>, Aintface {

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
        Aintface.print();



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
        ActionListener listen = event-> System.out.println(111);
        Arrays.sort(friends, (first, second) -> first.length() - second.length());

        //在 java.util.function 包中定义了很多非常通用的函数式接口。
        BiFunction<String, String, Integer> c = (first, second) -> first.length() - second.length();


        //方法引用
        // var t = new Timer(100, event -> System.out::println);
        //list.removeIf(e -> e == null);  list.removeIf(Objects::isNull);

    }


    @Test
    public void Hashtable(){

        Hashtable balance = new Hashtable();
        Enumeration names;
        String str;
        double bal;
        balance.put("Zara", 3434.34);
        balance.put("Mahnaz", 123.22);
        balance.put("Ayan", 1378.00);
        balance.put("Daisy", 99.22);
        balance.put("Qadir", -19.08);

        names = balance.keys();
        while(names.hasMoreElements()) {
            str = (String) names.nextElement();
            System.out.println(str + ": " + balance.get(str));
        }
        System.out.println();

        bal = ((Double)balance.get("Zara")).doubleValue();
        balance.put("Zara", bal + 1000);
        System.out.println("Zara's new balance: " + balance.get("Zara"));

    }


    @Test
    public void Properties(){

        Properties capitals = new Properties();
        Set states;
        String str;

        capitals.put("Illinois", "Springfield");
        capitals.put("Missouri", "Jefferson City");
        capitals.put("Washington", "Olympia");
        capitals.put("California", "Sacramento");
        capitals.put("Indiana", "Indianapolis");

        // Show all states and capitals in hashtable.
        states = capitals.keySet();
        Iterator itr = states.iterator();

        while(itr.hasNext()) {
            str = (String) itr.next();
            System.out.println("The capital of " + str + " is " + capitals.getProperty(str) + ".");
        }
        System.out.println();

        str = capitals.getProperty("Florida", "Not Found");
        System.out.println("The capital of Florida is " + str + ".");

    }

    @Test
    public void ArrayList(){

        List<String> list = new ArrayList<>();
        list.add("hello");
        list.add("world");
        for(String str : list){
            System.out.println(str);
        }

        //第二种遍历，把链表变为数组相关的内容进行遍历
        String[] arr1 = new String[list.size()];
        list.toArray(arr1);
        for(int i = 0; i < arr1.length; i++){
            System.out.println(arr1[i]);
        }

        Iterator<String> item=list.iterator();
        while(item.hasNext()){
            System.out.println(item.next());
        }

    }

    @Test
    public void HashMap(){

        Map<String, String> map = new HashMap<String, String>();
        map.put("1", "value1");
        map.put("2", "value2");
        map.put("3", "value3");

        //第一种：普遍使用，二次取值
        System.out.println("通过Map.keySet遍历key和value：");
        for (String key : map.keySet()) {
            System.out.println("key= "+ key + " and value= " + map.get(key));
        }

        //第二种
        System.out.println("通过Map.entrySet使用iterator遍历key和value：");
        Iterator<Map.Entry<String, String>> it = map.entrySet().iterator();
        while (it.hasNext()) {
            Map.Entry<String, String> entry = it.next();
            System.out.println("key= " + entry.getKey() + " and value= " + entry.getValue());
        }

        //第三种：推荐，尤其是容量大时
        System.out.println("通过Map.entrySet遍历key和value");
        for (Map.Entry<String, String> entry : map.entrySet()) {
            System.out.println("key= " + entry.getKey() + " and value= " + entry.getValue());
        }

        //第四种
        System.out.println("通过Map.values()遍历所有的value，但不能遍历key");
        for (String v : map.values()) {
            System.out.println("value= " + v);
        }
    }

    @Test
    public void HashSet(){

        HashSet<String> sites = new HashSet<String>();
        sites.add("Google");
        sites.add("Runoob");
        sites.add("Zhihu");
        sites.add("Runoob");  // 重复的元素不会被添加
        System.out.println(sites);

    }


    @Test
    public void iterator() {

        ArrayList<Integer> numbers = new ArrayList<Integer>();
        numbers.add(12);
        numbers.add(8);
        numbers.add(23);

        Iterator<Integer> it = numbers.iterator();
        while(it.hasNext()) {
            Integer i = it.next();
            if(i < 10) {
                it.remove();  // 删除小于 10 的元素
            }
        }
        System.out.println(numbers);

        List.of("aaa");

    }

    @Test
    public void it(){

        List<String> list = List.of("apple", "pear", "banana");
        for (Iterator<String> it = list.iterator(); it.hasNext(); ) {
            System.out.println(it.next());
        }

        Integer[] array = list.toArray(new Integer[3]);
        for (Integer n : array) {
            System.out.println(n);
        }

        Integer[] array2 = list.toArray(Integer[]::new);

        //List.of() 转换数组得到的 list 是一个只读的 List,不能添加或删除元素
        List<Integer> list2 = List.of(array);

    }


    @Test
    public void hd(){

        Map<String, Integer> map = new HashMap<>();
        map.put("apple", 123);
        map.put("pear", 456);
        map.put("banana", 789);

        for (String key : map.keySet()) {
            Integer value = map.get(key);
            System.out.println(key + " = " + value);
        }


        for (Map.Entry<String, Integer> entry : map.entrySet()){
            String key = entry.getKey();
            Integer value = entry.getValue();

            System.out.println(key + " = " + value);

        }

    }

    @Test
    public void EnumMap() {

        Map<DayOfWeek, String> map = new EnumMap<>(DayOfWeek.class);
        map.put(DayOfWeek.MONDAY, "星期一");
        map.put(DayOfWeek.TUESDAY, "星期二");
        map.put(DayOfWeek.WEDNESDAY, "星期三");
        System.out.println(map);
        System.out.println(map.get(DayOfWeek.MONDAY));

    }

    @Test
    public void TreeMap(){
        Map<String, Integer> map = new TreeMap<>();
        map.put("orange", 1);
        map.put("apple", 2);
        map.put("pear", 3);
        for (String key : map.keySet()) {
            System.out.println(key);
        }

        //使用TreeMap时，放入的Key必须实现Comparable接口
        Map<Student, Integer> map2 = new TreeMap<>(new Comparator<Student>() {
            // 根据分类排序
            public int compare(Student p1, Student p2) {
                return Integer.compare(p1.score, p2.score);
            }

        });
        map2.put(new Student("Tom", 77), 1);
        map2.put(new Student("Bob", 66), 2);
        map2.put(new Student("Lily", 99), 3);
        for (Student key : map2.keySet()) {
            System.out.println(key);
        }

        System.out.println(map2.get(new Student("Bob", 66))); // null?


    }

    @Test
    public void PropertiesSave() throws IOException{

        Properties props = new Properties();
        props.setProperty("language", "Java");
        //保存配置
        props.store(new FileOutputStream("e:\\aaa.properties"), "数据库配置");


    }

    @Test
    public void queue(){

        Queue<String> q = new LinkedList<>();
        // 添加3个元素到队列:
        q.offer("apple");
        q.offer("pear");
        q.offer("banana");
        // 队首永远都是apple，因为peek()不会删除它:
        System.out.println(q.peek()); // apple
        System.out.println(q.peek()); // apple
        System.out.println(q.peek()); // apple

        //LinkedList即实现了List接口，又实现了Queue接口

        // 这是一个List:
        List<String> list = new LinkedList<>();
        // 这是一个Queue:
        Queue<String> queue = new LinkedList<>();

    }

    /*
    优先级队列
     */
    @Test
    public void PriorityQueue(){

        Queue<User> q = new PriorityQueue<>(new UserComparator());
        // 添加3个元素到队列:
        q.offer(new User("Bob", "A1"));
        q.offer(new User("Alice", "A2"));
        q.offer(new User("Boss", "V1"));
        System.out.println(q.poll()); // Boss/V1
        System.out.println(q.poll()); // Bob/A1
        System.out.println(q.poll()); // Alice/A2

    }

    /**
     * 双端队列：两端都能进能出
     * Deque是一个接口，它的实现类有ArrayDeque和LinkedList
     * LinkedList 即是List，又是Queue，还是Deque。
     */
    @Test
    public void Deque(){

        Deque <String> deque = new LinkedList<>();
        deque.offerLast("a");
        deque.offerLast("b");
        deque.offerFirst("c");
        System.out.println(deque);

    }


    /**
     * 在Java中，我们用Deque可以实现Stack的功能：
     * 把元素压栈：push(E)/addFirst(E)；
     * 把栈顶的元素“弹出”：pop(E)/removeFirst()；
     * 取栈顶元素但不弹出：peek(E)/peekFirst()。
     * 为什么Java的集合类没有单独的Stack接口呢？因为有个遗留类名字就叫Stack，出于兼容性考虑，所以没办法创建Stack接口，只能用Deque接口来“模拟”一个Stack了。
     *
     * 当我们把Deque作为Stack使用时，注意只调用push()/pop()/peek()方法，不要调用addFirst()/removeFirst()/peekFirst()方法，这样代码更加清晰。
     *
     */


    /**
     * 迭代器
     */
    @Test
    public void Iterable() {
        ReverseList<String> rlist = new ReverseList<>();
        rlist.add("Apple");
        rlist.add("Orange");
        rlist.add("Pear");
        for (String s : rlist) {
            System.out.println(s);
        }
    }

    @Test
    public void Collections(){

        List<String> list = List.of();
        List<String> list2 = Collections.emptyList();
        List<String> list3 = Collections.singletonList("apple");

        Collections.sort(list);
        Collections.shuffle(list);

        // 变为不可变集合:
        List<String> immutable = Collections.unmodifiableList(list);

        //继续对原始的可变List进行增删是可以的，并且会影响到封装后的 “不可变”List
        list.add("orange");


    }


}


