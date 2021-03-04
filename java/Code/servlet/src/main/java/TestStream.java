import java.io.*;
import java.math.BigInteger;
import java.nio.MappedByteBuffer;
import java.nio.channels.FileChannel;
import java.nio.charset.StandardCharsets;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.util.*;
import java.util.function.Function;
import java.util.logging.Logger;
import java.util.regex.Matcher;
import java.util.regex.Pattern;
import java.util.stream.Collectors;
import java.util.stream.Stream;
import java.util.stream.StreamSupport;
import java.util.zip.CRC32;

public class TestStream extends Common {

    public static String file = "alice.txt";
    public static String content;
    public static Stream<String> stream;

    {
        try {
            content = new String(Files.readAllBytes(Paths.get(file)), StandardCharsets.UTF_8);
            stream  = Stream.of("gently", "down", "the", "stream");
        } catch (IOException e) {
            e.printStackTrace();
        }
    }


    public static void main(String[] args) throws IOException, IllegalAccessException {
        //create();
        print();
    }

    public static void start() throws IOException {

        List<String> words = List.of(content.split("\\PL+"));

        int use = 1;
        if(use == 1){
            int count = 0;
            for(String w: words){
                if(w.length() > 12) count++;
            }

        }else if(use == 2){
            long count = words.stream().filter(w -> w.length() > 12).count();

        }else if(use == 3){
            //并行方式执行
            long count = words.parallelStream().filter(w -> w.length() > 12).count();
        }

    }

    public static Stream<String> codePoints(String s){
        var result = new ArrayList<String>();
        int i = 0;
        while(i < s.length()){
            int j = s.offsetByCodePoints(i, 1);
            result.add(s.substring(i, j));
            i = j;
        }
        return result.stream();

    }
    public static void create() throws IOException, IllegalAccessException {


        stream = Stream.empty();
        stream = Arrays.stream(new String[]{"aaa", "bbb", "ccc"}, 1, 3);

        stream.map(String::toLowerCase).map(s -> s.substring(0, 1));
        stream.flatMap(w -> codePoints(w));

        p(Stream.generate(() -> "TEST").count());
        p(Stream.generate(Math::random));

        Stream<BigInteger> biginteger;
        biginteger = Stream.iterate(BigInteger.ZERO, n -> n.add(BigInteger.ONE));
        var limit = new BigInteger("100");
        biginteger = Stream.iterate(BigInteger.ZERO, n -> n.compareTo(limit) < 0, n -> n.add(BigInteger.ONE));

        stream = Pattern.compile("\\PL+").splitAsStream(content);
        stream = new Scanner(content).tokens();

        try(Stream<String> stream2 = Files.lines(Paths.get(file))){ }

        //如果 Iterable 对象不是集合，使用下面方法转成流
        //StreamSupport.stream(iterable.spliterator(), false);
        //StreamSupport.stream(Spliterators.spliteratorUnknownSize(iterator, Spliterator.ORDERED), false);

        Stream.generate(Math::random).limit(100);
        Stream.of(content.split("\\PL+")).skip(1);
        stream.takeWhile(s -> "123".contains(s));
        stream.dropWhile(s -> s.trim().length() == 0);
        Stream.concat(stream, stream);

        Stream.of("aaa", "bbb", "ccc", "aaa").distinct();
        stream.sorted(Comparator.comparing(String::length).reversed());
        Object[] powers = Stream.iterate(1.0, p -> p *2).peek(e -> System.out.println("Fetching " + e)).limit(20).toArray();

        Optional<String> optional = stream.max(String::compareToIgnoreCase);
        p(optional.orElse("空"));

//        optional = stream.filter(s -> startWith("Q")).findFirst();
//        optional = stream.filter(s -> startWith("Q")).findAny();

     //   p(stream.parallel().anyMatch(s -> startWith("Q")));

        optional.orElseGet(() -> System.getProperty("myapp.default"));
        optional.orElseThrow(IllegalAccessException::new);

        List<String> result = new ArrayList<>();
        optional.ifPresent(v -> result.add(v));
        Logger logger = Logger.getLogger("debug");
        optional.ifPresentOrElse(result::add, () -> logger.warning("No match"));

        inverse(10.2).flatMap(TestStream::squareRoot);
        Optional.of(-4.0).flatMap(TestStream::inverse).flatMap(TestStream::squareRoot);

//        stream.map(x -> x.length() < 0).flatMap(Optional::stream);
        stream.filter(Objects::nonNull);

//        stream.map(User::classLookup).flatMap(Stream::ofNullable);
//        stream.flatMap(id -> Stream.ofNullable(User.classLookup(id)));

    }

    public static Optional<Double> inverse(Double x){
        return x == 0 ? Optional.empty() : Optional.of(1 / x);
    }

    public static Optional<Double> squareRoot(Double x){
        return x < 0 ? Optional.empty() : Optional.of(Math.sqrt(x));
    }


    public static void print(){

        stream  = Stream.of("gently", "down", "the", "stream");
        //stream.forEach(System.out::println);
        //stream.forEachOrdered(System.out::println);
        p(stream.toArray(String[]::new));
        stream.collect(Collectors.toList());
        stream.collect(Collectors.toSet());
        stream.collect(Collectors.toCollection(TreeSet::new));
        stream.collect(Collectors.joining(","));
        IntSummaryStatistics summary = stream.collect(Collectors.summarizingInt(String::length));
        summary.getAverage();
        summary.getMax();


    }

}


