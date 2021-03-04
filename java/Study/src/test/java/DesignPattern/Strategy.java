package DesignPattern;

import org.junit.jupiter.api.Test;

import java.math.BigDecimal;
import java.math.RoundingMode;

/**
 * 策略
 * 定义一系列的算法，把它们一个个封装起来，并且使它们可相互替换。本模式使得算法可独立于使用它的客户而变化。
 * 策略模式的核心思想是在一个计算方法中能一些够改变结果步骤抽出来作为 “策略” 参数传进去，从而使得新增策略不必修改原有逻辑。
 *
 */
public class Strategy {

    interface DiscountStrategy {
        BigDecimal getDiscount(BigDecimal total);
    }

    class DiscountContext {

        private DiscountStrategy strategy = new UserDiscountStrategy();

        public void setStrategy(DiscountStrategy strategy) {
            this.strategy = strategy;
        }

        public BigDecimal calculatePrice(BigDecimal total) {
            return total.subtract(this.strategy.getDiscount(total)).setScale(2);
        }
    }

    class OverDiscountStrategy implements DiscountStrategy {

        @Override
        public BigDecimal getDiscount(BigDecimal total) {
            // 满100减20优惠:
            return total.compareTo(BigDecimal.valueOf(100)) >= 0 ? BigDecimal.valueOf(20) : BigDecimal.ZERO;
        }
    }

    class PrimeDiscountStrategy implements DiscountStrategy {

        @Override
        public BigDecimal getDiscount(BigDecimal total) {
            // Prime会员打七折:
            return total.multiply(new BigDecimal("0.3")).setScale(2, RoundingMode.DOWN);
        }
    }

    class UserDiscountStrategy implements DiscountStrategy {

        @Override
        public BigDecimal getDiscount(BigDecimal total) {
            // 普通会员打九折:
            return total.multiply(new BigDecimal("0.1")).setScale(2, RoundingMode.DOWN);
        }
    }


    @Test
    void main() {

        DiscountContext ctx = new DiscountContext();

        //默认使用普通会员折扣
        BigDecimal pay1 = ctx.calculatePrice(BigDecimal.valueOf(105));
        System.out.println(pay1);

        //使用满减折扣
        ctx.setStrategy(new OverDiscountStrategy());
        BigDecimal pay2 = ctx.calculatePrice(BigDecimal.valueOf(105));
        System.out.println(pay2);

        //使用Prime会员折扣
        ctx.setStrategy(new PrimeDiscountStrategy());
        BigDecimal pay3 = ctx.calculatePrice(BigDecimal.valueOf(105));
        System.out.println(pay3);

    }


}
