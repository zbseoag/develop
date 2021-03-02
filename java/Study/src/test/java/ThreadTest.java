import beans.Counter;
import beans.Counter2;
import beans.TaskQueue;
import org.junit.jupiter.api.Test;

import java.util.ArrayList;

public class ThreadTest{

    @Test
    public void create() throws InterruptedException{

        {
            Thread t = new Thread(() -> {
                System.out.println("start new thread!");
            });
            //t.setDaemon(true);//标记为守护线程
            t.start(); // 启动新线程
        }

        {
            System.out.println("main start...");

            Thread t = new Thread() {
                public void run() {
                    System.out.println("thread run...");
                    System.out.println("thread end.");
                }
            };
            t.start();

            System.out.println("main end...");
        }

        {
            System.out.println("main start...");
            Thread t = new Thread() {
                public void run() {
                    System.out.println("thread run...");
                    try {
                        Thread.sleep(1000);
                    } catch (InterruptedException e) {}
                    System.out.println("thread end.");
                }
            };
            t.setPriority(10);//优先级
            t.start();

            try {
                Thread.sleep(20);
            } catch (InterruptedException e) {}

            System.out.println("main end...");
        }

        {//main线程在启动t线程后，可以通过t.join()等待t线程结束后再继续运行
            Thread t = new Thread(() -> {
                System.out.println("hello");
            });
            System.out.println("start");
            t.start();
            t.join();
            System.out.println("end");
        }

        {//中断线程

            Thread t = new Thread(){
                public void run() {
                    int n = 0;
                    while (! isInterrupted()) {
                        n ++;
                        System.out.println(n + " hello!");
                    }
                }
            };

            t.start();
            Thread.sleep(1); // 暂停1毫秒
            t.interrupt(); // 中断t线程
            t.join(); // 等待t线程结束
            System.out.println("end");
        }


    }


    @Test
    public void synchronize() throws InterruptedException{

        {
            var add = new Thread(){
                public void run() {
                    for (int i=0; i<10000; i++) {
                        synchronized(Counter.lock) {
                            Counter.count += 1;
                        }
                    }
                }
            };

            var dec = new Thread(){
                public void run() {
                    for (int i=0; i<10000; i++) {
                        synchronized(Counter.lock) {
                            Counter.count -= 1;
                        }
                    }
                }
            };

            add.start();
            dec.start();
            add.join();
            dec.join();
            System.out.println(Counter.count);

        }


        {
            var c1 = new Counter2();
            var c2 = new Counter2();

            // 对c1进行操作的线程
            new Thread(() -> { c1.add(1); }).start();
            new Thread(() -> { c1.dec(2); }).start();

            // 对c2进行操作的线程
            new Thread(() -> { c2.add(1); }).start();
            new Thread(() -> { c2.dec(2); }).start();

        }

    }


    @Test
    public void waitAndNotify() throws InterruptedException{
        {
            var q = new TaskQueue();
            var ts = new ArrayList<Thread>();
            for (int i=0; i<5; i++) {
                var t = new Thread() {
                    public void run() {
                        // 执行task:
                        while (true) {
                            try {
                                String s = q.getTask();
                                System.out.println("execute task: " + s);
                            } catch (InterruptedException e) {
                                return;
                            }
                        }
                    }
                };
                t.start();
                ts.add(t);
            }

            var add = new Thread(() -> {
                for (int i=0; i<10; i++) {
                    // 放入task:
                    String s = "t-" + Math.random();
                    System.out.println("add task: " + s);
                    q.addTask(s);
                    try { Thread.sleep(100); } catch(InterruptedException e) {}
                }
            });
            add.start();
            add.join();
            Thread.sleep(100);

            for (var t : ts) {
                t.interrupt();
            }
        }
    }

}
