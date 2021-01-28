<?php
error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');

if (!empty($_REQUEST)){

    $action = $_REQUEST['action'];
    $tool = new Tool($_REQUEST['data']);
    if(method_exists($tool, $action)) $method = new \ReflectionMethod($tool, $action);

    if(function_exists($action)){
        $tool->output = $action($tool->data);
    }else if(isset($method) && $method->isStatic()){
        $tool->output = Tool::$action($tool->data);
    }else{
        $tool->$action();
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
    * {padding: 0; margin: 0;font: 14px "微软雅黑"; }
    html,body { width: 100%; }
    button {  padding: 4px 10px; }
    .action {}
    .button { min-width: 110px; margin: 4px 0; }
    li {margin-bottom: 10px; }
    table{ border-collapse: collapse;  }
    tr{   border: 1px solid  #CCC; }
    td, th{  border: 1px solid  #CCC; }
</style>

<body style="padding:10px; box-sizing: border-box;">
    <form id="form" autocomplete="off">
        <ul style="list-style:none;">
            <li><textarea name="data" id="data" style="width: 100%;padding: 4px;font:16px 'Courier New'; box-sizing: border-box;" rows="20"></textarea></li>
            <li style="position: absolute; top: 10px; right: 10px;">
                <button type="button" class="action" value="translates">翻译</button>
                <button type="button" id="seecode">源码</button>
                <a target="_blank" href="http://tmp.com"><button type="button">浏览</button></a>
                <button type="reset">清空</button>
            </li>
        </ul>
    </form>

    <button class="button" type="button" accesskey="s" value="bash_sh">Shell (S)</button>
    <button class="button" type="button" accesskey="p" value="python3.8_py" >Python (P)</button>
    <button class="button" type="button" accesskey="l" value="lua" >Lua (L)</button>
    <button class="button" type="button" accesskey="h" value="php" >PHP (H)</button>
    <button class="button" type="button" accesskey="c" value="c" >C 语言 (C)</button>
    <button class="button" type="button" accesskey="j" value="java" >Java (J)</button>
    <button class="button" type="button" accesskey="g" value="go_run_go" >Go (G)</button>
    <button class="button" value="datetime" type="button">时间戳</button>
    <button class="button" value="md5" type="button">md5</button>
    <button class="button" data-switch-value="strtolower|strtoupper" value="" type="button">大小写</button>
    <button class="button" data-switch-value="urlencode|urldecode" value="" type="button">url 解码</button>
    <button class="button" data-switch-value="parse_url|http_build_query" value="" type="button">url 解析</button>
    <button class="button" data-switch-value="deunicode|unicode" type="button">unicode 解码</button>
    <button class="button" value="nameStyle" type="button">命令风格</button>


    <button class="button" value="strip_tags" type="button">标签过滤</button>
    <button class="button" value="htmlFormat" type="button">html 格式化</button>

    <button class="button" value="templateTable" type="button">模板表格</button>
    <button class="button" value="select_array" type="button">&lt;select&gt;</button>
    <button class="button" value="table_array" type="button">&lt;table&gt;</button>
    <button class="button" value="myTagToArray" type="button">&lt;tag&gt;</button>

    <button class="button" value="arrayFluctuate" type="button">数组变换</button>
    <button class="button" value="json_array" type="button">JSON 转数组</button>

    <button class="button" value="createSetAndGetmethod" type="button">Seter 方法</button>
    <button class="button" value="notExist" type="button">对比存在</button>
    <button class="button" value="sql_field" type="button">SQL数组</button>
    <button class="button" value="filter" type="button">清除空白</button>
    <button class="button" value="maketplfile" type="button">创建文件</button>


    <div id="run" style="margin-top:1.6em;"></div>
    <div id="code"></div>
</body>

</html>
<script>
var log = console.log;

function el(id){
    return document.getElementById(id);
}

async function get(url){
    if(type == 'json'){
        return await fetch(url).then(response => response.json());
    }else{
        return await fetch(url).then(response => response.text());
    }
}

async function post(url, data, type='text'){

    let form = new FormData();
    for(let key in data) {
        form.append(key, data[key]);
    }

    let option = {method:'POST', body:form};
    if(type == 'json'){
        return await fetch(url, option).then(response => response.json());
    }else{
        return await fetch(url, option).then(response => response.text());
    }

}

class Form {

    constructor(id, url=''){

        this.option = {
            'header':new Headers(), 
            'method':'POST', 
            body: new FormData(document.getElementById(id))
        };
        this.url = url;
        this.type = 'text';
    }

    text(value='text'){
        this.type = value;
        return this;
    }

    append(key, value){
        this.option.body.append(key, value);
        return this;
    }

    header(value = null){
        if(value) this.option.header = new Headers(value);
        return this;
    }

    method(value = 'POST'){
        if(value) this.option.method = value;
        return this;
    }
  
    data(value = null){
        if(value){
            for(let key in value) {
                this.append(key, value[key]);
            }
        }
        return this;
    }

    async send(method, data){
        this.data(data);
        this.method(method);
        if(this.type == 'json'){
            return await fetch(this.url, this.option).then(response => response.json());
        }else{
            return await fetch(this.url, this.option).then(response => response.text());
        }
    }

    post(data = null){
        return this.send('POST', data);
    }
    
}

var buttons = document.querySelectorAll(".button, .action");
buttons.forEach(function(item){


    item.onclick = function(){

        if(item.hasAttribute('data-switch-value')){

            let switch_value = item.getAttribute('data-switch-value').split('|');
            let index = 0;
            for(var i in switch_value){
                if(item.getAttribute('value') == switch_value[i]){
                    if(i == switch_value.length - 1) break;
                    index = ++i; break;
                }
            }
            item.setAttribute('value', switch_value[index]);
        }

        localStorage.setItem('action', item.getAttribute('value'));
        el('run').innerHTML = el('code').innerHTML = '';
        new Form('form').post({'action': item.getAttribute('value')}).then(data => {

            if(el('data').value == ''){
                el('data').value = data; 
            }else{
                el('run').innerHTML = data; 
            }
            
        }).catch(e => console.error( e.message));

    }
});


el('seecode').onclick = function(){
    post('', {'action': 'look', 'data': localStorage.getItem('action') }).then(data => { el('code').innerHTML = data; }).catch(e => console.error( e.message));
}

</script>

<?php

class Tool{

    public $file = '';
    public $output =  null;
    public $data =  '';
    public $command = '';
    public $isHtml = false;

    public $template = array(

    'go' => ['
package main
import "fmt"
func main(){
    fmt.Println("Hello, World!")
}'],

    'java' => ['
import static java.lang.System.out;
import java.util.*;
public class Main {
    public static void main(String[] args) throws Exception{

    }
}', 'javac -encoding UTF8 {file} 2>&1 && java -Dfile.encoding=UTF8 {file}'],

    'c' => ['
#include <stdio.h>
#include <math.h>
int main(){
    printf("Hello, World \n");
    
}', 'echo "root" | sudo -S gcc -o {file}.out {file} 2>&1 && {file}.out'],
);

    public function __construct($data){

        $this->file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test' . DIRECTORY_SEPARATOR . 'Main';
        $this->data = preg_replace('/(\r\n|\r|\n)+/', PHP_EOL, trim($data));
    }

    public function __call($method, $args){

        list($cmd, $ext) = $this->resolve($method);
        if (empty($this->data)){
            if(isset($this->template[$ext])){
                $this->output = trim($this->template[$ext][0]);
            }
        } else {
            if ($ext == 'php')  $this->data = "<?php\n\n" . $this->data;
            if ($ext == 'java'){
                preg_match('/public\s+class\s+(\w+)/', $this->data, $subject);
                if(!empty($subject)) $this->file = dirname($this->file) . DIRECTORY_SEPARATOR .$subject[1];
            } 
            
            $this->file .= ".$ext";
            $this->save();
            $this->command = empty($this->template[$ext][1]) ? implode(' ', $cmd) . " $this->file" : str_replace('{file}', $this->file, $this->template[$ext][1]);
       
        }
    }

    public function __destruct(){

        if ($this->command) {
            $this->output = shell_exec($this->command . ' 2>&1');
            $this->output = preg_replace('/\[sudo\].*:\s+/', '', $this->output);
            self::p($this->command, htmlspecialchars($this->output, ENT_NOQUOTES));
       
        } else if(empty($this->data)) {
            echo self::format(htmlspecialchars($this->output, ENT_NOQUOTES));
        } else {

            //if(!$this->isHtml && is_string($this->output)) $this->output = htmlspecialchars($this->output, ENT_NOQUOTES);
            self::p($this->output);
            
        }
       
    }

    public function look(){
        $this->output = file_get_contents($this->file . '.' . $this->resolve($this->data)[1]);
    }

    public function save(){
        file_put_contents($this->file, $this->data, LOCK_EX);
    }

    public function resolve($value){

        $cmd = explode('_', $value);
        count($cmd) == 1 && array_push($cmd, $value);
        $ext = array_pop($cmd);
        return [$cmd, $ext];

    }

    /**
     * 格式化数据
     * @param $data
     * @return string
     */
    public static function format($data){

        if (in_array($data, array('TRUE', 'true', 'false', 'FALSE', 'null', 'NULL'), true)) $data = "\"$data\"";
        if (is_bool($data)) $data = $data ? 'true' : 'false';
        if (is_null($data)) $data = 'null';
        if ($data === '') $data = '""';

        $output = array();
        if (is_string($data) && function_exists($data)) {
            $object = new \ReflectionFunction($data);
            $output = array('Function' => ['Name' => $data, 'Namespace' => $object->getNamespaceName(),  'File' => $object->getFilename()]);
        }

        if (is_object($data) || (is_string($data) && class_exists($data, false))) {

            $message = '';
            if (is_object($data)) {

                if ($data instanceof \Exception) {

                    $file = $data->getFile() . ' (' . $data->getLine() . ')';
                    $message =  $data->getMessage() . ' (' . $data->getCode() . ')';
                }

                $name = get_class($data);
                $fields = get_object_vars($data);
            } else {
                $name = $data;
                $fields = get_class_vars($data);
            }

            $methods = get_class_methods($data);

            $object = new \ReflectionClass($data);
            if (!isset($file)) $file = $object->getFilename();

            $output += array('Class' => ['Name' => $name, 'Namespace' => $object->getNamespaceName(), 'Exception' => $message, 'File' => $file, 'Attribute ' => $fields, 'Method' => $methods]);
        }

        return empty($output)? $data : $output;
    }

    public static function p(){

        $args = func_get_args();
        $count = func_num_args();
        if ($count == 0) $args = array();

        $cli = PHP_SAPI == 'cli' ? true : false;
        $output = $cli ? '' : '<pre style="background:#f3f3f4;padding:5px;border:1px solid #aaa;">';
        foreach ($args as $key => $data) {
            $data = self::format($data);
            $output .= print_r($data, true);

            if ($key < $count - 1) $output .= $cli ? PHP_EOL . "--------------------------------------------------------" . PHP_EOL : '<hr/>';
        }

        $output .= $cli ? PHP_EOL : '</pre>';
        echo $output;
    }


    public static function unicode($str){
        preg_match_all('/./u',$str,$matches);
        $unicodeStr = "";
        foreach($matches[0] as $m){
            $unicodeStr .= "&#".base_convert(bin2hex(iconv('UTF-8',"UCS-4",$m)),16,10);
        }
        return $unicodeStr;
    }

    /**
     * unicode 解码
     * @param $string
     * @return string|string[]|null
     */
    public static function deunicode($string) {

        return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', function($match){
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        }, $string);

    }

    //命名风格
    public function nameStyle(){

        $this->data = explode("\n", trim($this->data));
        foreach($this->data as $line){
            if(strpos($line, '_')  === false ){
                $this->output .= "\n" .strtolower(ltrim(preg_replace('/([A-Z])/', '_${1}', $line), '_')) . "\n";
            }else{
                $this->output .= "\n" . str_replace(' ', '', ucwords(str_replace('_', ' ', trim($line)))). "\n";
            }
        }

    }

    /**
     * 翻译
     */
    public function translates(){

        include_once '../api/BaiduTrans.php';
        $this->data = explode("\n", str_replace(['*', '$', '@', '%', '=', '/'], '', $this->data));

        foreach($this->data as $key => $item){

            $item = Data::string($item)->toWord("\n");
            if(preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $item) > 0) {
                $this->output[] = translate($item, 'zh', 'en');
            }else{
                $this->output[] = translate($item, 'en', 'zh');
            }

            if($key < count($this->data) - 1){
                usleep(600000);
            }
           
        }

         $table = '';
        foreach($this->output as $item){

            if(isset($item['trans_result'])){
                
                $item = array_reduce($item['trans_result'], function($carry, $item){

                    $carry['src'] .= '<td style="padding:4px 10px;">' . $item['src'] . '</td>';
                    $carry['dst'] .= '<td style="padding:4px 10px;">' . $item['dst'] . '</td>';
                    return $carry;

                }, ['src'=>'', 'dst'=>'']);

                $table .= '<table><tr>'.$item['src'].'</tr><tr>'.$item['dst'].'</tr></table><br/>';

            }else{
                $table .=  '<table><tr><td>'.print_r($item, true).'</td></tr></table><br/>';
            }

        }

        $this->output = rtrim($table, '<br/>');
 
    }

}





?>