<?php

/**
 *  使用示例
#定义名称
define('ExchangeName', 'exchange_name_15');
define('QueueName',    'queue_name_15');
define('RoutingKey',   'routing_key_15');

if(count($argv) > 1){

    $amqp = new Amqp();
    $amqp->channel();
    $amqp->exchange(ExchangeName, AMQP_EX_TYPE_DIRECT, AMQP_DURABLE)->declareExchange();
    $amqp->queue(QueueName, AMQP_DURABLE)->declareQueue();
    $amqp->bindQueue(RoutingKey);
    $amqp->publish($argv[1], RoutingKey);
    $amqp->disconnect();

}else{

    $amqp = new Amqp();
    $amqp->channel();
    $amqp->exchange(ExchangeName, AMQP_EX_TYPE_DIRECT, AMQP_DURABLE)->declareExchange();
    $amqp->queue(QueueName, AMQP_DURABLE)->declareQueue();
    $amqp->bindQueue(RoutingKey);

    //接收消息
    $amqp->consume(function($envelope, $queue){
    $msg = $envelope->getBody();
    echo $msg . "\n";
    }, AMQP_AUTOACK); //自动应答

}
 */

class Amqp extends AMQPConnection {

//    protected AMQPChannel $channel;
//    protected AMQPExchange $exchange;
//    protected AMQPQueue $queue;

    protected  $channel;
    protected  $exchange;
    protected  $queue;

    public function __construct($host='127.0.0.1', $port=5672, $user='guest', $passwd='guest', $vhost='/'){

        $credentials = is_array($host)? $host : [
            'host'      => $host,
            'port'      => $port,
            'login'     => $user,
            'password'  => $passwd,
            'vhost'     => $vhost
        ];
        parent::__construct($credentials);

    }


    /**
     * 创建频道
     * @return AMQPChannel
     * @throws AMQPConnectionException
     */
    public function channel(){

        if(!$this->channel){
            parent::connect();
            $this->channel = new \AMQPChannel($this);
        }
        return $this->channel;
    }

    /**
     * 创建交换器
     * @param string $name
     * @param string $type
     * @param null $flags
     * @return AMQPExchange
     * @throws AMQPConnectionException
     * @throws AMQPExchangeException
     */
    public function exchange($name='', $type='', $flags=null){

        if(!$this->exchange){
            $this->exchange = new \AMQPExchange($this->channel());

        }
        $name && $this->exchange->setName($name);
        $type && $this->exchange->setType($type);
        is_integer($flags) && $this->exchange->setFlags($flags);

        return $this->exchange;

    }

    /**
     * 创建队列
     * @param string $name
     * @param null $flags
     * @return AMQPQueue
     * @throws AMQPConnectionException
     * @throws AMQPQueueException
     */
    public function queue($name='', $flags=null){

        if(!$this->queue){
            $this->queue = new \AMQPQueue($this->channel());
        }

        $name && $this->queue->setName($name);
        is_integer($flags) && $this->queue->setFlags($flags);

        return $this->queue;

    }

    /**
     * 发布交换器
     * @return bool
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPExchangeException
     */
    public function declareExchange(){
        return $this->exchange()->declareExchange();
    }

    /**
     * 发布队列
     * @return int
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPQueueException
     */
    public function declareQueue(){
        return $this->queue()->declareQueue();
    }

    /**
     * 绑定队列
     * @param string $routing_key
     * @param array $arguments
     * @return bool
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPExchangeException
     * @throws AMQPQueueException
     */
    public function bindQueue($routing_key='', $arguments=[]){
        return $this->queue()->bind($this->exchange()->getName(), $routing_key, $arguments);
    }


    /**
     * 发布消息
     * @param callable|null $callback
     * @param int $flags
     * @param null $consumerTag
     * @return bool
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPExchangeException
     */
    public function publish($message, $routing_key=null,  $flags=AMQP_NOPARAM,  array $attributes=[]){
        return $this->exchange()->publish($message, $routing_key, $flags, $attributes);
    }

    public function consume(callable $callback=null, $flags=AMQP_NOPARAM, $consumerTag = null){
        $this->queue()->consume($callback, $flags, $consumerTag);
    }

    /**
     * 删除交换器
     * @param null $name
     * @param int $flags
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPExchangeException
     */
    public function deleteExchange($name = null, $flags=AMQP_NOPARAM){
        $this->exchange()->delete($name, $flags);
    }

    /**
     * 删除队列
     * @param int $flags
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPQueueException
     */
    public function deleteQueue($flags=AMQP_NOPARAM){
        $this->queue()->delete($flags);
    }


}


