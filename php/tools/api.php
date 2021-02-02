<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Expose-Headers: X-My-Custom-Header, X-Another-Custom-Header');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: *');

$path = explode('/', key($_GET));

$path = array_reduce($path, function($carry, $item) use($path){  

    $key = $carry['key'];
    if($key == 0 && !empty($item)){
        $carry['action'] = $item;
    }else if($key > 0){
        if($key % 2 == 1) $carry['data'][$item] = $path[$key+1];
    }

    $carry['key']++;
    return $carry; 

}, [ 'action'=>'index',  'key' => 0, 'data' =>[ ]]); 


(new Api)->{$path['action']}($path['data']);

class Api {

    public $output = [];

    public function __destruct(){
        echo json_encode([ 'code'=>0, 'msg'=>'success',  'data' => $this->output], JSON_UNESCAPED_UNICODE);
    }

    public function index(){
        $this->output = [ 'id' => 1, 'name' => '张三', 'age' => 20 ];
    } 

}


