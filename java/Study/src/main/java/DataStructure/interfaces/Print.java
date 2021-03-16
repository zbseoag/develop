package DataStructure.interfaces;

public interface Print {

    default void print(){
        System.out.println(toString());
    }
}
