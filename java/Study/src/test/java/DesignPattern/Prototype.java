package DesignPattern;
import org.junit.jupiter.api.Test;

/**
 * 原型模式
 * 用原型实例指定创建对象的种类，并且通过拷贝这些原型创建新的对象。
 */

public class Prototype {

    class Student implements Cloneable {
        private int id;
        private String name;

        public Student(){}

        public Student(int id, String name){
            this.id = id;
            this.name = name;
        }

        /**
         * 复制新对象
         * @return
         */
        public Object clone() {

            Student std = new Student();
            std.id = this.id;
            std.name = this.name;
            return std;
        }
    }


    @Test
     void main(){

        Student std1 = new Student(123, "Bob");
        // 复制新对象
        Student std2 = (Student) std1.clone();
        System.out.println(std1);
        System.out.println(std1 == std2); // false
    }

}
