<?php
/**
author：zbseoag
仅限于本地使用,不要放在服务器上
因为没有任何安全过滤.
而且还能执行 php 代码.
**/
error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');

if(isset($_POST['data'])){

    echo '<style> *{padding:0; margin: 0; font:14px "微软雅黑";} table{ border-collapse: collapse;} tr{ border: 1px solid  #CCC; } td, th{ border: 1px solid  #CCC;} </style>';
    
    $action = $_POST['action'];
    $data = $_POST['data'];

    $tool = new Tool($data);
    if(method_exists($tool, $action)){

        $tool->$action();
    }else if(function_exists($action)){
        //执行函数
        $tool->output = $action($data);

    }else{
        $tool->output = '未定义的工具: ' . $action;
    }

    exit;
}

?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>我的工具包</title>
</head>
<style>
*{padding:0; margin: 0; font:14px "微软雅黑";}
html,body{ width:100%;}
button{padding:4px 10px;}
.button {padding:4px 10px; min-width: 110px; margin:4px 0; }
li{ margin-bottom: 10px;}
</style>
<body style="padding:10px; box-sizing: border-box; ">

<form id="form" action=""  method="post" target="iframe" autocomplete="off">
<ul style="list-style:none;">
<li><textarea name="data"  id="data"style="width: 100%;padding: 4px;font:16px 'Courier New'; box-sizing: border-box;" rows="25"></textarea></li>
<li style="position: absolute; top: 10px; right: 10px;"><button type="reset">清空</button>&nbsp;<button onclick="addon()" type="button">载入</button></li>
</ul>
<input id="action" type="hidden" name="action" />
</form>

<button class="button" value="md5" type="button">md5</button>
<button class="button" data-switch-value="strtolower|strtoupper"  type="button">大小写</button>
<button class="button" data-switch-value="urlencode|urldecode|http_build_query" type="button">url 编解码</button>
<button class="button" value="datetime" type="button">时间戳</button>
<button class="button" value="deUnicode" type="button">unicode 解码</button>
<button class="button" value="select2Array" type="button">&lt;select&gt;转数组</button>
<button class="button" value="table2Array" type="button">&lt;table&gt;转数组</button>
<button class="button" value="json2Array" type="button">JSON 转数组</button>
<button class="button" value="sqlField2Array" type="button">SQL字段转数组</button>
<button class="button" value="filter" type="button">清除空白</button>
<button class="button" value="download" type="button">批量下载</button>


<iframe id="iframe" name="iframe" style="border:none; margin-top:20px; display:block; width:100%;" onload="this.height=iframe.document.body.scrollHeight;" ></iframe>
</body>
</html>
<script>
function addon(){
    var content = document.getElementById('iframe').contentWindow.document.getElementsByTagName('pre')[0];
    document.getElementById('data').value = content.innerText;
}

var buttons = document.getElementsByClassName('button');
for(var i in buttons){

    buttons[i].onclick = function(){
        
        var form = document.getElementById('form');
        var index = 0;
        if(this.hasAttribute('data-switch-value')){

            var switch_value = this.getAttribute('data-switch-value').split('|');
            for(var i in switch_value){
                if(this.value == switch_value[i]){
                    if(i == switch_value.length - 1) break;
                    index = ++i; break;
                }

            }
            this.value = switch_value[index];
        }

        document.getElementById('action').setAttribute('value', this.getAttribute('value'));
        form.submit();
        
    }
    
}




</script>

<?php

class Helper {
  

