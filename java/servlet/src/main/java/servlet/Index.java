package servlet;

import bean.User;
import jakarta.servlet.*;
import jakarta.servlet.http.*;
import jakarta.servlet.annotation.*;

import java.beans.BeanInfo;
import java.beans.IntrospectionException;
import java.beans.Introspector;
import java.beans.PropertyDescriptor;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.List;
import java.util.Optional;
import java.util.StringJoiner;
import java.util.logging.Logger;
import java.util.logging.Level;
import java.util.stream.Collectors;
import java.util.stream.IntStream;
import java.util.stream.Stream;

//import org.apache.commons.logging.Log;
//import org.apache.commons.logging.LogFactory;

@WebServlet(name = "Index", value = "/Index")
public class Index extends HttpServlet{

    private String message;

    public void init(){
        message = "Hello World!";
    }


    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException{

        response.setContentType("text/html");
        PrintWriter pw = response.getWriter();
        pw.write("<h1>Index Page</h1>");
        pw.flush();
    }

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException{

        response.setContentType("text/html");

        PrintWriter out = response.getWriter();
        out.println("<html><body>");
        out.println("<h1>" + message + "</h1>");
        out.println("</body></html>");

    }

    public void destroy(){ }


    public void foreachBean() throws IntrospectionException{

        BeanInfo info = Introspector.getBeanInfo(User.class);
        for (PropertyDescriptor pd : info.getPropertyDescriptors()) {
            System.out.println(pd.getName());
            System.out.println("  " + pd.getReadMethod());
            System.out.println("  " + pd.getWriteMethod());
        }
    }

    public void stringJoiner(){

        String[] names = {"Bob", "Alice", "Grace"};
        var sj = new StringJoiner(", ", "开头 ", "结尾");
        for (String name : names) {
            sj.add(name);
        }
        System.out.println(sj.toString());
    }

    //如果调用方一定要根据null判断，比如返回null表示文件不存在，那么考虑返回Optional<T>
    public Optional<String> readFromFile(String file) {

        //断言失败时会抛出AssertionError，导致程序结束退出。因此，断言不能用于可恢复的程序错误，只应该用于开发和测试阶段。
        //JVM默认关闭断言指令，给Java虚拟机传递-enableassertions（可简写为-ea）参数启用断言。 java -ea Main.java
        //还可以有选择地对特定地类启用断言：-ea:com.itranswarp.sample.Main，表示只对com.itranswarp.sample.Main这个类启用断言。
        //或者对特定地包启用断言，命令行参数是：-ea:com.itranswarp.sample...（注意结尾有3个.），表示对com.itranswarp.sample这个包启动断言。
        assert file != "" : "file must not empty!";

        if (file == "") {
            return Optional.empty();
        }
        return Optional.of(new String("aaa"));
    }

    //可以在NullPointerException的详细信息中看到类似... because "<local1>.address.city" is null，意思是city字段为null，这样我们就能快速定位问题所在。
    //这种增强的NullPointerException详细信息是Java 14新增的功能，但默认是关闭的，我们可以给JVM添加一个-XX:+ShowCodeDetailsInExceptionMessages参数启用它：
    //java -XX:+ShowCodeDetailsInExceptionMessages Main.java

    public void logger(){
        Logger logger = Logger.getGlobal();
        logger.info("start process...");
        logger.warning("memory is running out...");
        logger.fine("ignored.");
        logger.severe("process will be terminated...");

        //Commons Logging 和 Log4j，它们一个负责充当日志API，一个负责实现日志底层，搭配使用非常便于开发。
        //SLF4J类似于Commons Logging，也是一个日志接口，而Logback类似于Log4j，是一个日志的实现。

    }

    public static void test(){

        IntStream.of(1, 2, 3, 4, 5, 6, 7, 8, 9).filter(n -> n % 2 != 0).forEach(System.out::println);

        int sum = Stream.of(1, 2, 3, 4, 5, 6, 7, 8, 9).reduce(0, (acc, n) -> acc + n);
        System.out.println(sum); // 45

        Stream<String> stream = Stream.of("Apple", "", null, "Pear", "  ", "Orange");
        List<String> list = stream.filter(s -> s != null && !s.isBlank()).collect(Collectors.toList());
        System.out.println(list);

    }

}
