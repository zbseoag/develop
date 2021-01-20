<?php
use api\Client;
use api\lib\Debug;

require_once './api/lib/Autoload.php';

//自动加载
Autoload::instance('api')->root(__DIR__)->exec();



class Demo {
    
    public function __construct(){

        $options = array(
            'merNo' => '800440370331676',

            'private_key' => 'prv.pem',
            'public_key'  => 'pub.pem',
        );


        $this->client = Client::instance($options);

    }

    public function wapPay(){

        $data['orderDate'] = date('Ymd');

        $data['returnUrl'] = 'http://test.com/test.php';
        $data['notifyUrl'] = 'http://test.com/test.php';
        return $this->client->execute($data);

    }

    public function verify($data){

        return $this->client->verify($data);

    }

    
}



$demo = new Demo();
$result = $demo->wapPay();

//打印返回数据
Debug::p($result);
//验证签名
Debug::p($demo->verify($result));