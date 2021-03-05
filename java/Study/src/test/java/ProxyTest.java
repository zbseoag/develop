import java.lang.reflect.InvocationHandler;
import java.lang.reflect.Method;
import java.lang.reflect.Proxy;
import java.util.Arrays;
import java.util.Random;


public class ProxyTest{

    public static void main(String[] args){

        var elements = new Object[1000];

        for(int i = 0; i < elements.length; i++){

            Integer value = i + 1;
            var handler = new TraceHandler(value);
            Object proxy = Proxy.newProxyInstance(ClassLoader.getSystemClassLoader(), new Class[]{Comparable.class}, handler);
            elements[i] = proxy;
        }

        // construct a random integer
        Integer key = new Random().nextInt(elements.length) + 1;

        // search for the key
        int result = Arrays.binarySearch(elements, key);

        // print match if found
        if(result >= 0) System.out.println(elements[result]);
    }
}


class TraceHandler implements InvocationHandler{

    private Object target;

    public TraceHandler(Object t){
        target = t;
    }

    public Object invoke(Object proxy, Method m, Object[] args) throws Throwable{

        System.out.print(target);

        System.out.print("." + m.getName() + "(");

        if(args != null){
            for(int i = 0; i < args.length; i++){
                System.out.print(args[i]);
                if(i < args.length - 1) System.out.print(", ");
            }
        }

        System.out.println(")");

        return m.invoke(target, args);

    }

}
