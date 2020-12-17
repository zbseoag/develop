//#!/usr/bin/java --source 14
import static java.lang.System.out;
import java.util.*;

public class Test {

    public static <E> void p(E data){
        out.println(data);
    }

    public static void main(String[] args) throws IOException{

        //Scanner in = new Scanner(System.in);
        p(System.getProperty("user.dir"));
        { int i = 10;}
        

    }
}