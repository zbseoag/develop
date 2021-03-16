package DataStructure;

import DataStructure.interfaces.Stack;

public class LinkedListStack<E> implements Stack<E> {

    private LinkedList<E> list;

    public LinkedListStack(){
        list = new LinkedList<>();
    }

    @Override
    public int getSize() {
        return list.getSize();
    }

    @Override
    public boolean isEmpty() {
        return list.isEmpty();
    }

    @Override
    public Stack<E> push(E e) {
        list.addFirst(e);
        return this;
    }

    @Override
    public E pop() {
        return list.removeFirst();
    }

    @Override
    public E peek() {
        return list.getFirst();
    }

    @Override
    public String toString(){

        StringBuilder sb = new StringBuilder();
        sb.append("Stack: top");
        sb.append(list);
        return sb.toString();
    }
}
