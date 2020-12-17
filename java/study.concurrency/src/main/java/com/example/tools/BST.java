package tools;

import java.util.Stack;
import java.util.LinkedList;
import java.util.Queue;
/**
二分搜索树

 */

public class BST<E extends Comparable<E>> {

    private Node root = null;
    private int size = 0;

    //前序
    public static int ORDER_FRONT = 0;
    //中序
    public static int ORDER_MIDDLE = 1;
    //后序
    public static int ORDER_BEHIND = 2;


    private class Node {

        public E e;
        public Node left = null;
        public Node right = null;

        public Node(E e){
            this.e = e;
        }

    }


    public int size(){

        return size;
    }

    public boolean isEmpty(){

        return size == 0;
    }

    public void add(E e){

        if(root == null){

            root = new Node(e);
            size++;
        }else{
            add(root, e);

        }

    }


    /**
     * 递归算法，向以 node 为根的二分搜索树中插入元素e
     * 返回插入新节点后，二分搜索树的根
     * @param node
     * @param e
     * @return
     */
    private Node add(Node node, E e){


        if(node == null){
            size++;
            return new Node(e);
        }

        //递归调用
        if(e.compareTo(node.e) < 0){

            //往左边添加
            node.left = add(node.left, e);
        }else if(e.compareTo(node.e) > 0){

            //往右边添加
            node.right = add(node.right, e);
        }

        return node;
    }


    public boolean contains(E e){

        return contains(root, e);
    }

    /**
     * 递归算法，以 node 为根的二分搜索树是否包含元素e
     * @param node
     * @param e
     * @return
     */
    private boolean contains(Node node, E e){

        if(node == null) return false;

        if(e.compareTo(node.e) == 0){

            return true;
        }else if(e.compareTo(node.e) < 0){
            return contains(node.left, e);
        }else{
            return contains(node.right, e);
        }

    }



    public void traverse(int order){
        traverse(root, order);
    }

    //二叉树遍历, 深度优先遍历
    private void traverse(Node node, int order){

        if(node == null) return;

        //前序
        if(order == ORDER_FRONT) System.out.println(node.e);

        traverse(node.left, order);

        //中序，其结果正好是已经排序的列表
        if(order == ORDER_MIDDLE) System.out.println(node.e);

        traverse(node.right, order);

        //后序
        if(order == ORDER_BEHIND) System.out.println(node.e);

    }



    //二分树遍历，栈实现
    public void traverseByStack(){

        Stack<Node> stack = new Stack<Node>();
        stack.push(root);

        while(!stack.isEmpty()){

            Node current = stack.pop();

            System.out.println(current.e);
            if(current.right != null) stack.push(current.right);
            if(current.left != null) stack.push(current.left);

        }

    }



    //二分搜索树，广度优先遍历
    public void traverseLevel(){

        Queue<Node> queue = new LinkedList<Node>();
        queue.add(root);

        while(!queue.isEmpty()){

            Node current = queue.remove();

            System.out.println(current.e);
            if(current.left != null) queue.add(current.left);
            if(current.right != null) queue.add(current.right);
        }

    }



    /**
     * 获取最小值
     * @return
     */
    public E min(){

        if(size == 0) throw new IllegalArgumentException("BST is empty");
        return min(root).e;
    }


    private Node min(Node node){

        //一直寻找最左边节点
        if(node.left == null) return node;
        return min(node.left);

    }


    /**
     * 获取最大值
     * @return
     */
    public E max(){

        if(size == 0) throw new IllegalArgumentException("BST is empty");
        return max(root).e;
    }


    private Node max(Node node){

        //一直寻找最右 边节点
        if(node.right == null) return node;
        return max(node.right);

    }


    public E removeMin(){

        E e = min();
        root = removeMin(root);
        return e;

    }


    /**
     *
     * @param node
     * @return
     */
    public Node removeMin(Node node){

        if(node.left == null){

            Node right = node.right;
            node.right = null;
            size--;
            return right;

        }

        node.left = removeMin(node.left);
        return node;
    }



    public E removeMax(){

        E e = max();
        root = removeMax(root);
        return e;

    }


    /**
     *
     * @param node
     * @return
     */
    public Node removeMax(Node node){

        if(node.right == null){

            Node left = node.left;
            node.left = null;
            size--;
            return left;

        }

        node.right = removeMax(node.right);
        return node;
    }


    public void remove(E e){

        root = remove(root, e);
    }

    private Node remove(Node node, E e){

        if(node == null) return null;

        if(e.compareTo(node.e) < 0){
            node.left = remove(node.left, e);
            return node;
        }else if(e.compareTo(node.e) > 0){
            node.right = remove(node.right, e);
            return node;
        }else{//e == node.e

            if(node.left == null){
                Node right = node.right;
                node.right = null;
                size--;
                return right;
            }

            if(node.right == null){
                Node left = node.left;
                node.left = null;
                size--;
                return left;

            }

            Node successor = min(node.right);
            successor.left = node.left;
            successor.right = removeMin(node.right);
            node.left = node.right = null;

            return successor;
        }
    }


    //--------------------------
    @Override
    public String toString(){

        StringBuilder sb = new StringBuilder();

        generateBSTString(root, 0, sb);

        return sb.toString();
    }

    private void generateBSTString(Node node, int depth, StringBuilder sb){

        if(node == null){
            sb.append(generateDepthString(depth) + "null\n");
            return;

        }

        sb.append(generateDepthString(depth) + node.e + "\n");

        generateBSTString(node.left, depth + 1, sb);
        generateBSTString(node.right, depth + 1, sb);

    }


    //获取深度
    private String generateDepthString(int depth){

        StringBuilder sb = new StringBuilder();
        for(int i = 0; i < depth; i++){
            sb.append(" --");
        }

        return sb.toString();

    }
    //--------------------------





}
