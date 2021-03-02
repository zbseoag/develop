import org.junit.jupiter.api.Test;

import java.time.*;
import java.time.format.DateTimeFormatter;
import java.time.format.FormatStyle;
import java.time.temporal.TemporalAdjusters;
import java.util.Calendar;
import java.util.Date;
import java.util.Locale;
import java.util.TimeZone;


public class TimeTest{

    /**
     * 从Java 8开始，java.time包提供了新的日期和时间API，主要涉及的类型有：
     *
     * 本地日期和时间：LocalDateTime，LocalDate，LocalTime；
     * 带时区的日期和时间：ZonedDateTime；
     * 时刻：Instant；
     * 时区：ZoneId，ZoneOffset；
     * 时间间隔：Duration。
     */

    @Test
    public void LocalDateTime(){

        {
            LocalDate d = LocalDate.now(); // 当前日期
            LocalTime t = LocalTime.now(); // 当前时间
            LocalDateTime dt = LocalDateTime.now(); // 当前日期和时间
            System.out.println(d); // 严格按照ISO 8601格式打印
            System.out.println(t); // 严格按照ISO 8601格式打印
            System.out.println(dt); // 严格按照ISO 8601格式打印
        }

        {
            LocalDateTime dt = LocalDateTime.now(); // 当前日期和时间
            LocalDate d = dt.toLocalDate(); // 转换到当前日期
            LocalTime t = dt.toLocalTime(); // 转换到当前时间
        }

        {
            // 指定日期和时间:
            LocalDate d2 = LocalDate.of(2019, 11, 30); // 2019-11-30, 注意11=11月
            LocalTime t2 = LocalTime.of(15, 16, 17); // 15:16:17
            LocalDateTime dt2 = LocalDateTime.of(2019, 11, 30, 15, 16, 17);
            LocalDateTime dt3 = LocalDateTime.of(d2, t2);

        }
        {
            LocalDateTime dt = LocalDateTime.parse("2019-11-19T15:16:17");
            LocalDate d = LocalDate.parse("2019-11-19");
            LocalTime t = LocalTime.parse("15:16:17");
        }

        {
            // 自定义格式化:
            DateTimeFormatter dtf = DateTimeFormatter.ofPattern("yyyy/MM/dd HH:mm:ss");
            System.out.println(dtf.format(LocalDateTime.now()));

            // 用自定义格式解析:
            LocalDateTime dt2 = LocalDateTime.parse("2019/11/30 15:16:17", dtf);
            System.out.println(dt2);
        }

        {
            LocalDateTime dt = LocalDateTime.of(2019, 10, 26, 20, 30, 59);
            System.out.println(dt);
            // 加5天减3小时:
            LocalDateTime dt2 = dt.plusDays(5).minusHours(3);
            System.out.println(dt2); // 2019-10-31T17:30:59
            // 减1月:
            LocalDateTime dt3 = dt2.minusMonths(1);
            System.out.println(dt3); // 2019-09-30T17:30:59
        }

        {
            LocalDateTime dt = LocalDateTime.of(2019, 10, 26, 20, 30, 59);
            System.out.println(dt);
            // 日期变为31日:
            LocalDateTime dt2 = dt.withDayOfMonth(31);
            System.out.println(dt2); // 2019-10-31T20:30:59
            // 月份变为9:
            LocalDateTime dt3 = dt2.withMonth(9);
            System.out.println(dt3); // 2019-09-30T20:30:59
        }

        {
            // 本月第一天0:00时刻:
            LocalDateTime firstDay = LocalDate.now().withDayOfMonth(1).atStartOfDay();
            System.out.println(firstDay);

            // 本月最后1天:
            LocalDate lastDay = LocalDate.now().with(TemporalAdjusters.lastDayOfMonth());
            System.out.println(lastDay);

            // 下月第1天:
            LocalDate nextMonthFirstDay = LocalDate.now().with(TemporalAdjusters.firstDayOfNextMonth());
            System.out.println(nextMonthFirstDay);

            // 本月第1个周一:
            LocalDate firstWeekday = LocalDate.now().with(TemporalAdjusters.firstInMonth(DayOfWeek.MONDAY));
            System.out.println(firstWeekday);
        }

        {
            LocalDateTime now = LocalDateTime.now();
            LocalDateTime target = LocalDateTime.of(2019, 11, 19, 8, 15, 0);
            System.out.println(now.isBefore(target));
            System.out.println(LocalDate.now().isBefore(LocalDate.of(2019, 11, 19)));
            System.out.println(LocalTime.now().isAfter(LocalTime.parse("08:15:00")));
        }

        {
            LocalDateTime start = LocalDateTime.of(2019, 11, 19, 8, 15, 0);
            LocalDateTime end = LocalDateTime.of(2020, 1, 9, 19, 25, 30);
            Duration d = Duration.between(start, end);
            System.out.println(d); // PT1235H10M30S

            Period p = LocalDate.of(2019, 11, 19).until(LocalDate.of(2020, 1, 9));
            System.out.println(p); // P1M21D
        }


    }

