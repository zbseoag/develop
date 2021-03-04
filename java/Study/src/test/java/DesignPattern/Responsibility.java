package DesignPattern;

import org.junit.jupiter.api.Test;

/**
 * 使多个对象都有机会处理请求，从而避免请求的发送者和接收者之间的耦合关系。将这些对象连成一条链，并沿着这条链传递该请求，直到有一个对象处理它为止。
 *
 * 责任链模式（Chain of Responsibility）是一种处理请求的模式，它让多个处理器都有机会处理该请求，直到其中某个处理成功为止。责任链模式把多个处理器串成链，然后让请求在链上传递：
 */
public class Responsibility {

    //抽象处领导
    abstract class Leader {

        protected Leader next;

        public Leader(){

        }

        public Leader(Leader next){
            this.next = next;
        }

        public void setNext(Leader next) {
            this.next = next;
        }
        public Leader getNext() {
            return next;
        }
        //处理请求的方法
        public abstract void handleRequest(int days);

        protected void nextHandleRequest(int days){

            if (getNext() != null) {
                getNext().handleRequest(days);
            } else {
                System.out.println("抱歉，没人能批准 " + days +" 天假！");
            }
        }

    }

    //班主任 2 天
    class ClassAdviser extends Leader {

        public ClassAdviser(){

        }

        public ClassAdviser(Leader next){
            super(next);
        }


        public void handleRequest(int days) {

            if (days <= 2) {
                System.out.println("班主任批准请假" + days + "天。");
            } else {
                nextHandleRequest(days);
            }
        }
    }
    //系主任类 7 天
    class DepartmentHead extends Leader {

        public DepartmentHead(){

        }

        public DepartmentHead(Leader next){
            super(next);
        }

        public void handleRequest(int days) {
            if (days <= 7) {
                System.out.println("系主任批准请假" + days + "天。");
            } else {
                nextHandleRequest(days);
            }
        }
    }

    //校长 10 天
    class Principal extends Leader {

        public Principal(){

        }

        public Principal(Leader next){
            super(next);
        }


        public void handleRequest(int days) {
            if (days <= 10) {
                System.out.println("校长批准请假" + days + "天。");
            } else {
                nextHandleRequest(days);
            }
        }
    }

    @Test
    void main() {

        //组装责任链
        Leader teacher1 = new ClassAdviser();
        Leader teacher2 = new DepartmentHead();
        Leader teacher3 = new Principal();
        teacher1.setNext(teacher2);
        teacher2.setNext(teacher3);
        teacher1.handleRequest(5); //请假五天

        Leader leader = new ClassAdviser(new DepartmentHead(new Principal()));
        leader.handleRequest(8);//请假八天

    }

}
