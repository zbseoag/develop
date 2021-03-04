package DesignPattern;

import org.junit.jupiter.api.Test;

import java.util.HashMap;
import java.util.Map;

/**
 * 享元模式
 * 运用共享技术有效地支持大量细粒度的对象。
 * 享元模式就是通过工厂方法创建对象，在工厂方法内部，很可能返回缓存的实例，而不是新创建实例，从而实现不可变实例的复用。
 */
public class Flyweight {

    static class Student {

        private final int id;
        private final String name;

        //持有缓存
        private static final Map<String, Student> cache = new HashMap<>();

        //静态工厂方法
        public static Student create(int id, String name) {

            String key = id + "-" + name;

            //先查找缓存
            Student std = cache.get(key);

            if (std == null) {
                //未找到,创建新对象
                System.out.println(String.format("create new Student(%s, %s)", id, name));
                std = new Student(id, name);
                cache.put(key, std);// 放入缓存
            } else {
                //缓存中存在
                System.out.println(String.format("return cached Student(%s, %s)", std.id, std.name));
            }
            return std;
        }

        public Student(int id, String name) {

            this.id = id;
            this.name = name;
        }
    }

    @Test
    void main(){

        Student.create(1, "tom");
        Student.create(2, "jim");
        Student.create(1, "tom");

    }


}
