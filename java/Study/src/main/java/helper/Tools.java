package helper;

import java.io.*;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

public class Tools{

    public static String date(String format, Long timestamp) {

        return (new SimpleDateFormat(format)).format(timestamp);
    }


    public static String date(Long timestamp) {

        return date("yyyy-MM-dd HH:mm:ss", timestamp);
    }


    public static String date(String format) {

        return date(format);
    }

    public static String date() {

        return date( "yyyy-MM-dd HH:mm:ss", System.currentTimeMillis());

    }

    public static String getThreadName() {

        return Thread.currentThread().getName();
    }


    public static <T> Object[] merge(T[] a, T[] b) {

        List<T> list = new ArrayList<T>(Arrays.asList(a));
        list.addAll(Arrays.asList(b));

        return list.toArray();

    }

    public static long fact(long n) {
        long r = 1;
        for (long i = 1; i <= n; i++) {
            r = r * i;
        }
        return r;
    }

    public static long modified(String file) throws MalformedURLException, IOException{

        URLConnection uc = (new URL(file)).openConnection();
        uc.setUseCaches(false);

        return uc.getLastModified();

    }

    public static boolean download(String url, String file) throws IOException {

        BufferedReader reader = new BufferedReader(new InputStreamReader(new URL(url).openStream()));
        BufferedWriter writer = new BufferedWriter(new FileWriter(file));

        String line;

        while ((line = reader.readLine()) != null){

            writer.write(line);
            writer.newLine();
        }

        reader.close();
        writer.close();

        return true;

    }

    public static boolean rename(String file, String newfile) {

        return (new File(file)).renameTo(new File(newfile));

    }

    public static void isCanWrite(String filePath) {

        File file = new File(filePath);
        System.out.println(file.canWrite());

    }


    // 比较三个值并返回最大值
    @SafeVarargs
    public static <T extends Comparable<T>> T max(T... number){

        T max = number[0]; // 假设x是初始最大值
        for(T i : number) {

            if(i.compareTo(max) > 0){
                max = i;
            }
        }
        return max; // 返回最大对象

    }

    @SafeVarargs
    public static <T extends Comparable<T>> T min(T... number){

        T min = number[0]; // 假设x是初始最大值
        for(T i : number) {

            if(i.compareTo(min) < 0){
                min = i;
            }
        }

        return min; // 返回最大对象

    }


    public static Object getItem(List<?> data, int index){

        return data.get(index);
    }

    public static Number getUperNumber(List<? extends Number> data) {

        return data.get(0);
    }



}
