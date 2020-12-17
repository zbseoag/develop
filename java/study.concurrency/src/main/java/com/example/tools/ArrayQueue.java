package tools;

/**

 ArrayQueue<Integer> queue = new ArrayQueue<Integer>();

 for(int i = 0; i < 10; i++){

     queue.add(i);
     System.out.println(queue);

     if(i % 3 == 2){

         queue.remove();
         System.out.println(queue);
     }
 }


 */
public class ArrayQueue<E> implements Queue<E>{


    private Array<E> array;

    public ArrayQueue(){
        array = new Array<E>();
    }

    public ArrayQueue(int length){

        array = new Array<E>(length);
    }


    @Override
    public E peek(){

        return array.isEmpty()? null : array.get(0);
    }



    public int getCount(){

        return array.getCount();
    }

    public boolean isEmpty(){

        return array.isEmpty();
    }

    public int getLength(){

        return array.getLength();
    }

    @Override
    public boolean add(E e){

        array.add(e);
        return true;
    }

    @Override
    public E remove(){

        return array.remove(0);
    }


    @Override
    public String toString(){

        StringBuilder sb = new StringBuilder();
        sb.append(String.format("Queue[%d]: \t", array.getLength()));
        sb.append("[");
        for(int i = 0; i < array.getCount(); i++){
            sb.append(array.get(i));
            if(i != array.getCount() - 1) sb.append(',');
        }
        sb.append("]");

        return sb.toString();
    }


}

