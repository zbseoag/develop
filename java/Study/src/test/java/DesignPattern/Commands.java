package DesignPattern;

import org.junit.jupiter.api.Test;

import java.awt.*;
import java.awt.datatransfer.Clipboard;
import java.awt.datatransfer.DataFlavor;
import java.awt.datatransfer.StringSelection;
import java.awt.datatransfer.Transferable;

/**
 * 命令模式
 * 将一个请求封装为一个对象，从而使你可用不同的请求对客户进行参数化，对请求排队或记录请求日志，以及支持可撤销的操作。
 *
 */
public class Commands {

    //抽象命令
    interface Command {
        void execute();
    }

    class CopyCommand implements Command {

        private TextEditor receiver;

        public CopyCommand(TextEditor receiver) {
            this.receiver = receiver;
        }

        @Override
        public void execute() {
            receiver.copy();
        }
    }


    class PasteCommand implements Command {

        private TextEditor receiver;

        public PasteCommand(TextEditor receiver) {
            this.receiver = receiver;
        }

        @Override
        public void execute() {
            receiver.paste();
        }
    }

    class TextEditor {

        private StringBuilder buffer = new StringBuilder();

        public void copy() {

            Clipboard clip = Toolkit.getDefaultToolkit().getSystemClipboard();
            Transferable text = new StringSelection(buffer.toString());
            clip.setContents(text, null);

        }

        public void paste() {

            Clipboard sysClip = Toolkit.getDefaultToolkit().getSystemClipboard();
            Transferable clipTf = sysClip.getContents(null);
            if (clipTf != null) {
                if (clipTf.isDataFlavorSupported(DataFlavor.stringFlavor)) {
                    try {
                        String text = (String) clipTf.getTransferData(DataFlavor.stringFlavor);
                        add(text);
                    } catch (Exception e) {
                        e.printStackTrace();
                    }
                }
            }
        }

        public void add(String s) {
            buffer.append(s);
        }

        public void delete() {
            if (buffer.length() > 0) {
                buffer.deleteCharAt(buffer.length() - 1);
            }
        }



        public void print() {
            System.out.println(buffer.toString());
        }

    }


    @Test
    void main() {

        TextEditor editor = new TextEditor();

        editor.add("Command pattern in text editor.\n");
        Command copy = new CopyCommand(editor);
        copy.execute();


        editor.add("----\n");
        Command paste = new PasteCommand(editor);
        paste.execute();

        editor.print();

    }

}
