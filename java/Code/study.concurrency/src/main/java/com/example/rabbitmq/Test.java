package com.example.rabbitmq;

import com.rabbitmq.client.*;

import java.io.IOException;
import java.util.Date;
import java.util.Iterator;
import java.util.LinkedList;
import java.util.List;
import java.util.concurrent.TimeoutException;

public class Test{

    private final static String QUEUE_NAME = "test_queue_name";
    private final static String HOST = "192.168.115.3";
    public static int port = 5672;
    public static String password = "guest";
    public static String username = "guest";
    public static String vhost = "/";


    public static void main(String[] args) throws Exception{

        send();

    }


    public static Connection connect() throws IOException, TimeoutException{

        ConnectionFactory factory = new ConnectionFactory();
        //factory.setUri("amqp://userName:password@hostName:portNumber/virtualHost");
        factory.setHost(HOST);
        factory.setPort(port);
        factory.setUsername(username);
        factory.setPassword(password);
        factory.setVirtualHost(vhost);

        return factory.newConnection();
    }

    /**
     * 发送消息
     * @throws IOException
     * @throws TimeoutException
     */
    public static void send() throws IOException, TimeoutException{

        try(Connection connection = connect(); Channel channel = connection.createChannel()){

            channel.queueDeclare(QUEUE_NAME, false, false, false, null);

            List<String> list = new LinkedList<String>();
            list.add("aaa");
            list.add("bbb");

            for(String message : list){

                channel.basicPublish("", QUEUE_NAME, null, message.getBytes());
                System.out.println("[x] Sent '" + message + "'");
            }

            list.clear();
            list.add("111");
            list.add("222");

            for(Iterator iter = list.iterator(); iter.hasNext();){

                String message = iter.next().toString();
                channel.basicPublish("", QUEUE_NAME, null, message.getBytes());
                System.out.println("[x] Sent '" + message + "'");

            }


        }


    }

    /**
     * 接收消息
     */
    public static void receive() throws Exception {

        Connection connection = connect();
        Channel channel = connection.createChannel();

        channel.queueDeclare(QUEUE_NAME, false, false, false, null);

        System.out.println("[*] Waiting for messages...");

        DeliverCallback deliverCallback = (consumerTag, delivery) -> {
            String message = new String(delivery.getBody(), "UTF-8");
            System.out.println("[x] Received '" + message + "'");
        };

        channel.basicConsume(QUEUE_NAME, true, deliverCallback, consumerTag -> { });

    }

    /**
     * 推送消息
     */
    public static void publisher() throws IOException, TimeoutException{
        // 创建一个连接
        Connection conn = connect();
        if (conn != null) {
            try {
                // 创建通道
                Channel channel = conn.createChannel();
                // 声明队列【参数说明：参数一：队列名称，参数二：是否持久化；参数三：是否独占模式；参数四：消费者断开连接时是否删除队列；参数五：消息其他参数】
                channel.queueDeclare(QUEUE_NAME, false, false, false, null);
                String content = String.format("当前时间 %s", new Date().getTime());
                // 发送内容【参数说明：参数一：交换机名称；参数二：队列名称，参数三：消息的其他属性-routing headers，此属性为MessageProperties.PERSISTENT_TEXT_PLAIN用于设置纯文本消息存储到硬盘；参数四：消息主体】
                channel.basicPublish("", QUEUE_NAME, null, content.getBytes("UTF-8"));
                System.out.println("发送消息：" + content);
                // 关闭连接
                channel.close();
                conn.close();
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
    }

    /**
     * 消费消息
     */
    public static void consumer() throws IOException, TimeoutException{
        // 创建一个连接
        Connection conn = connect();
        if(conn != null){
            try{
                // 创建通道
                Channel channel = conn.createChannel();
                // 声明队列【参数说明：参数一：队列名称，参数二：是否持久化；参数三：是否独占模式；参数四：消费者断开连接时是否删除队列；参数五：消息其他参数】
                channel.queueDeclare(QUEUE_NAME, false, false, false, null);

                // 创建订阅器，并接受消息
                channel.basicConsume(QUEUE_NAME, false, "", new DefaultConsumer(channel){
                    @Override
                    public void handleDelivery(String consumerTag, Envelope envelope, AMQP.BasicProperties properties,
                                               byte[] body) throws IOException{
                        String routingKey = envelope.getRoutingKey(); // 队列名称
                        String contentType = properties.getContentType(); // 内容类型
                        String content = new String(body, "utf-8"); // 消息正文
                        System.out.println("收到消息：" + content);
                        channel.basicAck(envelope.getDeliveryTag(), false); // 手动确认消息【参数说明：参数一：该消息的index；参数二：是否批量应答，true批量确认小于index的消息】
                    }
                });

            }catch(Exception e){
                e.printStackTrace();
            }
        }
    }






}
