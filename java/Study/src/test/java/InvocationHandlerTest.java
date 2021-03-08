
import org.junit.jupiter.api.Test;

import java.lang.reflect.InvocationHandler;
import java.lang.reflect.Method;
import java.lang.reflect.Proxy;

public class InvocationHandlerTest implements InvocationHandler {

    class Helloimplements {

        public void sayHello(String name) {

            System.out.println("Hello " + name);

        }

        public void sayGoogBye(String name) {

            System.out.println(name+" GoodBye!");

        }

    }


    private Object delegate;

    public Object bind(Object delegate) {

        this.delegate = delegate;
        return Proxy.newProxyInstance(this.delegate.getClass().getClassLoader(), this.delegate.getClass().getInterfaces(), this);

    }

    public Object invoke(Object proxy, Method method, Object[] args) throws Throwable {

        Object result = null;

        try{

            System.out.println("问候之前的日志记录...");
            // JVM通过这条语句执行原来的方法(反射机制)
            result = method.invoke(this.delegate, args);

        }catch(Exception e){

            e.printStackTrace();

        }

        return result;

    }

    @Test
    void main() {

        InvocationHandlerTest helloproxy = new InvocationHandlerTest();
        Helloimplements a = (Helloimplements) helloproxy.bind(new Helloimplements());
        a.sayHello("Jerry");

    }

}
