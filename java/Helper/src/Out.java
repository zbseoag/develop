public class Out{

    public static <E> void print(E val){
        System.out.print(val);
    }

    public static <E> void echo(E val){
        System.out.println(val);
    }

    public static <E> void stop(E val){
        Out.echo(val);
        System.exit(0);
    }


    public static void exit(){
        System.exit(0);
    }

}
