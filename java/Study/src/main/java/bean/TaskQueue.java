package bean;

import java.util.LinkedList;
import java.util.Queue;

public class TaskQueue{

    Queue<String> queue = new LinkedList<>();

    public synchronized void addTask(String s) {
        this.queue.add(s);
        this.notifyAll();//唤醒在this锁等待的线程
    }


    public synchronized String getTask() throws InterruptedException {


        while(queue.isEmpty()){
            this.wait();//释放锁，并进入线程等待
            //重新获取this锁
        }

        return queue.remove();
    }


}
