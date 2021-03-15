package DatasTructures;
import DatasTructures.interfaces.Queue;

//循环队列

public class LoopQueue <E> implements Queue<E> {

    private E[] data;
    private int front, tail;
    private int size;

    public LoopQueue(int capacity) {
        data = (E[]) new Object[capacity + 1];
        front = 0;
        tail = 0;
        size = 0;
    }

    public LoopQueue() {
        this(10);
    }

    @Override
    public int getSize() {
        return size;
    }

    @Override
    public boolean isEmpty() {
        return front == tail;
    }

    @Override
    public Queue<E> enqueue(E e) {

        if((tail + 1) % data.length == front){
            resize(getCapacity() * 2);
        }
        data[tail] = e;
        tail = (tail + 1) % data.length;
        size++;
        return this;
    }

    @Override
    public E dequeue() {

        if(isEmpty()) throw new IllegalArgumentException("空队列");

        E e = data[front];
        data[front] = null;
        front = (front + 1) % data.length;
        size--;

        if(size == getCapacity() / 4 && getCapacity() / 2 != 0) resize(getCapacity() / 2);
        return e;
    }

    @Override
    public E getFront() {

        if(isEmpty()) throw new IllegalArgumentException("空队列");
        return data[front];
    }

    public int getCapacity(){
        return data.length - 1;
    }

    public void resize(int capacity){

        E[] data = (E[]) new Object[capacity + 1];
        for(int i = 0; i < size; i++){
            data[i] = data[(i + front) % data.length];
        }
        this.data = data;
        front = 0;
        tail = size;
    }

    @Override
    public String toString(){

        var sb = new StringBuilder();
        sb.append(String.format("Queue(size:%d, capacity:%d)", size, getCapacity())).append("<= [");

        for(int i = front; i != tail; i = (i + 1) % data.length){
            sb.append(data[i]);

            if((i + 1) % data.length != tail) sb.append(", ");
        }

        return sb.append("]").toString();
    }

}
