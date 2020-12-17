<?php

class Service {


    
    /**
     * test function
     * @param array $user
     * @param array $age
     * @return object
     */
    public function test($user, $age){
        
        return $user . $age;
    }
    
    /**
     * test function
     * @param number $abc
     * @param int $def
     * @return double
     */
    public function tool($abc, $def){
        
        return $abc * $def;
    }
    
}

include("SoapDiscovery.class.php");

//第一个参数是类名（生成的wsdl文件就是以它来命名的），即Service类
//第二个参数是服务的名字（这个可以随便写）。 
$disco = new SoapDiscovery('Service', 'soap'); 
$disco->buildWSDL();

$server = new SoapServer('Service.wsdl', array('soap_version' => SOAP_1_2));
//注册Service类的所有方法
$server->setClass("Service");
//处理请求
$server->handle();