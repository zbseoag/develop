import org.junit.jupiter.api.Test;

import java.lang.annotation.Annotation;
import java.lang.annotation.Retention;
import java.lang.annotation.RetentionPolicy;
import java.lang.reflect.Method;

public class AnnotationTest {

    @Retention(RetentionPolicy.RUNTIME)
    @interface MyAnnotation {
        String[] six() default "unknown";
    }

    class Person {

        @MyAnnotation //取默认值
        @Deprecated //标记为不被建议使用
        public void empty(){
            System.out.println("empty");
        }

        @MyAnnotation(six={"girl","boy"}) //MyAnnotation的value值是{"girl","boy"}
        public void somebody(String name, int age){
            System.out.println("somebody: "+name+", "+age);

        }
    }

    public static void iteratorAnnotations(Method method) {

        // 判断 somebody() 方法是否包含MyAnnotation注解
        if(method.isAnnotationPresent(MyAnnotation.class)){
            // 获取该方法的MyAnnotation注解实例
            MyAnnotation myAnnotation = method.getAnnotation(MyAnnotation.class);
            // 获取 myAnnotation的值，并打印出来
            String[] values = myAnnotation.six();
            for (String str:values)
                System.out.printf(str+", ");
            System.out.println();
        }

        // 获取方法上的所有注解，并打印出来
        Annotation[] annotations = method.getAnnotations();
        for(Annotation annotation : annotations){
            System.out.println(annotation);
        }

    }

    @Test
    void main() throws Exception {

        Person person = new Person();

        // 获取Person的Class实例
        Class<Person> c = Person.class;
        // 获取 somebody() 方法的Method实例
        //Method mSomebody = c.getMethod("somebody", new Class[]{String.class, int.class});
        Method mSomebody = c.getMethod("somebody", String.class, int.class);
        // 执行该方法
        mSomebody.invoke(person, new Object[]{"lily", 18});
        iteratorAnnotations(mSomebody);

        // 获取 somebody() 方法的Method实例
        Method mEmpty = c.getMethod("empty", new Class[]{});
        // 执行该方法
        mEmpty.invoke(person, new Object[]{});
        iteratorAnnotations(mEmpty);

    }



}
