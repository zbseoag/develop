import java.lang.annotation.Annotation;
import java.lang.reflect.Field;
import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.lang.reflect.Modifier;
import java.util.*;
import static java.lang.System.out;

class ListNode{
    public int val;
    public ListNode next;
}

class Tree {
    public Tree left;
    public Tree right;
    public void test(){

    }
}





public class App {

    public String findLongWord(String s, List<String> d){
        String longWord = "";
        for(String target : d){
            int l1 = longWord.length(), l2 = target.length();
            if(l1 > l2 || (l1 == l2 && longWord.compareTo(target) < 0)){
                continue;
            }
            if(isSubstr(s, target)){
                longWord = target;
            }
        }
        return longWord;
    }

    public boolean isSubstr(String s, String target){
        int i = 0;
        int j = 0;
        while(i < s.length() && j < target.length()){
            if(s.charAt(i) == target.charAt(j)){
                j++;
            }
            i++;
        }
        return j == target.length();
    }


    public List getIntersectionNode(LinkedList listA){

//        LinkedList a = listA;
//        LinkedList b = listB;
//        while(a != b){
//            a = (a == null)? listB : a.;
//        }

        System.out.println(listA.getFirst());
        return null;
    }

    public ListNode mergeTwoLists(ListNode l1, ListNode l2){
        if(l1 == null) return l2;
        if(l2 == null) return l1;
        if(l1.val < l2.val){
            l1.next = mergeTwoLists(l1.next, l2);
            return l1;
        }else{
            l2.next = mergeTwoLists(l1, l2.next);
            return l2;
        }
    }

    public int[] topKFrequent(int[] nums, int k) {

        Map<Integer, Integer> frequencyForNum = new HashMap<>();
        for (int num : nums) {
            frequencyForNum.put(num, frequencyForNum.getOrDefault(num, 0) + 1);
        }

        List<Integer>[] buckets = new ArrayList[nums.length + 1];
        for (int key : frequencyForNum.keySet()) {
            int frequency = frequencyForNum.get(key);
            if (buckets[frequency] == null) {
                buckets[frequency] = new ArrayList<>();
            }
            buckets[frequency].add(key);
        }

        List<Integer> topK = new ArrayList<>();
        for (int i = buckets.length - 1; i >= 0 && topK.size() < k; i--) {
            if (buckets[i] == null) {
                continue;
            }
            if (buckets[i].size() <= (k - topK.size())) {
                topK.addAll(buckets[i]);
            } else {
                topK.addAll(buckets[i].subList(0, k - topK.size()));
            }
        }

        int[] res = new int[k];
        for (int i = 0; i < k; i++) {
            res[i] = topK.get(i);
        }
        return res;
    }

    public int maxDepth(Tree root){
        if(root == null) return 0;
        return Math.max(maxDepth(root.left), maxDepth(root.right)) + 1;

    }

    public int maxDepth1(Tree root){
        if(root == null) return 0;
        int l = maxDepth1(root.left);
        int r = maxDepth1(root.right);
      //  if(Math.abs(l - r) > 1) this.result = false;
        return 1 + Math.max(l, r);

    }

    static void printClassInfo(Class cls) {

        System.out.println("Class name: " + cls.getName());
        System.out.println("Simple name: " + cls.getSimpleName());
        if (cls.getPackage() != null) {
            System.out.println("Package name: " + cls.getPackage().getName());
        }
        System.out.println("is interface: " + cls.isInterface());
        System.out.println("is enum: " + cls.isEnum());
        System.out.println("is array: " + cls.isArray());
        System.out.println("is primitive: " + cls.isPrimitive());

    }

    public static void main(String[] args) throws Exception {

        //Class cls = String.class;
        //Class cls2 = s.getClass();
        //Class cls3 = Class.forName("java.lang.String");

        // 获取String的Class实例:
        Class cls = String.class;
        // 创建一个String实例:
        //String s = (String) cls.getDeclaredConstructor().newInstance();


        Field f = String.class.getDeclaredField("value");
        f.setAccessible(true);
        f.getName(); // "value"

        f.getType(); // class [B 表示byte[]类型
        //f.get(Obj);
        //f.set();
        int m = f.getModifiers();
        //Modifier.isPublic(f.getModifiers());
        Method method = Person.class.getMethod("test");

List<String> s = List.of("app", "bbbb");

out.println(method.getAnnotation(Report.class));

System.exit(0);
            var tree = new Tree();
            try {

                tree.getClass().getMethod("test", new Class[]{String.class}).invoke(tree, new Object[]{"ss"});
            } catch (NoSuchMethodException e) {
                e.printStackTrace();
            } catch (IllegalAccessException e) {
                e.printStackTrace();
            } catch (InvocationTargetException e) {
                e.printStackTrace();
            }


        var a = new LinkedList<String>();
        a.add("abc");
        var obj = new App();
        obj.getIntersectionNode(a);

    }


}

