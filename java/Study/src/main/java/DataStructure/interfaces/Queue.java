package DataStructure.interfaces;

public interface Queue <E> extends Print{

    int getSize();
    boolean isEmpty();
    Queue<E> enqueue(E e);
    E dequeue();
    E getFront();
}
