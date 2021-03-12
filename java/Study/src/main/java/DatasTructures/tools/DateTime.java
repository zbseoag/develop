package DatasTructures.tools;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;


/**
 * 用法：
 * String time = new DateTime("2013-03-26").format("yyyy-MM-dd").add(Calendar.YEAR,1).output("yyyy年MM月dd分HH时ss分mm秒");
 * System.out.println(time);
 */

public class DateTime {

    public String time;
    public Date date;
    public SimpleDateFormat format;
    public Calendar calendar = Calendar.getInstance();


    public DateTime(){

        date = new Date();
        this.calendar.setTime(date);
    }


    public DateTime(long time){

        date = new Date(time);
        this.calendar.setTime(date);
    }

    public DateTime(String time){

        this.time = time;
    }

    public DateTime format(String format) throws ParseException{

        this.format = new SimpleDateFormat(format);

        this.date= this.format.parse(time);
        this.calendar.setTime(date);

        return this;
    }


    public DateTime calendar(Calendar calendar){

        this.calendar = calendar;
        this.calendar.setTime(date);
        
        return this;
    }

    public String output(){
        return this.output("yyyy-MM-dd HH:ss:mm");
    }

    public String output(String format){

        date = calendar.getTime();
        return new SimpleDateFormat(format).format(date);
    }


    public DateTime add(int field, int amount){

        this.calendar.add(field, amount);
        return this;
    }

    public DateTime set(int field, int value){

        this.calendar.set(field, value);
        return this;
    }


}

