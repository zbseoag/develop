package tools;

/**
 //循环队列
 LoopQueue<Integer> queue = new LoopQueue<Integer>();
 for(int i = 0; i < 10; i++){

 queue.add(i);
 System.out.println(queue);

 if(i % 3 == 2){
 queue.remove();
 System.out.println(queue);
 }

 }

 */
public class LoopQueue<E> implements Queue<E>{

    private E[] data;
    private int front, tail, size;

    public LoopQueue(){
        this(10);
    }

    public LoopQueue(int length){

        //循环队列，当front = tail 时，表示空，所以要有意空出一个位置，所以总长度得加一
        data = (E[]) new Object[length + 1];
        front = tail = size = 0;

    }

    public int getCapacity(){
        //之前，多用一个位置
        return data.length - 1;
    }


    public E peek(){

        if(isEmpty()) throw  new IllegalArgumentException("Queue is empty!");
        return data[front];
    }



    public int getSize(){
        return size;
    }


    public boolean isEmpty(){
        return front == tail;
    }

    @Override
    public boolean add(E e){

        //如果数组满了
        if((tail + 1) % data.length == front){
            resize(getCapacity() *2);
        }

        data[tail] = e;
        tail = (tail + 1) % data.length;
        size++; 

        return true;

    }


    public E remove(){

        if(isEmpty()) throw  new IllegalArgumentException("Queue is empty!");

        E ret = data[front];
        data[front] = null;

        front = (front + 1) % data.length;
        size--;

        if(size == getCapacity() / 4 && getCapacity() / 2 != 0){
            resize(getCapacity() / 2);
        }
        return ret;
    }


    private void resize(int length){

        E[] data = (E[]) new Object[length + 1];
        for(int i = 0; i < size; i++){
            data[i] = this.data[(front + i) % data.length];
        }
        this.data = data;
        front = 0;
        tail = size;

    }




    @Override
    public String toString(){

        StringBuilder sb = new StringBuilder();
        sb.append(String.format("LoopQueue[%d]: ", getCapacity()));
        sb.append('[');
        for(int i = front; i != tail; i = (i + 1) % data.length){
            sb.append(data[i]);
            if((i + 1) % data.length != tail) sb.append(',');
        }
        sb.append("]");

        return sb.toString();
    }



}
