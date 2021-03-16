package DataStructure.tools;

public class UnionFind{

    private int[] id;

    public UnionFind(int size){

        id = new int[size];

        for(int i = 0; i < id.length; i++){

            id[i] = i;
        }
    }


    public int getSize(){

        return id.length;
    }


    private int find(int p){

        if(p < 0 && p >= id.length) throw new IllegalArgumentException("p is out of bound");

        return id[p];
    }

    /**
     * 元素p和元素q是否属于同一个集合
     * @param p
     * @param q
     * @return
     */
    public boolean isConnected(int p, int q){

        return find(p) == find(q);
    }



    public void union(int p, int q){

        int pid = find(p);
        int qid = find(q);

        if(pid == qid) return;

        for(int i = 0; i < id.length; i++){

            if(id[i] == pid) id[i] = qid;
        }
    }



}
