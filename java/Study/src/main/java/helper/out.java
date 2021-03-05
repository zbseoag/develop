package helper;

import java.net.InetAddress;
import java.net.UnknownHostException;
import java.util.Arrays;

import static java.lang.System.out;

public class out {

    public static <E> void print(E... args){

        for(E i:args)  System.out.println(i);
    }

    public static <E> void println(E... args){

        for(E i:args)  System.out.println(i);
    }


    public static <E> void stop(E... args){

        for(E i:args)  out.println(i);
        System.exit(0);
    }

    public static <T> String getType(T a) {

        return a.getClass().getTypeName();
    }

    public static <T> void printf(String format, T... data){

        System.out.printf(format + "\n", data);

    }


    public static <T> void printr(T data) {

        if(data.getClass().isArray()){

            String out = null;
            String type = getType(data);

            switch (type) {
                case "byte[]": 		out = Arrays.toString((byte[]) data);
                    break;
                case "short[]": 	out = Arrays.toString((short[]) data);
                    break;
                case "int[]": 		out = Arrays.toString((int[]) data);
                    break;
                case "long[]": 		out = Arrays.toString((long[]) data);
                    break;
                case "float[]": 	out = Arrays.toString((float[]) data);
                    break;
                case "double[]": 	out = Arrays.toString((double[]) data);
                    break;
                case "char[]": 		out = Arrays.toString((char[]) data);
                    break;
                case "boolean[]": 	out = Arrays.toString((boolean[]) data);
                    break;
                default:
                    out = Arrays.toString((Object[]) data);
                    break;
            }

            System.out.println(out);

        }else {
            System.out.println(data);
        }


    }

    public static String getIp() throws UnknownHostException{

        return (InetAddress.getLocalHost()).getHostAddress();
    }




}
