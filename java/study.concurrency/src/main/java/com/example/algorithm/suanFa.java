package com.example.algorithm;

import java.util.HashMap;
import java.util.Stack;


class Sum {

    public static int sum(int[] arr){

        return sum(arr, 0);
    }

    private static int sum(int[] arr, int start){

        if(start == arr.length) return 0;

        return arr[start] + sum(arr, start + 1);
    }
}



class Solution {

    private HashMap<Character, Character> mappings;


    public boolean isValid(String s) {

        this.mappings = new HashMap<Character, Character>();

        //键值对
        this.mappings.put(')', '(');
        this.mappings.put('}', '{');
        this.mappings.put(']', '[');


        Stack<Character> stack = new Stack<Character>();

        for(int i = 0; i < s.length(); i++) {

            char c = s.charAt(i);
            //如果找到键名为c的元素
            if(this.mappings.containsKey(c)) {

                char topElement = stack.empty() ? '#' : stack.pop();
                if(topElement != this.mappings.get(c)){
                    return false;
                }
            } else {
                stack.push(c);
            }
        }

        return stack.empty();
    }

}

