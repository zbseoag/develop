package DesignPattern;

/**
 * 用一个中介对象来封装一系列的对象交互。中介者使各个对象不需要显式地相互引用，从而使其耦合松散，而且可以独立地改变它们之间的交互。
 */

public class Mediator {

    interface MediatorInterface {
        /**
         * 发出响应
         */
        public void sendResponse(String content);

        /**
         * 做出请求
         */
        public void makeRequest();

        /**
         * 查询数据库
         */
        public void queryDb();
    }



}
