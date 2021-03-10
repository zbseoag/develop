package task;

import java.io.File;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.util.concurrent.LinkedBlockingDeque;
import java.util.concurrent.ThreadPoolExecutor;
import java.util.concurrent.TimeUnit;

public class LoadDataTask implements Runnable {

    public File file;

    @Override
    public void run() {

        var url = "jdbc:mysql://127.0.0.1:3306/test";
        var username = "admin";
        var passwod = "123456";
        try{
            var con = DriverManager.getConnection(url, username, passwod);

            var sql = String.format("load data local infile '/home/data/%s' ignore into table t_test",  file.getName());
            var pst = con.prepareCall(sql);
            pst.execute();
            con.close();

        }catch(SQLException throwables){
            throwables.printStackTrace();
        }

    }


}


class LoadData{

    public static int end = 0;
    public static int num = 0;
    public static ThreadPoolExecutor pool = new ThreadPoolExecutor(1, 5, 60, TimeUnit.SECONDS, new LinkedBlockingDeque<>(200));

    public static void main(String[] args) throws ClassNotFoundException {

        Class.forName("com.mysql.jdbc.Driver");

        var folder = new File("/home/data");
        var files = folder.listFiles();
        end = files.length;

        for(File file : files){
            var task = new LoadDataTask();
            task.file = file;
            pool.execute(task);
        }

    }

    public synchronized static void updateNum(){

        num++;
        if(num == end){
            pool.shutdown();
            System.out.println("执行结束");
        }
    }
}