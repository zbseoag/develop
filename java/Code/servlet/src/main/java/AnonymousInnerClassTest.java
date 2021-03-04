import javax.swing.*;
import java.awt.*;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.time.Instant;


public class AnonymousInnerClassTest{

    public static void main(String[] args){

        var clock = new TalkingClock();
        clock.start(1000, true);

        // keep program running until the user selects "OK"
        JOptionPane.showMessageDialog(null, "Quit program?");
        System.exit(0);
    }
}


class TalkingClock{

    public void start(int interval, boolean beep){
        var listener = new ActionListener(){

            public void actionPerformed(ActionEvent event){

                System.out.println("The time is " + Instant.ofEpochMilli(event.getWhen()));
                if(beep) Toolkit.getDefaultToolkit().beep();
            }
        };

        var timer = new Timer(interval, listener);
        timer.start();
    }
}
