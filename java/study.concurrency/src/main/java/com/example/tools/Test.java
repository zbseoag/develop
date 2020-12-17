package tools;


import java.util.concurrent.*;

public class Test{


    public static void main(String[] args) throws Exception {


        for(int i = 1; i < 100; i++){

            System.out.println(i);
        }

        System.exit(0);




        ScheduledExecutorService scheduledThreadPool = Executors.newScheduledThreadPool(5);
        scheduledThreadPool.schedule(new Runnable() {

            @Override
            public void run() {
                System.out.println("delay 3 seconds");
            }
        }, 1, TimeUnit.SECONDS);



        scheduledThreadPool.scheduleAtFixedRate(new Runnable() {

            @Override
            public void run() {
                System.out.println("delay 0 seconds, and excute every 1 seconds");
            }
        }, 0, 1, TimeUnit.SECONDS);



    }





}

