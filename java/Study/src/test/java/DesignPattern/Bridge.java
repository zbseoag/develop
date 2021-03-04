package DesignPattern;
import org.junit.jupiter.api.Test;

/**
 * ===========================================================================================
 * 桥接
 * 将抽象部分与它的实现部分分离，使它们都可以独立地变化。
 * ===========================================================================================
 */

public class Bridge {


    abstract class Car {

        // 引用Engine
        protected Engine engine;

        public Car(Engine engine) {
            this.engine = engine;
        }

        public abstract void drive();

    }

    interface Engine {
        void start();
    }

    class HybridEngine implements Engine {

        public void start() {
            System.out.println("Start Hybrid Engine...");
        }
    }


    abstract class RefinedCar extends Car {

        public RefinedCar(Engine engine) {
            super(engine);
        }

        public void drive() {

            this.engine.start();
            System.out.println("Drive " + getBrand() + " car...");
        }

        public abstract String getBrand();

    }

    class BossCar extends RefinedCar {

        public BossCar(Engine engine) {
            super(engine);
        }

        public String getBrand() {
            return "Boss";
        }

    }


    @Test
    void main(){

        //不同子品牌的车，可以组合不同的发动机
        RefinedCar car = new BossCar(new HybridEngine());
        car.drive();
    }

}
