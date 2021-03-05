package bean;

import java.util.concurrent.TimeUnit;
import java.util.concurrent.locks.Lock;
import java.util.concurrent.locks.ReentrantLock;

public class Counter3{

    private final Lock lock = new ReentrantLock();
    private int count;

    public void add(int n) {

        try{
            if (lock.tryLock(1, TimeUnit.SECONDS)) {
                try {
                    count += n;
                } finally {
                    lock.unlock();
                }
            }
        }catch(InterruptedException e){
            e.printStackTrace();
        }

    }

}