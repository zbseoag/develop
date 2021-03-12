package DatasTructures.tools;

public interface Stack<E>{

    public void push(E item);

    public E pop();

    public E peek();

    public int getCount();

    public boolean isEmpty();

}