        /**
         * 格式化数据
         * @param $data
         * @return string
         */
        public static function format($data){
            
            if(in_array($data, array('TRUE','true', 'false', 'FALSE', 'null', 'NULL'), true )) $data = "\"$data\"";
            if(is_bool($data)) $data = $data? 'true' : 'false';
            if(is_null($data)) $data = 'null';
            if($data === '') $data = '""';
            
            //thinphp 支持
            if(is_array($data) && current($data) instanceof \think\Model){ $data = collection($data)->toArray();}
    
            if(is_string($data)) $data = self::unicode($data);
    
            $output = array();
            if(is_string($data) && function_exists($data)){
                $object = new \ReflectionFunction($data);
                $output['========== FUNC =========='] = array('Function' => $data, 'Namespace' => $object->getNamespaceName(),  'File' => $object->getFilename());
            }
    
            if(is_object($data) || (is_string($data) && class_exists($data, false))){
    
                $message = '';
                if(is_object($data)){
    
                    if($data instanceof \Exception){
    
                        $file = $data->getFile() . ' (' .$data->getLine() .')';
                        $message =  $data->getMessage() . ' (' .$data->getCode() .')';
                    }
    
                    $name = get_class($data);
                    $fields = get_object_vars($data);
    
                }else{
                    $name = $data;
                    $fields = get_class_vars($data);
                }
    
                $methods = get_class_methods($data);
    
                $object = new \ReflectionClass($data);
                if(!isset($file)) $file = $object->getFilename();
    
                $output['========== CLASS =========='] = array('Class' => $name, 'Namespace' => $object->getNamespaceName(), 'Message' => $message, 'File' => $file, 'Attr' => $fields, 'Method' => $methods);
                
                if(count($output) == 1)  $output = current($output);
            }
    
            return empty($output)? $data : $output;
    
        }
    

    
        /**
         * unicode 解码
         * @param $string
         * @return string|string[]|null
         */
        public static function unicode($string) {
            
            return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', function($match){
                return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
            }, $string);
            
        }


        /**
         * 打印数据
         * $args 参数列表
         */
        public static function p($args = ''){
    
            $args = func_get_args();
            $count = func_num_args();
            if($count == 0) $args = array();
       
            $cli = isset($argc);
            $output = $cli? '' : '<pre style="background:#f3f3f4;padding:5px;border:1px solid #aaa;">' ;
            foreach($args as $key => $data){
                $data = self::format($data);
                $output .= print_r($data, true);
    
                if($key < $count - 1) $output .= $cli? "\r\n--------------------------------------------------------\r\n" : '<hr/>';
            }
    
            $output .= $cli? "\r\n" : '</pre>';
            echo $output;
            
        }
    

    
    }


class Tool{
    
    public $command = array();
    public $data = '';
    
    public $output =  null;

    public function __construct($data){
        $this->data = trim($data);

    }

    private function  tagToArray($html,  $tag = 'td', $strip = false, $keys = array(), $append = array()){

        if($strip) $html = strip_tags($html, "<$tag>");
        $pattern = "/<{$tag}\b.*?>(.*?)<\/{$tag}>/s";
        preg_match_all($pattern , $html, $matches);

        $matches = $matches[1];
        foreach($matches as &$value) $value = trim($value);

        if($append) $matches = $matches + $append;
        if(!empty($keys)) $matches = array_combine($keys, $matches);

        return $matches;

    }

    /**
     * 时间戳转换
     */
    public function datetime(){

        if($this->data){
            if(is_numeric($this->data))
                $this->output = date('Y-m-d H:i:s', trim($this->data));
            else if(is_string($this->data))
                $this->output = strtotime($this->data);
        }else{
            $time = time();
            $this->output = $time .'<br/>' . date('Y-m-d H:i:s', $time);
        }

    }

    public function deUnicode(){

        $this->output = Helper::unicode($this->data);
    }


