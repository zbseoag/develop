package DatasTructures.interfaces;

import DatasTructures.ArrayStack;

public interface Stack<E> extends Print {

    int getSize();
    boolean isEmpty();
    Stack<E> push(E e);
    E pop();
    E peek();

}
