package DatasTructures;

import DatasTructures.bean.Student;
import org.junit.jupiter.api.Test;

import java.util.HashMap;
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
    void arrayQueue(){
        var queue = new ArrayQueue<Integer>();
        queue.enqueue(1).enqueue(2).enqueue(4);
        queue.dequeue();
        queue.print();
    }



}
