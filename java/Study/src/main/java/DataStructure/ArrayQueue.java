package DataStructure;

import DataStructure.interfaces.Queue;

public class ArrayQueue<E> implements Queue<E> {

    private Array<E> array;

    public ArrayQueue(int capacity) {
        array = new Array<>(capacity);
    }

    public ArrayQueue() {
        array = new Array<>();
    }

    @Override
    public String toString() {
        var sb = new StringBuilder();
        sb.append("Queue: <=[");
        for(int i = 0; i < array.getSize(); i++){
            sb.append(array.get(i));
            if(i != array.getSize() - 1) sb.append(", ");
        }
        return sb.append("]").toString();
    }

    @Override
    public int getSize() {
        return array.getSize();
    }

    @Override
    public boolean isEmpty() {
        return array.isEmpty();
    }

    @Override
    public Queue<E> enqueue(E e) {
        array.addLast(e);
        return this;
    }

    @Override
    public E dequeue() {
        return array.removeFirst();
    }

    @Override
    public E getFront() {
        return array.getFirst();
    }


}
