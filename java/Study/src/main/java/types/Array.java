package types;

public class Array<E>{

    private E[] data;
    private int size;

    public Array(int capacity){

        data = (E[]) new Object[capacity];
        size = 0;
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

    public void addLast(E e){
        add(size, e);
    }

    public void addFirst(E e){
        add(0, e);
    }


    public void add(int index, E e){

        //如果超出数组容量，扩容
        if(size == data.length){
            resize(data.length * 2);
        }

        //如果中间有跳格的话
        if(index < 0 || index > size){
            throw new IllegalArgumentException("添加失败，数组元素必须紧密排列");
        }

        //从最后一个元素开始，依次往后移一位，直到当前索引位置为止
        for(int i = size - 1; i >= index; i--){
            data[i + 1] = data[i];
        }

        data[index] = e;
        size++;

    }

    public E get(int index){

        if(index < 0 || index >= size) throw new IllegalArgumentException("非法索引");
        return data[index];
    }

    public void set(int index, E e){

        if(index < 0 || index >= size) throw new IllegalArgumentException("非法索引");
        data[index] = e;
    }


    @Override
    public String toString(){

        StringBuilder res = new StringBuilder();
        res.append(String.format("Array: size = %d, capacity = %d\n", size, data.length));
        res.append("[");
        for(int i = 0; i < size; i++){
            res.append(data[i]);
            if(i != size - 1) res.append(", ");
        }

        res.append("]");
        return res.toString();

    }


    public boolean contains(E e){

        for(int i = 0; i < size; i++){
            if(data[i].equals(e)) return true;
        }
        return false;
    }

    //查找元素的索引，无效返回 -1
    public int find(E e){

        for(int i = 0; i < size; i++){
            if(data[i].equals(e)) return i;

        }
        return -1;
    }

    public E remove(int index){

        if(index < 0 || index >= size){
            throw new IllegalArgumentException("删除失败，索引不合法");
        }
        E ret = data[index];
        //从当前index位置开始，所有元素向前移一位
        for(int i = index + 1; i < size; i++){
            data[i - 1] = data[i];
        }
        size--;
        data[size] = null;

        //缩小容量
        if(size == data.length / 4 && data.length / 2 != 0){
            resize(data.length / 2);
        }

        return ret;
    }

    public E removeFirst(){
        return remove(0);
    }

    public E removeLast(){
        return remove(size - 1);
    }

    public boolean removeElement(E e){
        int index = find(e);
        if(index != -1){
            remove(index);
            return true;
        }
        return false;

    }

    //重置数据容量
    private void resize(int capacity){

        E[] newData = (E[]) new Object[capacity];
        for(int i = 0; i < size; i++){
            newData[i] = data[i];
        }
        data = newData;

    }



}
