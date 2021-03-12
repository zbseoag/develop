package DatasTructures.tools;

/**
带有尾指针的的链表实现


 LinkedListQueue<Integer> queue = new LinkedListQueue<Integer>();

 for(int i = 0; i < 10; i++){

 queue.add(i);
 System.out.println(queue);

 if(i % 3 == 2){
 queue.remove();
 System.out.println(queue);
 }
 }


 */
public class LinkedListQueue<E> {


    private class Node {

        public E e;
        public Node next;

        public Node(E e, Node next){
            this.e = e;
            this.next = next;
        }

        public Node(E e){this(e, null);}
        public Node(){ this(null, null);}

        @Override
        public String toString(){
            return e.toString();
        }

    }


    private Node head, tail;

    int size;


    public int size(){

        return size;
    }

    public boolean isEmpty(){

        return size == 0;
    }

    public void add(E e){

        if(tail == null){
            tail = new Node(e);
            head = tail;
        }else{
            tail.next = new Node(e);
            tail = tail.next;
        }
        size++;

    }


    public E remove(){

        //非法元素位置
        if(isEmpty()) throw new IllegalArgumentException();

        Node current = head;
        head = head.next;
        current.next = null;

        if(head == null) tail = null;

        size--;
        return current.e;
    }


    public E getFront(){

        //非法元素位置
        if(isEmpty()) throw new IllegalArgumentException();
        return head.e;
    }



    @Override
    public String toString(){

        StringBuilder sb = new StringBuilder();
        sb.append(String.format("Queue[%d] ", size));

        Node current = head;
        while(current != null){
            sb.append(current + "->");
            current = current.next;
        }

        sb.append("NULL");

        return sb.toString();
    }



}
