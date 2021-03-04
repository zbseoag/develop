package DesignPattern;

//装饰器
//动态地给一个对象添加一些额外的职责。就增加功能来说，相比生成子类更为灵活。
//是一种在运行期动态给某个对象的实例增加功能的方法。

import org.junit.jupiter.api.Test;
import java.io.IOException;

public class Decorators {

    interface Node {
        String getText();
    }

    class SpanNode implements Node{

        private String text;
        public SpanNode(String text){
            this.text = text;
        }

        public String getText() {
            return "<span>" + text + "</span>";
        }
    }

    /**
     * 所有装饰器父类
     *
     */
    abstract class Decorator implements Node{

        protected final Node target;

        protected Decorator(Node target) {
            this.target = target;
        }

    }

    /**
     * 装饰器，加粗效果
     */
    class BoldDecorator extends Decorator {

        public BoldDecorator(Node target){
            super(target);
        }

        public String getText() {
            return "<b>" + target.getText() + "</b>";
        }
    }

    /**
     * 装饰器，斜体效果
     */
    class ItalicDecorator extends Decorator {

        public ItalicDecorator(Node target){
            super(target);
        }

        public String getText() {
            return "<i>" + target.getText() + "</i>";
        }
    }


    @Test
    void main() throws IOException{

        ////创建原始的数据源
        //InputStream input = new FileInputStream("test.gz");
        ////增加缓冲功能
        //InputStream input1 = new BufferedInputStream(input);
        ////增加解压缩功能
        //InputStream input2 = new GZIPInputStream(input1);

        Node node = new ItalicDecorator(new BoldDecorator(new SpanNode("Hello World")));

        System.out.println(node.getText());

    }
}


