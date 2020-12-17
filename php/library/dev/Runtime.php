<?php
namespace app\common\helper;

use app\common\model\Log;

class Runtime {
    
    protected static $instance;
    
    protected $message = ['trace'=>[], 'error'=>[]];
    
    protected $type = 'message';

    protected function __construct() {}
    
    public static function instance(){
        
        if(is_null(self::$instance)){
            self::$instance = new static();
        }
        return self::$instance;
    }
    
    
    public function __toString(){
        return json_encode($this->message, JSON_UNESCAPED_UNICODE);
    }
    
    public function trace($string = '', $agrs = ''){
    
        $type = 'trace';
        if(func_num_args() == 0) return $this->message[$type];
        $args = func_get_args();
        
        array_unshift($args, $type);
        return  call_user_func_array([$this, 'message'], $args);
        
    }
    

    
    public function error($string = '', $agrs = ''){
        
        $type = 'error';
        if(func_num_args() == 0) return $this->message[$type];
        $args = func_get_args();
        array_unshift($args, $type);
        
        return  call_user_func_array([$this, 'message'], $args);
        
    }
    
    
    public function message($type = '', $string = '', $args = ''){
    
        $type || $type = $this->type;
        if(func_num_args() == 0) return $this->message;
        $args = array_slice(func_get_args(), 2);
        
        if($string instanceof \Exception){
            $string =  ' 文件: '. basename($string->getFile()) . ':' . $string->getLine() . ' 行, 消息: ' . $string->getMessage();
        }
        
        foreach($args as $key => $value){
            $string = str_replace('{'.$key.'}', $value, $string);
        }
        $this->message[$type][] = $string;
        
        return $this;
        
    }
    
    
    
    public function log($title = '', $data = '', $remark = ''){
    
        $title || $title = 'Runtime';
        $data || $data = $this->message;
        
        if(!is_string($data)) $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $data = [
            'title' => $title,
            'data' => $data,
            'remark' => $remark,
            'create_time' => date('Y-m-d H:i:s'),
        ];
        
        Log::instance()->allowField(true)->insert($data);
        
        return $this;
    }
    
    public function output(){
        echo $this;
    }
    
    
    
}