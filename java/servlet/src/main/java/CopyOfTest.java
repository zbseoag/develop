import java.lang.reflect.Array;
import java.util.Arrays;

public class CopyOfTest extends Base{

    public static void main(String[] args){
        int[] a = {1, 2, 3};

        a = (int[]) goodCopyOf(a, 10);
        System.out.println(Arrays.toString(a));

        String[] b = {"Tom", "Dick", "Harry"};
        b = (String[]) goodCopyOf(b, 10);
        System.out.println(Arrays.toString(b));

    }

    public static Object goodCopyOf(Object a, int newLength){

        Class cl = a.getClass();
        if(!cl.isArray()) return null;

        Class componentType = cl.getComponentType(); //返回数组类型
        Object newArray = Array.newInstance(componentType, newLength);//创建新数组
        System.arraycopy(a, 0, newArray, 0, Math.min(Array.getLength(a), newLength));//复制元素到新数组

        return newArray;
    }

}
