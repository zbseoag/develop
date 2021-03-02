import org.junit.jupiter.api.Test;

import java.util.regex.Matcher;
import java.util.regex.Pattern;

import static org.junit.jupiter.api.Assertions.*;

public class PatternTest{

    @Test
    public void start(){

        {
            Pattern pattern = Pattern.compile("(\\d+?)(0*)");
            Matcher matcher = pattern.matcher("1230000");
            if (matcher.matches()) {
                System.out.println("group1=" + matcher.group(1)); // "123"
                System.out.println("group2=" + matcher.group(2)); // "0000"
            }
        }

        {
            String s = "the quick brown fox jumps over the lazy dog.";
            Pattern p = Pattern.compile("\\wo\\w");
            Matcher m = p.matcher(s);
            while (m.find()) {
                System.out.println(s.substring(m.start(), m.end()));
            }
        }

        {
            String s = "The     quick\t\t brown   fox  jumps   over the  lazy dog.";
            String r = s.replaceAll("\\s+", " ");
            System.out.println(r); // "The quick brown fox jumps over the lazy dog."
        }


    }

}
