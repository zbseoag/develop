import org.junit.jupiter.api.Test;

import javax.swing.*;
import java.awt.*;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.io.IOException;
import java.nio.ByteBuffer;
import java.nio.channels.FileChannel;
import java.nio.charset.StandardCharsets;
import java.nio.file.FileVisitOption;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.time.Instant;
import java.util.Scanner;
import java.util.regex.MatchResult;
import java.util.regex.Pattern;
import java.util.stream.Stream;

public class TalkingClockTest {

    @Test
    void main(){

        var listener = new ActionListener(){
            public void actionPerformed(ActionEvent event){
                System.out.println("The time is " + Instant.ofEpochMilli(event.getWhen()));
                Toolkit.getDefaultToolkit().beep();
            }
        };

        var timer = new Timer(1000, listener);
        timer.start();

        JOptionPane.showMessageDialog(null, "Quit program?");
        System.exit(0);
    }


    public void test() throws IOException {

        FileChannel.open(Paths.get("/home")).tryLock();
        ByteBuffer.allocate(100);

        Pattern.compile("\\d").matcher("aaaaa").results().map(MatchResult::group).forEach(System.out::println);

        Pattern commas = Pattern.compile("\\s*,\\s*");

        Pattern.compile("\\s*,\\s*").split(",");

        var in = new Scanner(Paths.get("/home"), StandardCharsets.UTF_8);
        in.useDelimiter("\\s*");
        Stream<String> tockens = in.tokens();


        Pattern.compile("\\d").matcher("toms").replaceAll("aaaa");

        Path path = Paths.get("/home", "aa", "bbb");

        Files.newInputStream(Paths.get("aaa"));

        Files.walk(path , 10, FileVisitOption.FOLLOW_LINKS).forEach(p -> {
            System.out.println(p.getFileName());

        });

    }

}
