package DataStructure;

import DataStructure.bean.Student;
import org.junit.jupiter.api.Test;

import java.util.HashMap;
import java.util.Random;
import java.util.Stack;

public class Main {

    @Test
    void array(){
        var arr = new Array<Student>(2);
        arr.addLast(new Student("tom", 12)).addLast(new Student("jim", 15));
        arr.print();
    }

    @Test
    void arrayStack(){

        var stack = new ArrayStack<Integer>();
        stack.push(1).push(2).push(3);
        stack.pop();
        stack.print();

    }

    @Test
    boolean isBracesValid(String s) {

        HashMap<Character, Character> mappings = new HashMap<>();
        mappings.put(')', '(');
        mappings.put('}', '{');
        mappings.put(']', '[');

        Stack<Character> stack = new Stack<>();

        for(int i = 0; i < s.length(); i++) {
            char c = s.charAt(i);
            //如果找到键名为c的元素
            if(mappings.containsKey(c)) {
                char topElement = stack.empty() ? null : stack.pop();
                if(topElement != mappings.get(c)){
                    return false;
                }
            }else{
                stack.push(c);
            }
        }

        return stack.empty();
    }

    private static int sum(int[] arr, int start){

        if(start == arr.length) return 0;
        return arr[start] + sum(arr, start + 1);
    }

    @Test
    void ArrayQueue(){
        var queue = new ArrayQueue<Integer>();
        queue.enqueue(1).enqueue(2).enqueue(4);
        queue.dequeue();
        queue.print();
    }


    @Test
    void LoopQueue(){

        int count = 10000;
        LoopQueue<Integer> arrayQueue = new LoopQueue<>();

        long starttime = System.nanoTime();

        Random random = new Random();
        for(int i = 0; i < count; i++){
            arrayQueue.enqueue(random.nextInt(Integer.MAX_VALUE));
        }
        for(int i = 0; i < count; i++){
            arrayQueue.dequeue();
        }

        long endtime = System.nanoTime();
        double time = (endtime - starttime) / 1000000000.0;

        System.out.println(time);

    }

    @Test
    void LinkedList(){
        LinkedList<Integer> linkedList = new LinkedList<>();
        linkedList.addLast(1);
        linkedList.addLast(2);
        linkedList.addLast(3);
        linkedList.addLast(4);

        System.out.println(linkedList.toString());

    }

    @Test
    void LinkedListStack(){
        LinkedListStack<Integer> stack = new LinkedListStack<>();
        stack.push(1).push(2).push(3);
        stack.pop();
        System.out.println(stack);
    }

    @Test
    void LinkedListQueue(){

        LinkedListQueue<Integer> queue = new LinkedListQueue<Integer>();
        for(int i = 0; i < 10; i++){

            queue.add(i);
            System.out.println(queue);

            if(i % 3 == 2){
                queue.remove();
                System.out.println(queue);
            }
        }
    }

}
