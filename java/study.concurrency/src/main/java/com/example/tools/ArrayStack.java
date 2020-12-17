package tools;

/*

        ArrayStack<Integer> stack = new ArrayStack<Integer>();
        for(int i = 0; i < 5; i++){

            stack.push(i);
            System.out.println(stack);
        }

        stack.pop();
        System.out.println(stack);

* */

public class ArrayStack<E> implements Stack<E>{

    Array<E> array;

    public ArrayStack(int length){
        array = new Array<E>(length);
    }

    public ArrayStack(){

        array = new Array<E>();
    }


    public void push(E item){

        array.add(item);
    }

    public E pop(){

        return array.remove(-1);
    }


    public E peek(){

        return array.get(-1);
    }


    public int getCount(){

        return array.getCount();
    }

    public int getLength(){

        return array.getLength();
    }

    public boolean isEmpty(){

        return array.isEmpty();
    }


    @Override
    public String toString(){

        StringBuilder sb = new StringBuilder();
        sb.append(String.format("Stack[%d]: ", array.getLength()));
        sb.append("[");
        for(int i = 0; i < array.getCount(); i++){
            sb.append(array.get(i));
            if(i != array.getCount() - 1) sb.append(',');
        }
        sb.append("]");

        return sb.toString();
    }
}
