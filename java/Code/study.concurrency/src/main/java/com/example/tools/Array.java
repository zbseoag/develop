package tools;

public class Array<E> {

    private E[] data;
    private int count = 0;


    public Array(int length){

        data = (E[]) new Object[length];
    }


    public Array(){

        this(10);
    }


    /**
     * 获取数组长度
     * @return
     */
    public int getLength(){

        return data.length;
    }

    public int getCount(){

        return count;
    }


    public boolean isEmpty(){

        return count == 0;
    }

    /**
     * 追加元素
     * @param item
     */
    public Array<E> add(E item){

        return add(count, item);

    }


    /**
     * 插入元素
     * @param index 索引位置
     * @param item 元素
     */
    public Array<E> add(int index, E item){

        //元素索引必须合法，则插入位置不能造成元素位置不连续
        if(index < 0 || index > count) throw new IllegalArgumentException("Get failed Index is illegal.");

        if(count == data.length){
            resize((int) (data.length * 1.5));
        }

        //插入时，从尾部开始，每个元素后移动一位,直到当前索引位置为止
        for(int i = count - 1; i >= index; i--){
            data[i + 1] = data[i];
        }

        data[index] = item;
        count++;

        return this;
    }



    /**
     * 获取元素
     * @param index 索引，负数表示倒数
     * @return
     */
    public E get(int index){

        if(index < 0) index += count;
        if(index >= count) throw new IllegalArgumentException("Get failed Index is illegal.");

        return data[index];
    }


    /**
     * 修改元素
     * @param index 索引，负数表示倒数
     * @param item
     */
    public Array<E> set(int index, E item){

        if(index < 0) index += count;
        if(index >= count) throw new IndexOutOfBoundsException();
        data[index] = item;

        return this;
    }


    /**
     * 删除给定索引位置的元素
     * @param index
     * @return 元素
     */
    public E remove(int index){

        if(index < 0) index += count;
        if(index >= count) throw new IndexOutOfBoundsException();

        for(int i = index; i < count - 1; i++){
            data[i] = data[i + 1];
        }

        count--;
        data[count] = null;

        //收缩容量
        if(count == data.length / 4 && data.length / 2 > 0){
            resize(data.length / 2);
        }

        return data[index];
    }


    /**
     * 删除给定元素
     * @param item
     * @return
     */
    public boolean remove(E item){

        int index = find(item);
        if(index >= 0){
            remove(index);
            return true;
        }
        return false;
    }


    /**
     * 删除所有给定元素
     * @param item
     * @return boolean
     */
    public boolean removeAll(E item){

        int index = find(item);
        if(index > -1){

            do{
                remove(index);
                index = find(item);

            }while(index <= 0);

            return true;
        }

        return false;
    }



    /**
     * 重新分配数组长度
     * @param length
     */
    private void resize(int length){

        E[] data = (E[]) new Object[length];
        for(int i = 0; i < count; i++){
            data[i] = this.data[i];
        }
        this.data = data;
    }


    /**
     * 判断元素是否存在
     * @param item
     * @return boolean
     */
    public boolean contains(E item){

        return (find(item) > -1)? true : false;
    }


    /**
     * 查找元素所在位置
     * @param item
     * @return 索引位置
     */
    public int find(E item){

        for(int i = 0; i < count; i++){
            //对象之间用 equals 方法，表示值比较
            if(data[i].equals(item)) return i;
        }
        return -1;
    }



    @Override
    public String toString(){

        StringBuilder sb = new StringBuilder();
        sb.append(String.format("Array[%d]: ", data.length, count));
        sb.append('[');
        for(int i = 0; i < count; i++){
            sb.append(data[i]);
            if(i != count - 1) sb.append(", ");
        }
        sb.append("]");

        return sb.toString();
    }




}