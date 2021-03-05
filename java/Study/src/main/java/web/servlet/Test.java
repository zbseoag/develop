package web.servlet;

import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;

import java.beans.BeanInfo;
import java.beans.IntrospectionException;
import java.beans.Introspector;
import java.beans.PropertyDescriptor;
import java.util.Optional;
import java.util.StringJoiner;
import java.util.logging.Logger;

@WebServlet("/test")
public class Test extends HttpServlet{

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response) {

        BeanInfo info = null;
        try{
            info = Introspector.getBeanInfo(User.class);
        }catch(IntrospectionException e){
            e.printStackTrace();
        }

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


}
