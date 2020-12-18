<?php

class Socket{
    
    public $sockets;
    public $users;
    public $master;
    
    private $sda = array();//已接收的数据
    private $slen = array();//数据总长度
    private $sjen = array();//接收数据的长度
    private $ar = array();//加密key
    private $n = array();
    
    public function __construct($address, $port){
        
        $this->master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($this->master, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_bind($this->master, $address, $port);
        socket_listen($this->master);
        $this->sockets['master'] = $this->master;

    }
    
    
    public function start(){
        
        while(true){
            
            $write = $except = null;
            $read = array_values($this->sockets);
            socket_select($read, $write, $except, null);
    
            foreach($read as $socket){
                
                if($socket == $this->master){
                    
                    $uid = uniqid();
                    $this->sockets[$uid] = socket_accept($this->master);
                    $this->users[$uid] = false;
                    continue;
                }
                
                $content = '';
                do{
                    $length = socket_recv($socket, $buffer, 1000, 0);
                    $content .= $buffer;
    
                }while($length == 1000);
                
                $uid = $this->getUid($socket);
                if(empty($uid)) continue;
                
                //握手
                if($this->users[$uid] == false){
                    $this->link($uid, $content); continue;
                }
                
                //退出
                if($length == ''){
                    $this->close($uid); continue;
                }
                //聊天
                $this->chat($uid, $this->decode($content, $uid));
                    
                
            }
    
        }
        
    }
    

    function link($uid, $buffer){
        
        $buf  = substr($buffer, strpos($buffer,'Sec-WebSocket-Key:') + 18);
        $key  = trim(substr($buf, 0, strpos($buf,"\r\n")));
        $new_key = base64_encode(sha1($key."258EAFA5-E914-47DA-95CA-C5AB0DC85B11",true));
        
        $new_message = "HTTP/1.1 101 Switching Protocols\r\n";
        $new_message .= "Upgrade: websocket\r\n";
        $new_message .= "Sec-WebSocket-Version: 13\r\n";
        $new_message .= "Connection: Upgrade\r\n";
        $new_message .= "Sec-WebSocket-Accept: " . $new_key . "\r\n\r\n";
        
        socket_write($this->sockets[$uid],$new_message,strlen($new_message));
        $this->users[$uid] = true;
    }
    
    

    function chat($uid, $message){

        $data = json_decode($message);
        if(empty($data)) return false;
        
        if($data->event == 'join'){
            $this->users[$uid] = $data->name;
            $this->users['room'][] = ['uid'=>$uid, 'uname'=>$data->name];
            $send = ['uid'=>$uid, 'uname'=>$data->name, 'users'=> $this->users['room']];
        }else{
            $send = $data->message;
        }
        $receive = isset($this->users[$data->receive])? $data->receive : 'all';
        $this->send($data->event, $uid, $send, $receive);
    }
    
    

    function send($event, $sender, $data, $receive = 'all'){
        
        $send = array('event' => $event, 'sender' => $sender, 'receive'=>$receive, 'data'=>$data, 'time'=> date('m-d H:i:s'),);
        
        $send = $this->encode(json_encode($send));
        if($receive == 'all'){
            $sockets = $this->sockets;
            unset($sockets['master']);
        }else{
            
            if(!is_array($receive)) $receive = array($receive);
            foreach($receive as $uid){
                if(isset($this->sockets[$uid])) $sockets[$uid] = $this->sockets[$uid];
            }
            if(isset($this->sockets[$sender])) $sockets[$sender] = $this->sockets[$sender];
        }

        foreach($sockets as $uid => $socket){
            socket_write($socket, $send, strlen($send));
        }
        

    }
    
    //用户退出
    function close($uid){
    
        socket_close($this->sockets[$uid]);
        unset($this->sockets[$uid]);
        $this->send('close', 'system', ['uid' => $uid]);
    
    }
    
    
    function getUid($socket){
        
        foreach ($this->sockets as $uid => $value){
            if($socket == $value)
            return $uid;
        }
    }
    

    
    function decode($str,$key){
    
        $mask = array();
        $data = '';
        $msg = unpack('H*',$str);
        $head = substr($msg[1],0,2);
        if ($head == '81' && !isset($this->slen[$key])) {
            $len=substr($msg[1],2,2);
            $len=hexdec($len);
            if(substr($msg[1],2,2)=='fe'){
                $len=substr($msg[1],4,4);
                $len=hexdec($len);
                $msg[1]=substr($msg[1],4);
            }else if(substr($msg[1],2,2)=='ff'){
                $len=substr($msg[1],4,16);
                $len=hexdec($len);
                $msg[1]=substr($msg[1],16);
            }
            $mask[] = hexdec(substr($msg[1],4,2));
            $mask[] = hexdec(substr($msg[1],6,2));
            $mask[] = hexdec(substr($msg[1],8,2));
            $mask[] = hexdec(substr($msg[1],10,2));
            $s = 12;
            $n=0;
        }else if($this->slen[$key] > 0){
            $len=$this->slen[$key];
            $mask=$this->ar[$key];
            $n=$this->n[$key];
            $s = 0;
        }
    
        $e = strlen($msg[1])-2;
        for ($i=$s; $i<= $e; $i+= 2) {
            $data .= chr($mask[$n%4]^hexdec(substr($msg[1],$i,2)));
            $n++;
        }
        $dlen=strlen($data);
    
        if($len > 255 && $len > $dlen+intval($this->sjen[$key])){
            $this->ar[$key]=$mask;
            $this->slen[$key]=$len;
            $this->sjen[$key]=$dlen+intval($this->sjen[$key]);
            $this->sda[$key]=$this->sda[$key].$data;
            $this->n[$key]=$n;
            return false;
        }else{
            unset($this->ar[$key],$this->slen[$key],$this->sjen[$key],$this->n[$key]);
            $data=$this->sda[$key].$data;
            unset($this->sda[$key]);
            return $data;
        }
    
    }
    
    
    function encode($msg){
    
        $frame = array();
        $frame[0] = '81';
        $len = strlen($msg);
        if($len < 126){
            $frame[1] = $len<16?'0'.dechex($len):dechex($len);
        }else if($len < 65025){
            $s=dechex($len);
            $frame[1]='7e'.str_repeat('0',4-strlen($s)).$s;
        }else{
            $s=dechex($len);
            $frame[1]='7f'.str_repeat('0',16-strlen($s)).$s;
        }
        $frame[2] = $this->ord_hex($msg);
        $data = implode('',$frame);
        return pack("H*", $data);
    }
    
    function ord_hex($data)  {
        $msg = '';
        $l = strlen($data);
        for ($i= 0; $i<$l; $i++) {
            $msg .= dechex(ord($data{$i}));
        }
        return $msg;
    }
}



error_reporting(E_ALL ^ E_NOTICE);

ob_implicit_flush();

$sk = new Socket('192.168.45.57',8000);
$sk->start();
