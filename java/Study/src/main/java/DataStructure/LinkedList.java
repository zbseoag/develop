package DataStructure;

/**
 *  (head) node -> node2 -> node3
 * @param <E>
 */
public class LinkedList<E> {

    private class Node {

        public E e;
        public Node next;

        public Node(E e, Node next){
            this.e = e;
            this.next = next;
        }
        public Node(E e){
            this(e, null);
        }
        public Node(){
            this(null, null);
        }

        @Override
        public String toString(){
            return e.toString();
        }

    }

    private Node dummyHead;//虚拟头节点,在链表头前面放置一个内容为空的节点
    private int size;

    public LinkedList(){
        dummyHead = new Node(null, null);
        size = 0;

    }

    public int getSize(){
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

        if(index < 0 || index > size) throw new IllegalArgumentException("非法索引位置");

        Node prev = dummyHead;
        for(int i = 0; i < index; i++){
            prev = prev.next;
        }
        prev.next = new Node(e, prev.next);
        size++;

    }

    public void addFirst(E e){
        add(0, e);
    }


    public void addLast(E e){
        add(size, e);
    }


    public E get(int index){

        if(index < 0 || index >= size) throw new IllegalArgumentException("非法元素位置");

        Node current = dummyHead.next;//从虚拟节点的下一个节点开始

        for(int i = 0; i < index; i++){
            current = current.next;
        }
        return current.e;
    }

    public E getFirst(){
        return get(0);
    }

    public E getLast(){
        return get(size - 1);
    }

    public void set(int index, E e){

        if(index < 0 || index >= size) throw new IllegalArgumentException("非法元素位置");
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


    public E remove(int index){

        if(index < 0 || index >= size) throw new IllegalArgumentException("非法元素位置");
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

    public E removeFirst(){
        return remove(0);
    }

    public E removeLast(){
        return remove(size - 1);
    }

    @Override
    public String toString(){

        StringBuilder sb = new StringBuilder();
        sb.append(String.format("LinkedList[%d]: NULL", size));
        Node current = dummyHead.next;
        while(current != null){
            sb.append("->" + current);
            current = current.next;

        }
        return sb.toString();
    }


}
