import org.junit.jupiter.api.Test;

import java.time.*;
import java.time.temporal.TemporalAdjusters;

public class DateTimeTest {

    @Test
    void main() throws InterruptedException{

        Instant start = Instant.now();
        Thread.sleep(2001);
        Instant end = Instant.now();
        Duration cost = Duration.between(start, end);
        System.out.println(cost.toMillis());

        Duration cost2 = Duration.between(start, end);
        cost.multipliedBy(10).minus(cost2).isNegative();

        cost.toNanos();

        LocalDate.now().plusMonths(1);
        LocalDate.of(2019, 12, 15).plus(Period.ofYears(1));
        LocalDate.of(2014, 1, 1).plusDays(255).plus(Duration.ofDays(5));

        LocalDate firstTuesday = LocalDate.of(2018, 2, 1).with(TemporalAdjusters.nextOrSame(DayOfWeek.TUESDAY));

    }

}
