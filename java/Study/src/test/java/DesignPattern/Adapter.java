package DesignPattern;

import org.junit.jupiter.api.Test;
import java.util.concurrent.Callable;

/**
 * ===========================================================================================
 * 适配器
 * 将一个类的接口转换成客户希望的另外一个接口，使得原本由于接口不兼容而不能一起工作的那些类可以一起工作。
 * ===========================================================================================
 */

public class Adapter {

    /**
     * 一个实现的 Callable 接口的 call 方法的类
     * 想通过 Runnable 模式去调用
     */
    class Task implements Callable<Long>{

        private long num;

        public Task(long num) {
            this.num = num;
        }

        public Long call() throws Exception {
            long r = 0;
            for (long n = 1; n <= this.num; n++) {
                r = r + n;
            }
            System.out.println("Result: " + r);
            return r;
        }
    }

    /**
     * 适配器，适配所有实现了 Callable 接口的类
     */
    class RunnableAdapter implements Runnable {

        // 引用待转换接口
        private Callable<?> callable;

        public RunnableAdapter(Callable<?> callable) {
            this.callable = callable;
        }

        public void run() {
            // 将指定接口调用委托给转换接口调用
            try {
                callable.call();
            } catch (Exception e) {
                throw new RuntimeException(e);
            }
        }
    }


    @Test
    void main(){

        Callable<Long> callable = new Task(123450000L);
        RunnableAdapter runnable = new RunnableAdapter(callable);//通过 RunnableAdapter 适配器转换成 Runnable 接口

        Thread thread = new Thread(runnable);
        thread.start();

    }

}
