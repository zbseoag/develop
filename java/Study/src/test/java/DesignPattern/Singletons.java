package DesignPattern;

import org.junit.jupiter.api.Test;

/**
 * 单例模式
 * 保证一个类仅有一个实例，并提供一个访问它的全局访问点。
 * 除非确有必要，否则Singleton模式一般以“约定”为主，不会刻意实现它。
 */

public class Singletons {

    static class Singleton {
        // 静态字段引用唯一实例:
        private static final Singleton INSTANCE = new Singleton();
        private static Singleton INSTANCE2 = null;

        // private构造方法保证外部无法实例化:
        private Singleton() {}

        // 通过静态方法返回实例:
        public static Singleton getInstance() {
            return INSTANCE;
        }

        public synchronized static Singleton getInstance2() {

            if (INSTANCE2 == null) {
                INSTANCE2 = new Singleton();
            }
            return INSTANCE2;
        }

        public static Singleton getInstance3() {

            if (INSTANCE2 == null) {
                synchronized (Singleton.class) {
                    if (INSTANCE2 == null) {
                        INSTANCE2 = new Singleton();
                    }
                }
            }
            return INSTANCE2;
        }

    }

    enum World {
        // 唯一枚举:
        INSTANCE;

        private String name = "world";
        public String getName() {
            return this.name;
        }
        public void setName(String name) {
            this.name = name;
        }
    }

    @Test
    void main(){

    }

}
