import java.util.Objects;

public class Base{

    public static <E> void p(E val){
        System.out.println(val);
    }
    public static void exit(){ System.exit(0);}
    public static <E> void stop(E val){ p(val); System.exit(0);}

    public static void test(){

        Objects.hash(1, 2, 3, 4);
    }

}
