package interfaces;

public interface Aintface{

    public static void print(){
        System.out.print("static method");
    }

    default int isEmpty(){
        return 0;
    }


}