    public function select2Array(){

        $this->data = trim($this->data);
        $this->data = preg_replace(array('/^\$\w+\s+=\s+\'*/', '/\';$/'), array('', ''), $this->data);

        if(substr($this->data, 0, 5) === 'array'){

            eval('$data='.$this->data.';');
            $html = '';
            foreach($data as $key => $value){
                $html .= '<option value="'.$key.'">'.$value.'</option>' . "\n";
            }

            $this->output = '<select class="form-control" name="">' ."\n" . $html .'</select>';
            $this->output = htmlentities($this->output, ENT_QUOTES, "UTF-8");
        }else{
            $regex = '/<option\s+value="(.*)?".*?>(.*)?<\/option>/';
            preg_match_all($regex, $this->data, $matches);
            $this->output = array_combine($matches[1], $matches[2]);

        }

    }


 


    
    public function json2Array(){

        $this->data = trim($this->data);

        $this->data = preg_replace(array('/^\$\w+\s+=\s+\'*/', '/\';$/'), array('', ''), $this->data);

        if(substr($this->data, 0, 5) === 'Array'){
            $pattern = array('/Array\s+\(/', '/\[(\w+)\]\s=>\s/', '/=>#~#(.*)/', '/[\r\n]\',/', '/\'NULL\'/i', "/'array\(',/", '/([\r\n]+\s+)\)/');
            $replacement = array('array(', "'$1'=>#~#", " => '$1',", "',", 'null', 'array(', "$1),");
            $this->data =  preg_replace($pattern, $replacement, $this->data);
            $this->output = $this->data . ';';
            return;
        }


        if(substr($this->data, 0, 5) === 'array'){

            eval('$data='.$this->data.';');
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        }else if(substr($this->data, 0, 1) === '{' || substr($this->data, 0, 1) === '['){

            $data = json_decode($this->data, true);
            for($i = 1; $i < 3; $i++){
                if(!empty($data)) break;

                $this->data = stripslashes($this->data);
                $data = json_decode($this->data, true);
            }

        }else{
            parse_str($this->data, $this->output);
            return;
        }

        $this->output = var_export($data, true) . ';';

    }

    /**
     * 从建表语句中分离出字段
     *
     */
    public function sqlField2Array(){

        $matchs = explode(',',  trim($this->data, ','));

        $data = '';
        foreach($matchs as $line){
            preg_match('/`(\w+)`(.*?COMMENT\s+[\'"](.*)[\'"]|.)/',  $line, $line);

            if(!isset($line[3])) $line[3] = '';
            $data .= "  '$line[1]' => null, //" . $line[3] . '<br/>';
        }

        $this->output = '$data = array(<br/>' . $data .');';

    }

    /**
     * 清徐空白
     */
    public function filter(){

        $this->output = preg_replace('/(\s|&nbsp;|　|\xc2\xa0)/', '',  $this->data);

    }


    public function download(){

        $this->data = explode("\n", str_replace("\r", '', $this->data));
        if(empty($this->date)) return $this->output = '没有下载地址';
        foreach($this->data as $row){
            $name = basename($row);
            file_put_contents('/home/zbseoag/Downloads/'.$name, file_get_contents($row));
            $this->output[] =  $name;
        }

        $this->output[] = ' 下载完成';

    }

    
    public  function table2Array(){
        $this->title =  '';
        if($this->title !== ''){
            $this->title = explode(',', trim($this->title));
        }

        $html = $this->tagToArray($this->data, 'tr');

        $result['th'] = $this->tagToArray($html[0], 'th', true);
        unset($html[0]);

        foreach($html as $row){
            $result['td'][] = $this->tagToArray($row, 'td', true);
        }


        $html = '| ' .implode(' | ', $result['th']) . ' |<br/>|:---|<br/>';
        foreach($result['td'] as $line => &$row){

            if(!empty($this->title)){

                $newrow = array();
                foreach($this->title as $allow_key){
                    $newrow[] = isset($row[$allow_key])? $row[$allow_key] : '';
                }
                $row = $newrow;

            }

            $html .= '| ' . implode(' | ', $row) . ' |<br/>';
            if(!empty($this->title) && is_array($this->title) && count($this->title) == 1) $result['td'][$line] = array_pop($row);
        }


        Helper::p(var_export($result, true));
        $this->output = $html;


    }



    public function __destruct(){
      
        if(is_array($this->output)) $this->output = var_export($this->output, true) . ';';
        if($this->output !== null) Helper::p($this->output);
    }



}


?>







