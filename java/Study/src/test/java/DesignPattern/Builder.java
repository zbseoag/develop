package DesignPattern;

import org.junit.jupiter.api.Test;

/**
 * 生成器
 * 将一个复杂对象的构建与它的表示分离，使得同样的构建过程可以创建不同的表示。
 */

public class Builder {


    class HeadingBuilder {

        public String create(String line) {

            int n = 0;
            while (line.charAt(0) == '#') {
                n++;
                line = line.substring(1);
            }
            return String.format("<h%d>%s</h%d>", n, line.strip(), n);
        }
    }

    class QuoteBuilder {

        public String create(String line) {

            int n = 0;
            while (line.charAt(0) == '>') {
                n++;
                line = line.substring(1);
            }
            return String.format("<h%d>%s</h%d>", n, line.strip(), n);
        }
    }

    class HtmlBuilder {

        private HeadingBuilder headingBuilder = new HeadingBuilder();
        private QuoteBuilder quoteBuilder = new QuoteBuilder();

        public String toHtml(String markdown) {

            StringBuilder buffer = new StringBuilder();

            markdown.lines().forEach(line -> {

                if (line.startsWith("#")) {
                    buffer.append(headingBuilder.create(line)).append('\n');
                } else {
                    buffer.append(quoteBuilder.create(line)).append('\n');
                }

            });
            return buffer.toString();
        }
    }

    @Test
    void main(){

        System.out.println(new HtmlBuilder().toHtml("# this is a heading"));

        //String url = URLBuilder.builder().setDomain("www.test.com").setScheme("https").setPath("/").setQuery(Map.of("a", "123", "q", "K&R")).build();
    }
}
