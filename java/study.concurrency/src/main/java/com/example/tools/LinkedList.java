package tools;


public class LinkedList<E> {

    private class Node {

        public E e;
        public Node next;

        public Node(E e, Node next){
            this.e = e;
            this.next = next;
        }

        public Node(E e){ this(e, null); }

        public Node(){ this(null, null);}

        @Override
        public String toString(){

            return e.toString();
        }

    }



    private Node dummyHead;

    private int size;

    public LinkedList(){

        //初始化的时候，链表不为空,虚拟头
        dummyHead = new Node(null, null);
        size = 0;

    }


    public int size(){

        return size;
    }

    public boolean isEmpty(){

        return size == 0;
    }


    /**
     * 在指定位置插入元素
     * @param index
     * @param e
     */
    public void add(int index, E e){

        if(index < 0 || index > size) throw new IllegalArgumentException();

        Node prev = dummyHead;
        for(int i = 0; i < index; i++){
            prev = prev.next;
        }

        prev.next = new Node(e, prev.next);

        size++;

    }

    public void add(E e){
        add(size, e);
    }


    public E get(int index){

        //非法元素位置
        if(index < 0 || index >= size) throw new IllegalArgumentException();

        Node current = dummyHead.next;
        for(int i = 0; i < index; i++){
            current = current.next;
        }
        return current.e;
    }

    public E getLast(){
        return get(size - 1);
    }

    public E remove(int index){

        //非法元素位置
        if(index < 0 || index >= size) throw new IllegalArgumentException();
        Node prev = dummyHead;
        for(int i = 0; i < index; i++){
            prev = prev.next;
        }

        Node current = prev.next;
        prev.next = current.next;
        current.next = null;
        size--;
        return current.e;
    }

    public E remove(){
        return remove(size - 1);
    }

    public void set(int index, E e){

        //非法元素位置
        if(index < 0 || index >= size) throw new IllegalArgumentException();
        Node current = dummyHead;
        for(int i = 0; i < index; i++){
            current = current.next;
        }
        current.e = e;

    }

    public boolean contains(E e){

        Node current = dummyHead;

        while(current != null){

            if(current.e.equals(e)) return true;
            current = current.next;
        }
        return false;
    }


    @Override
    public String toString(){

        StringBuilder sb = new StringBuilder();
        sb.append(String.format("LinkedList[%d]: ", size));


        Node current = dummyHead.next;
        while(current != null){
            sb.append(current + "->");
            current = current.next;
        }

        sb.append("NULL");

        return sb.toString();
    }



}
