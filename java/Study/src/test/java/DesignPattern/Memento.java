package DesignPattern;

import org.junit.jupiter.api.Test;

//备忘录模式

public class Memento {

    class TextEditor {

        private StringBuilder buffer = new StringBuilder();

        public TextEditor add(String s) {
            buffer.append(s);
            return this;
        }

        public TextEditor delete() {

            if (buffer.length() > 0) {
                buffer.deleteCharAt(buffer.length() - 1);
            }
            return this;
        }

        public String getState() {
            return buffer.toString();
        }

        public TextEditor setState(String state) {

            this.buffer.delete(0, this.buffer.length());
            this.buffer.append(state);
            return this;

        }

        public void print() {
            System.out.println(this.buffer.toString());
        }
    }

    @Test
    void main() {

        TextEditor editor = new TextEditor();
        editor.add("Hello").add(",").delete().add(" ").add("world !");

        // 保存状态:
        String state = editor.getState();
        // 继续编辑
        editor.add("第二次编辑").add("!!!").print();
        // 恢复状态
        editor.setState(state).print();

    }

}
