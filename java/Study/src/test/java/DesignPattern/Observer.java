package DesignPattern;

import org.junit.jupiter.api.Test;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

/**
 * 观察者
 * 定义对象间的一种一对多的依赖关系，当一个对象的状态发生改变时，所有依赖于它的对象都得到通知并被自动更新
 */
public class Observer {

    interface ProductObservable {

    }

    interface ProductObserver {

        void onPublished(Product product);
        void onPriceChanged(Product product);
    }

    class Store {

        private List<ProductObserver> observers = new ArrayList<>();
        private Map<String, Product> products = new HashMap<>();

        public void addObserver(ProductObserver observer) {
            this.observers.add(observer);
        }

        public void removeObserver(ProductObserver observer) {
            this.observers.remove(observer);
        }

        public void addNewProduct(String name, double price) {

            Product p = new Product(name, price);
            products.put(p.getName(), p);

            observers.forEach(o -> o.onPublished(p));

        }

        public void setProductPrice(String name, double price) {
            Product p = products.get(name);
            p.setPrice(price);
            observers.forEach(o -> o.onPriceChanged(p));
        }
    }


    class Product {

        private String name;
        private double price;

        public Product(String name, double price) {
            this.name = name;
            this.price = price;
        }

        public String getName() {
            return name;
        }

        public double getPrice() {
            return price;
        }

        void setPrice(double price) {
            this.price = price;
        }

        @Override
        public String toString() {
            return String.format("{Product: name=%s, price=%s}", name, price);
        }
    }



    class Customer implements ProductObserver {

        @Override
        public void onPublished(Product product) {
            System.out.println("[Customer] on product published: " + product);
        }

        @Override
        public void onPriceChanged(Product product) {
            System.out.println("[Customer] on product price changed: " + product);
        }
    }

    class Admin implements ProductObserver {

        @Override
        public void onPublished(Product product) {
            System.out.println("[Admin] on product published: " + product);
        }

        @Override
        public void onPriceChanged(Product product) {
            System.out.println("[Admin] on product price changed: " + product);
        }
    }


    @Test
    void main() {

        //被观察对象
        Store store = new Store();

        //添加观察者
        store.addObserver(new Admin());
        store.addObserver(new Customer());

        //匿名观察者
        store.addObserver(new ProductObserver() {
            @Override
            public void onPublished(Product product) {
                System.out.println("[Log] on product published: " + product);
            }

            @Override
            public void onPriceChanged(Product product) {
                System.out.println("[Log] on product price changed: " + product);
            }
        });

        //添加新商品
        store.addNewProduct("Design Patterns", 35.6);
        store.addNewProduct("Effective Java", 50.8);

        //设置商品价格
        store.setProductPrice("Design Patterns", 31.9);

    }


}
