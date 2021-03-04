package DesignPattern;
//组合模式
//将对象组合成树形结构以表示“部分-整体”的层次结构，使得用户对单个对象和组合对象的使用具有一致性。

import org.junit.jupiter.api.Test;

import java.util.ArrayList;
import java.util.List;
import java.util.StringJoiner;


public class Composite {

    interface Node {
        // 添加一个节点为子节点:
        Node add(Node node);
        // 获取子节点:
        List<Node> children();
        // 输出为XML:
        String toXml();

    }

    class ElementNode implements Node {

        private String name;
        private List<Node> list = new ArrayList<>();

        public ElementNode(String name) {
            this.name = name;
        }

        public Node add(Node node) {
            list.add(node);
            return this;
        }

        public List<Node> children() {
            return list;
        }

        public String toXml() {

            String start = "<" + name + ">\n";
            String end = "</" + name + ">\n";
            StringJoiner sj = new StringJoiner("", start, end);
            list.forEach(node -> {
                sj.add(node.toXml() + "\n");
            });
            return sj.toString();
        }
    }

    class TextNode implements Node {

        private String text;

        public TextNode(String text) {
            this.text = text;
        }

        public Node add(Node node) {
            throw new UnsupportedOperationException();
        }

        public List<Node> children() {
            return List.of();
        }

        public String toXml() {
            return text;
        }
    }

    class CommentNode implements Node {
        private String text;

        public CommentNode(String text) {
            this.text = text;
        }

        public Node add(Node node) {
            throw new UnsupportedOperationException();
        }

        public List<Node> children() {
            return List.of();
        }

        public String toXml() {
            return "<!-- " + text + " -->";
        }
    }


    @Test
    void main(){

        Node root = new ElementNode("school");
        root.add(new ElementNode("classA").add(new TextNode("Tom")).add(new TextNode("Alice")));
        root.add(new ElementNode("classB").add(new TextNode("Bob")).add(new TextNode("Grace")).add(new CommentNode("comment...")));

        System.out.println(root.toXml());

    }
}
