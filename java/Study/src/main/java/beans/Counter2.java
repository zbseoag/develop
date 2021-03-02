package beans;

public class Counter2{

    private int count = 0;

    public void add(int n){
        synchronized(this){
            count += n;
        }
    }

    public synchronized void dec(int n){
        count -= n;
    }

    public int get(){
        return count;
    }
}