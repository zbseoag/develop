package DatasTructures.tools;

/**
 LinkedListStack<Integer> stack = new LinkedListStack<Integer>();

 for(int i = 0; i < 5; i++){

 stack.push(i);
 System.out.println(stack);
 }

 stack.pop();
 System.out.println(stack);

 */

import java.util.LinkedList;

public class LinkedListStack<E> {


    private LinkedList<E> list;

    public LinkedListStack(){

        list = new LinkedList<E>();
    }

    public int size(){

        return list.size();
    }

    public boolean isEmpty(){

        return list.isEmpty();
    }

    public void push(E e){

        list.add(0, e);
    }

    public E pop(){

        return list.remove(0);
    }

    public E peek(){

        return list.get(0);
    }

    @Override
    public String toString(){

        StringBuilder sb = new StringBuilder();
        sb.append("Stack ");
        sb.append(list);
        return sb.toString();

    }

}