class CollectionIntoMaps extends Common  {

    public static class Person {
        private int id;
        private String name;

        public Person(int id, String name){
            this.id = id;
            this.name = name;
        }

        public int getId(){
            return id;
        }
        public String getName(){
            return name;
        }
        @Override
        public String toString(){
            return getClass().getName() + "[id=" + id + ", name=" + name + "]";
        }

        public static Stream<Person> people(){
            return Stream.of(new Person(1001, "Peteer"), new Person(1002, "Paul"), new Person(1003, "Mary"));
        }

        public static void main(String[] args) throws IOException {
            Map<Integer, String> idToName = people().collect(Collectors.toMap(Person::getId, Person::getName));
            p(idToName);

            Map<Integer, Person> idToPerson = people().collect(Collectors.toMap(Person::getId, Function.identity()));
            p("idToPerson:" + idToPerson );

            idToPerson = people().collect(Collectors.toMap(Person::getId, Function.identity(), (existingVal, newval) -> { throw new IllegalStateException();}, TreeMap::new));

            Stream<Locale> locales = Stream.of(Locale.getAvailableLocales());
            Map<String, String> languageNames = locales.collect(Collectors.toMap(Locale::getDisplayLanguage, l -> l.getDisplayLanguage(l), (exisval, newval) -> exisval));
            p(languageNames);

            locales = Stream.of(Locale.getAvailableLocales());
            Map<String, Set<String>> countryLanguageSets = locales.collect(Collectors.toMap(Locale::getDisplayCountry, l -> Set.of(l.getDisplayLanguage()), (a, b) -> {Set<String> union = new HashSet<>(a); union.addAll(b); return union;}));
            p(countryLanguageSets);

            System.getProperty("user.dir");

            String[] items = Pattern.compile(":").split("one:two:three");
            for(String s : items){
                p(s);
            }

            String[] word = "this is a test 1 3 4".split("\\s+");
            for(String w : word){
                p(w);
            }

            if("aaa".matches(".@.")){

            }

            Matcher m = Pattern.compile("\\b(\\d{3})\\d{7}\\b").matcher("1111111111, 1111111, and 1111111111");
            while(m.find()){
                String phone = m.group();
                String areacode = m.group(1);
            }

           m =  Pattern.compile("\\b(\\d{3})(\\d{3})(\\d{4})\\b").matcher("1111111111, 1111111, and 1111111111");
           p(m.replaceAll("($1) $2-$3"));

           String[] input = new Scanner(System.in).nextLine().split("\\w+");
           int count = 0;
           for(int i = 0; i < input.length; i++){
               count++;
           }
           p(count);

           TreeSet<String> ts = new TreeSet<>();
            m = Pattern.compile("[A-Za-z_.]{1,}@[a-z.]*[a-z]{1,3}").matcher("abc@email.com");
            if(m.find()){
                ts.add(m.group());
            }
            Iterator<String> it = ts.iterator();
            while(it.hasNext()){
                if(it.hasNext()){
                    p(";");
                }else{
                    p(" ");
                }
            }

            byte[] buf = new byte[]{0x12, 0x23};


        }



    }



}


class ObjectStream extends Common {

    public static void main(String[] args) throws IOException, ClassNotFoundException {

        var harry = new Employee("Harry Hacker", 5000, "1989-02-15");
        var carl = new Manger("Carl Cracker", 8000, "1985-05-15");
        carl.setSecretary(harry);
        var staff = new Employee[]{carl};

        try(var out = new ObjectOutputStream(new FileOutputStream("employee.txt"))){
            out.writeObject(staff);
        }
        try(var in = new ObjectInputStream(new FileInputStream("employee.txt"))){
            var newStaff = (Employee[]) in.readObject();

            for(var item : newStaff){
                p(item);
            }
        }

    }

}

class SeialCloneable implements Cloneable, Serializable {

    public Object clone() throws CloneNotSupportedException{
        try {
            var bout = new ByteArrayOutputStream();
            try(var out = new ObjectOutputStream(bout)){
                out.writeObject(this);
            }
            try(var bin = new ByteArrayInputStream(bout.toByteArray())){
                var in = new ObjectInputStream(bin);
                return in.readObject();
            }
        }catch(IOException | ClassNotFoundException e){
            var e2 = new CloneNotSupportedException();
            e2.initCause(e);
            throw e2;
        }

    }
}

class MemoryMapTest{

    public static long checksumInputStream(Path fileName) throws IOException{
        try(InputStream in = Files.newInputStream(fileName)){
            var crc = new CRC32();
            int c;
            while((c = in.read()) != -1){
                crc.update(c);
            }
            return crc.getValue();
        }
    }

    public static long checksumBufferedInputStream(Path filename) throws IOException {
        try(var in = new BufferedInputStream(Files.newInputStream(filename))){
            var crc = new CRC32();
            int c;
            while((c = in.read()) != -1){
                crc.update(c);
            }
            return crc.getValue();
        }
    }

    public static long checksumMappedFile(Path filename) throws IOException {
        try(FileChannel channel = FileChannel.open(filename)){
            var crc = new CRC32();
            int length = (int) channel.size();
            MappedByteBuffer buffer = channel.map(FileChannel.MapMode.READ_ONLY, 0, length);
            for(int p = 0; p < length; p++){
                int c = buffer.get(p);
                crc.update(c);
            }
            return crc.getValue();
        }

    }
}