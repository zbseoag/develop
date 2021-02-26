import java.util.LinkedList;
import static java.lang.System.out;

public class ClassDemo {

    public static void demoLinkedList(){
        LinkedList<Integer> list = new LinkedList<>();
        list.addFirst(0);
        list.add(1);//追加
        list.add(0, 2);//插入到指定位置
        list.addLast(3);
        list.add(5);
        list.set(1, 3);//修改

        list.contains(3);//是否包含指定元素
        list.indexOf(3); //返回元素首次出现的位置
        list.lastIndexOf(3);  //返回元素最后出现的位置


        list.peek(); //返回当前元素
        list.element();//返回当前元素，带异常 NoSuchElementException
        list.poll();//弹出当前元素
        list.remove(); //弹出当前元素，带异常 NoSuchElementException
        list.offer(4); // 将指定元素添加到此列表的末尾

        list.removeFirstOccurrence(2);
        out.println(list);
        list.clear();

        out.println(list.getFirst());
        out.println();

    }

    public static void main(String[] args) {

        demoLinkedList();
    }
}


