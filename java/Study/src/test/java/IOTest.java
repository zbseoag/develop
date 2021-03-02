import org.junit.jupiter.api.*;

import java.io.*;
import java.nio.charset.StandardCharsets;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.time.*;
import java.time.format.DateTimeFormatter;
import java.time.temporal.TemporalAdjusters;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;
import java.util.Properties;
import java.util.zip.ZipEntry;
import java.util.zip.ZipInputStream;
import java.util.zip.ZipOutputStream;

import static java.lang.System.out;

public class IOTest{

    @Test
    public void File() throws IOException{

        File file = new File("C:\\Windows\\notepad.exe");
        file.isFile();
        file.isDirectory();
        file.canRead();
        file.canExecute();
        file.createNewFile();
        file.delete();
        file.mkdir();
        file.mkdirs();

        file = new File("..");
        file.getPath();//返回构造方法传入的路径
        file.getAbsolutePath();
        file.getCanonicalPath();

        file = File.createTempFile("tmp-", ".txt"); // 提供临时文件的前缀和后缀
        file.deleteOnExit(); // JVM退出时自动删除

        file = new File("C:\\Windows");

        File[] files = file.listFiles(new FilenameFilter(){

            public boolean accept(File dir, String name) {
                return name.endsWith(".exe"); //仅列出.exe文件
            }

        });

        if (files != null) {
            for (File f : files) System.out.println(f);
        }

    }

    /**
     * 如果需要对目录进行复杂的拼接、遍历等操作，使用Path对象更方便
     * @throws IOException
     */
    @Test
    public void path() throws IOException {

        Path p1 = Paths.get(".", "project", "study"); // 构造一个Path对象
        System.out.println(p1);

        Path p2 = p1.toAbsolutePath(); //转换为绝对路径
        System.out.println(p2);

        Path p3 = p2.normalize(); //转换为规范路径
        System.out.println(p3);

        File f = p3.toFile(); //转换为File对象
        System.out.println(f);

        //可以直接遍历Path
        for (Path p : Paths.get("..").toAbsolutePath()) {
            System.out.println("  " + p);
        }


    }

    @Test
    public void InputStream() throws IOException{

        try (InputStream input = new FileInputStream("src/readme.txt")) {
            int n;
            while ((n = input.read()) != -1) {
                System.out.println(n);
            }
        } // 编译器在此自动为我们写入finally并调用close()

        try (InputStream input = new FileInputStream("src/readme.txt")) {
            // 定义1000个字节大小的缓冲区:
            byte[] buffer = new byte[1000];
            int n;
            while ((n = input.read(buffer)) != -1) { // 读取到缓冲区
                System.out.println("read " + n + " bytes.");
            }
        }

        byte[] data = { 72, 101, 108, 108, 111, 33 };
        try (InputStream input = new ByteArrayInputStream(data)) {
            int n;
            while ((n = input.read()) != -1) {
                System.out.println((char)n);
            }
        }

    }

    @Test
    public void OutputStream() throws IOException{

        try(OutputStream output = new FileOutputStream("out/readme.txt")){
            output.write(72); // H
            output.write(101); // e
            output.write("World".getBytes("UTF-8")); // Hello
        }

        byte[] data;
        try (ByteArrayOutputStream output = new ByteArrayOutputStream()) {
            output.write("Hello ".getBytes("UTF-8"));
            output.write("world!".getBytes("UTF-8"));
            data = output.toByteArray();
            System.out.println(new String(data, "UTF-8"));
        }

        // 读取input.txt，写入output.txt:
        try (InputStream input = new FileInputStream("input.txt"); OutputStream output = new FileOutputStream("output.txt")){
            input.transferTo(output);
        }


    }

    @Test
    public void ZipInputStream() throws IOException{

        try (ZipInputStream zip = new ZipInputStream(new FileInputStream("abc.zip"))) {
            ZipEntry entry = null;
            while ((entry = zip.getNextEntry()) != null) {
                String name = entry.getName();
                if (!entry.isDirectory()) {
                    int n;
                    while ((n = zip.read()) != -1) {

                    }
                }
            }
        }

        try (ZipOutputStream zip = new ZipOutputStream(new FileOutputStream("abc.zip"))) {

            File[] files = { new File("a.txt") };

            for (File file : files) {
                zip.putNextEntry(new ZipEntry(file.getName()));

                InputStream input = new FileInputStream(file) ; // 定义文件的输入流

                int content = 0;
                while((content = input.read())!=-1){
                    zip.write(content);
                }
                zip.closeEntry();
                input.close();
            }

        }

    }

