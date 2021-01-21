<?php
namespace api;

use api\lib\Curl;
use api\lib\Openssl;

class Client {
    
    public static $instance = null;
    public $options = null;
    public $curl = null;
    public $rsa = null;
    public $error = '';


    public static $url = '';

    public $merNo = '';
    private $private_key = '';
    private $public_key = '';

    public function __construct($options = array()){
        
        if(is_string($options)) $options = json_decode($options);
        if(is_array($options)) $options =  (object) $options;
        
        $options = clone $options;
        foreach($options as $name => $item){
            if(property_exists($this, $name)){
                $this->$name = $item;
                unset($options->$name);
            }
        }
        
        if(!is_null($options)) $this->options = $options;
        
        $this->curl = Curl::instance()->header('Content-type: application/x-www-form-urlencoded; charset=UTF-8');
        $this->rsa = new Openssl();
        $this->rsa->setPrivateKey($this->private_key)->setPublicKey($this->public_key);

        
    }
    

    public static function instance($options = array()){

        if(!isset(static::$instance)){
            static::$instance = new static($options);
        }
        return static::$instance;
    }
    
    
    public function param($data = array()){

        $data += array(
            'merNo' => $this->merNo,
            'version' => 'V1.0',
        );

        $data['signature'] = $this->rsa->signature($this->sign($data));

        return $this->curl->url(static::$url)->data($data);
        
    }
    

    /**
     * 生成签名
     * @param array $data
     * @return string
     */
    public function sign($data = array()){

        ksort($data);
        $buffer = '';
        foreach($data as $key => $value){
            if($value === '') continue;
            $buffer .= "$key=$value&";
        }

        return rtrim($buffer, '&');
        
    }
    

    
    public function execute($data = array()){
        
        return $this->format($this->param($data)->post());
    }
    
    
    public function format($result){

        $result = explode('&', $result);

        $data = array();
        foreach($result as $item){

            $item = explode('=', $item);
            $data[$item[0]] = $item[1];
        }

        return $data;

    }
    
    
    public function error($message = '', $code = ''){

        if(func_num_args() == 0) return $this->error;
        $this->error = $message . "( $code )";
        
    }

    public function verify($data){

        $sign = $data['signature'];
        unset($data['signature']);

        $data = $this->sign($data);
        return $this->rsa->verify($data, $sign);

    }
    


}