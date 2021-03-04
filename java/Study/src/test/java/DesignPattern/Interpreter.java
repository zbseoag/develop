package DesignPattern;

import org.junit.jupiter.api.Test;

import java.time.LocalDate;
import java.time.LocalTime;

/**
 * 解释器
 * 给定一个语言，定义它的文法的一种表示，并定义一个解释器，这个解释器使用该表示来解释语言中的句子。
 *
 */
public class Interpreter {


    public static void log(String format, Object... args) {

        int len = format.length();
        int argIndex = 0;
        StringBuilder sb = new StringBuilder(len + 20);
        char last = '\0';
        for (int i = 0; i < len; i++) {
            char ch = format.charAt(i);
            if (ch == '}') {
                if (last == '{') {
                    sb.deleteCharAt(sb.length() - 1);
                    sb.append(args[argIndex]);
                    argIndex++;
                } else {
                    sb.append(ch);
                }
            } else {
                sb.append(ch);
            }
            last = ch;
        }
        System.out.println(sb.toString());
    }


    @Test
    void main() {
        log("[{}] start {} at {}...", LocalTime.now().withNano(0), "engine", LocalDate.now());
    }


}
