import java.lang.annotation.ElementType;
import java.lang.annotation.Retention;
import java.lang.annotation.RetentionPolicy;
import java.lang.annotation.Target;
//@Repeatable(Reports.class)
//使用@Inherited定义子类是否可继承父类
@Target({ElementType.METHOD, ElementType.TYPE, ElementType.FIELD})
@Retention(RetentionPolicy.RUNTIME)
public @interface Report {

    int type() default 0;
    String level() default "info";
    String value() default "";
}


