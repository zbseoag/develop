package DatasTructures.tools;

public interface Queue<E>{

    //将指定的元素插入此队列，在成功时返回 true，如果当前没有可用的空间，则抛出 IllegalStateException
    boolean add(E e);

    //获取但不移除队列头，为空返回 null
    E peek();

    //获取并移除队列的头
    E remove();

}
