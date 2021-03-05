import java.text.NumberFormat;
import java.text.ParseException;
import java.util.Currency;
import java.util.Locale;

public class Chapter7 {

    public static void main(String[] args) throws ParseException{

        NumberFormat fmt = NumberFormat.getCurrencyInstance(Locale.CHINESE);
        System.out.println(fmt.format(1234.56));

        NumberFormat fmt2 = NumberFormat.getNumberInstance();
        Number input = fmt2.parse("1,234.56".trim());
        System.out.println(input.doubleValue());

        NumberFormat fmt3 = NumberFormat.getCurrencyInstance(Locale.CHINA);
        fmt3.setCurrency(Currency.getInstance("USD"));
        //System.out.println(fmt3.parse("123.45"));

    }
}
