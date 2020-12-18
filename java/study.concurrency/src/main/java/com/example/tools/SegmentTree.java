package tools;

public class SegmentTree<E>{

    private E[] data;
    private E[] tree;

    public SegmentTree(E[] arr){
        data = (E[]) new Object[arr.length];

        for(int i = 0; i < arr.length; i++){
            data[i] = arr[i];
        }

        tree = (E[]) new Object[4 * arr.length];
        create(0, 0, data.length - 1);

    }

    /**
     * 在index位置创建表示区间l ...r 的线段树
     * @param index
     * @param l
     * @param r
     */
    private void create(int index, int l, int r){

        if(l == r){
            tree[index] = data[l];

            return;
        }

        int left = leftChild(index);
        int right = rightChild(index);

        int mid = l + (r - l) / 2;
        create(left, l, mid);
        create(right, mid + 1, r);



    }


    public int getSize(){

        return data.length;
    }

    public E get(int index){

        if(index < 0 || index >= data.length) throw new IllegalArgumentException("索引不合法");

        return data[index];

    }


    private int leftChild(int index){

        return 2 * index + 2;
    }

    private int rightChild(int index){

        return 2 * index + 2;
    }



}
