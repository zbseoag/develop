<?php
/**
 * SoapSever调用类
 * @author zbseoag
 * 
 */

class SoapSimple{
	
    //通信密钥
    private $agentKey = '';
    
    //服务器地址
    private $url = '';
    
    //设置 SoapClient 的参数
    public $options = array('cache_wsdl'=>0, 'trace'=>1);
    
    //要发送的数据
    public $data = '';
    
    //SoapClient对象
    public $client;
    
    //当发生错误时,返回的原始数据
    public $response = '';
    
    //错误消息
    public $error = '';
    
    //返回结果
    public $result = '';

    public function __construct($url='', $options='', $agentKey=''){
        
        if(!empty($url)) $this->url = $url;
        if(!empty($options)) $this->options = $options;
        if(!empty($agentKey)) $this->agentKey = $agentKey;
        
        $this->time = time();
        $this->client = new SoapClient($this->url, $this->options);

    }
    
	/**
	 * 设置发送数据
	 * @param string $data
	 * @param string $agentKey
	 * @return CallSoap
	 */
    public function data($data='', $agentKey=''){

        if(is_array($data)) $data = json_encode($data);
        if(!empty($agentKey)) $this->agentKey = $agentKey;
        $this->data = $data;
        $this->token = md5($this->data . $this->time . $this->agentKey);
        return $this;
    }
	
	/**
	 * 调用服务器方法
	 * @param unknown $method
	 * @param unknown $args
	 * @return string
	 */
	public function __call($method, $args){
	
	    //当调用方法传有参数时,第一个表示$data, 第二个表示$agentKey
		if(!empty($args)){
		    if(!isset($args[1])) $args[1] = '';
		    $this->data($args[0], $args[1]);
		}
		
		try{
		    //调用过程
			$this->result = call_user_func_array(array($this->client, $method), array($this->data, $this->time, $this->token));
		}catch(SoapFault $e){
		    
			$this->error = $e->getMessage();
			$this->response = $this->client->__getLastResponse();
		}
 
		//当调用方法传有$data参数时,清空数据
		if(!empty($args[0])) $this->data = '';//清空
		
		return $this->result;

    }
    
    /**
     * 查看服务器提供的方法列表
     * @return multitype:
     */
    public function getMethods(){
        return $this->client->__getFunctions();
    }
    

}