    @Test
    public void ZonedDateTime(){

        {
            ZonedDateTime zbj = ZonedDateTime.now(); // 默认时区
            ZonedDateTime zny = ZonedDateTime.now(ZoneId.of("America/New_York")); // 用指定时区获取当前时间
            System.out.println(zbj);
            System.out.println(zny);
        }

        {
            LocalDateTime ldt = LocalDateTime.of(2019, 9, 15, 15, 16, 17);
            ZonedDateTime zbj = ldt.atZone(ZoneId.systemDefault());
            ZonedDateTime zny = ldt.atZone(ZoneId.of("America/New_York"));
            System.out.println(zbj);
            System.out.println(zny);
        }

        {
            // 以中国时区获取当前时间:
            ZonedDateTime zbj = ZonedDateTime.now(ZoneId.of("Asia/Shanghai"));
            // 转换为纽约时间:
            ZonedDateTime zny = zbj.withZoneSameInstant(ZoneId.of("America/New_York"));
            System.out.println(zbj);
            System.out.println(zny);
        }
    }
    @Test
    public void DateTimeFormatter(){
        {
            ZonedDateTime zdt = ZonedDateTime.now();
            var formatter = DateTimeFormatter.ofPattern("yyyy-MM-dd'T'HH:mm ZZZZ");
            System.out.println(formatter.format(zdt));

            var zhFormatter = DateTimeFormatter.ofPattern("yyyy MMM dd EE HH:mm", Locale.CHINA);
            System.out.println(zhFormatter.format(zdt));
        }
        {   //不同格式打印
            var ldt = LocalDateTime.now();
            System.out.println(DateTimeFormatter.ISO_DATE.format(ldt));
            System.out.println(DateTimeFormatter.ISO_DATE_TIME.format(ldt));
        }

    }

    @Test
    public void Instant(){

        {
            Instant now = Instant.now();
            System.out.println(now.getEpochSecond()); // 秒
            System.out.println(now.toEpochMilli()); // 毫秒
        }

        {
            // 以指定时间戳创建Instant:
            Instant ins = Instant.ofEpochSecond(1568568760);
            ZonedDateTime zdt = ins.atZone(ZoneId.systemDefault());
            System.out.println(zdt); // 2019-09-16T01:32:40+08:00[Asia/Shanghai]
        }

    }

    @Test
    public void OldToNew(){

        {
            // ZonedDateTime -> long:
            ZonedDateTime zdt = ZonedDateTime.now();
            long ts = zdt.toEpochSecond() * 1000;

            // long -> Date:
            Date date = new Date(ts);

            // long -> Calendar:
            Calendar calendar = Calendar.getInstance();
            calendar.clear();
            calendar.setTimeZone(TimeZone.getTimeZone(zdt.getZone().getId()));
            calendar.setTimeInMillis(zdt.toEpochSecond() * 1000);
        }

        {

            Instant ins = Instant.ofEpochMilli(1574208900000L);
            DateTimeFormatter f = DateTimeFormatter.ofLocalizedDateTime(FormatStyle.MEDIUM, FormatStyle.SHORT);
            String time = f.withLocale(Locale.CHINA).format(ZonedDateTime.ofInstant(ins, ZoneId.of("Asia/Shanghai")));
            System.out.println(time);

        }

    }


}
