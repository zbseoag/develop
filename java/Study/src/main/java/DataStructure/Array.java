package DataStructure;

public class Array<E> {

    private E[] data;
    private int size;

    public Array(int capacity){
        data = (E[]) new Object[capacity];
        size = 0;//下一个元素的位置
    }

    public Array(){
        this(10);
    }

    public int getSize(){
        return size;
    }

    public int getCapacity(){
        return data.length;
    }

    public boolean isEmpty(){
        return size == 0;
    }

    public Array addLast(E e){
       return add(size, e);
    }

    public Array addFirst(E e){
        return add(0, e);
    }

    public Array add(int index, E e){

        if(index < 0 || index > size) throw new IllegalArgumentException("不能空位");//不能插入到当前下一个元素之后,这样会生产空位,数组元素不连续

        //扩容
        if(size == data.length) resize(data.length * 2);

        //从最后一个元素到指定要插入位置上的元素为止,依次往后挪一位,腾出空间放新元素
        for(int i = size -1; i >= index; i--){
            data[i + 1] = data[i]; //后一个元素等于前一个元素值
        }
        data[index] = e;
        size++;
        return this;
    }

    public void resize(int capacity){

        E[] data = (E[]) new Object[capacity];
        for(int i = 0; i < size; i++){
            data[i] = this.data[i];
        }
        this.data = data;

    }


    @Override
    public String toString(){

        var sb = new StringBuilder();
        sb.append(String.format("Array(size:%d, capacity:%d)", size, data.length)).append("[");

        for(int i = 0; i < size; i++){
            sb.append(data[i]);
            if(i != size - 1) sb.append(", ");
        }

        return sb.append("]").toString();
    }

    public void print(){
        System.out.println(toString());
    }

    public E get(int index){
        if(index < 0 || index >= size) throw new IllegalArgumentException("索引位置不合法");
        return data[index];
    }

    public E getLast(){
        return get(size -1);
    }

    public E getFirst(){
        return get(0);
    }

    public Array set(int index, E e){
        if(index < 0 || index >= size) throw new IllegalArgumentException("索引位置不合法");
        data[index] = e;
        return this;
    }

    public boolean contains(E e){

        for(int i = 0; i < size; i++){
            if(data[i].equals(e)) return true;
        }
        return  false;
    }

    /**
     * 查看元素索引位置
     * @param e 元素
     * @return index
     */
    public int find(E e){

        for(int i = 0; i < size; i++){
            if(data[i].equals(e)) return i;
        }
        return  -1;
    }

    public E remove(int index){

        if(index < 0 || index >= size) throw new IllegalArgumentException("索引位置不合法");
        E e = data[index];
        //从 index 之后的元素开始,到结尾,所有元素向前挪一位
        for(int i = index + 1; i < size; i++){
            data[i - 1] = data[i]; //前一个元素等于后一个元素
        }
        size--;
        data[size] = null;

        //当 size 是空间的 4分之一时才缩容一半,防止频繁扩容缩容造成复杂度震荡
        if(size <= data.length / 4 && data.length / 2 != 0){
            resize(data.length / 2);
        }

        return e;
    }

    public E removeFirst(){
        return remove(0);
    }

    public E removeLast(){
        return remove(size - 1);
    }

    public void removeElement(E e){

        int index = find(e);
        if(index != -1) remove(index);
    }


}