    @Test
    public void classpath(){

        try (InputStream input = getClass().getResourceAsStream("/default.properties")) {
            if (input != null) {
                // TODO:
            }

        }catch(IOException e){
            e.printStackTrace();
        }

        try{
            Properties props = new Properties();
            props.load(new FileInputStream("/default.properties"));
            props.load(new FileInputStream("config.properties"));//覆盖默认配置

        }catch(IOException e){
            e.printStackTrace();
        }

    }

    @Test
    public void Serializable() throws IOException, ClassNotFoundException{

        ByteArrayOutputStream buffer = new ByteArrayOutputStream();
        try (ObjectOutputStream output = new ObjectOutputStream(buffer)) {

            output.writeInt(12345);// 写入int
            output.writeUTF("Hello");// 写入String
            output.writeObject(Double.valueOf(123.456));// 写入Object

        }
        System.out.println(Arrays.toString(buffer.toByteArray()));

        //反序列化
        try (ObjectInputStream input = new ObjectInputStream(new FileInputStream("aaa.txt"))) {
            int n = input.readInt();
            String s = input.readUTF();
            Double d = (Double) input.readObject();
        }

    }

    @Test
    public void Reader() throws IOException{

        // 创建一个FileReader对象
        try(Reader reader = new FileReader("src/readme.txt", StandardCharsets.UTF_8);){

            for (;;) {
                int n = reader.read(); // 反复调用read()方法，直到返回-1
                if (n == -1) break;
                System.out.println((char)n); // 打印char
            }

        }


        try (Reader reader = new FileReader("src/readme.txt", StandardCharsets.UTF_8)) {
            char[] buffer = new char[1000];
            int n;
            while ((n = reader.read(buffer)) != -1) {

                System.out.println("read " + n + " chars.");
                out.println(buffer.toString());
            }
        }

        try (Reader reader = new CharArrayReader("Hello".toCharArray())) {

        }

        try (Reader reader = new StringReader("Hello")) {

        }

        // InputStream变换为为 Reader
        try (Reader reader = new InputStreamReader(new FileInputStream("src/readme.txt"), "UTF-8")) {
            // TODO:
        }

    }

    @Test
    public void Writer() throws IOException{

        try (Writer writer = new FileWriter("readme.txt", StandardCharsets.UTF_8)) {
            writer.write('H'); // 写入单个字符
            writer.write("Hello".toCharArray()); // 写入char[]
            writer.write("Hello"); // 写入String
        }

        try (CharArrayWriter writer = new CharArrayWriter()) {
            writer.write(65);
            writer.write(66);
            writer.write(67);
            char[] data = writer.toCharArray(); // { 'A', 'B', 'C' }
        }

        //OutputStream转换为Writer
        try (Writer writer = new OutputStreamWriter(new FileOutputStream("readme.txt"), "UTF-8")) {
            // TODO:
        }

    }

    @Test
    public void PrintStream() throws FileNotFoundException{

        new PrintStream(new FileOutputStream("aaa.txt"));

    }

    @Test
    public void PrintWriter(){

        StringWriter buffer = new StringWriter();
        try (PrintWriter pw = new PrintWriter(buffer)) {
            pw.println("Hello");
            pw.println(12345);
            pw.println(true);
        }
        System.out.println(buffer.toString());

    }

    @Test
    public void Files() throws IOException{

        byte[] data = Files.readAllBytes(Paths.get("/path/to/file.txt"));

        String content2 = Files.readString(Paths.get("/path/to/file.txt"), StandardCharsets.ISO_8859_1);
        // 按行读取并返回每行内容:
        List<String> lines = Files.readAllLines(Paths.get("/path/to/file.txt"));

        // 写入二进制文件:
        byte[] data2 = {12, 33};
        Files.write(Paths.get("/path/to/file.txt"), data2);
        // 写入文本并指定编码:
        Files.writeString(Paths.get("/path/to/file.txt"), "文本内容...", StandardCharsets.ISO_8859_1);
        // 按行写入文本:
        List<String> lines2 = new ArrayList<>();
        Files.write(Paths.get("/path/to/file.txt"), lines2);

    }

}

