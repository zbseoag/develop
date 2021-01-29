<?php
error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');

if (!empty($_REQUEST)){

    $action = $_REQUEST['action'];
    $tool = new Tool($_REQUEST['data']);

    if(preg_match('/\(.*\)/', $action)){
        $tool->output = eval('return ' . str_replace('$VAL', $tool->data ?? '', $action) . ';');

    }else if(function_exists($action)){
        $tool->output = $action($tool->data);

    }else{

        $method = false;
        if(method_exists($tool, $action)){
            $method = new \ReflectionMethod($tool, $action);

        }
        if($method && $method->isStatic()){
            $tool->output = Tool::$action($tool->data);
        }else{
            $tool->$action();

        }

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
    button {  padding: 4px 10px; margin:0px -4px; min-width: 80px; }
    .button { min-width: 100px; margin:10px -4px;}
    li {margin-bottom: -2px; }
    table{ border-collapse: collapse;  }
    tr{   border: 1px solid  #CCC; }
    td, th{  border: 1px solid  #CCC; }
</style>

<body style="padding:2px; box-sizing: border-box;">
    <form id="form" autocomplete="off">
        <ul style="list-style:none;">

            <li style="float:left; padding:10px 0 0 4px;">

                <button type="reset">清空</button>
                <button type="button" onclick="el('data').value = el('run').innerText">加载</button>
                <button type="button" class="action" value="translates">翻译</button>
                <button class="action" value="timestamp" type="button">时间戳</button>
                <button class="action" value="md5" type="button">MD5</button>
                <button class="action" data-switch-value="strtolower|strtoupper" value="" type="button">大小写</button>
                <button class="action" data-switch-value="urlencode|urldecode" value="" type="button">URL</button>
                <button class="action" data-switch-value="deunicode|unicode" type="button">Unicode</button>
                <button class="action" value="nameStyle" type="button">命名</button>
                <button class="action" value="pregMatch" type="button">正则</button>

            </li>

            <li style="float:right;padding:10px 4px 0 0px;">
                <button class="action" type="button" accesskey="s" value="lang:bash_sh">Shell (S)</button>
                <button class="action" type="button" accesskey="h" value="lang:php" >PHP (H)</button>
                <button class="action" type="button" accesskey="p" value="lang:python3.8_py" >Python (P)</button>
                <button class="action" type="button" accesskey="l" value="lang:lua" >Lua (L)</button>
                <button class="action" type="button" accesskey="c" value="lang:c" >C 语言 (C)</button>
                <button class="action" type="button" accesskey="j" value="lang:java" >Java (J)</button>
                <button class="action" type="button" accesskey="g" value="lang:go_run_go" >Go (G)</button>
                <a target="_blank" href="http://tmp.com"><button type="button">浏览</button></a>

            </li>

            <li><textarea name="data" id="data" style="padding:6px;width: 100%; font:16px 'Courier New'; box-sizing: border-box;" rows="20"></textarea></li>

            <li style="padding-left:4px;">
                <button class="button" data-switch-value="parse_url|http_build_query" value="" type="button">url 解析</button>
                <button class="button" value="table_array" type="button">&lt;table&gt;</button>

                <button class="button" value="strip_tags" type="button">标签过滤</button>
                <button class="button" value="htmlFormat" type="button">html 格式化</button>
                <button class="button" value="templateTable" type="button">模板表格</button>
                <button class="button" value="select2Array" type="button">&lt;select&gt;</button>

                <button class="button" value="myTagToArray" type="button">&lt;tag&gt;</button>
                <button class="button" value="arrayFluctuate" type="button">数组变换</button>
                <button class="button" value="json2Array" type="button">JSON数组</button>
                <button class="button" value="notExist" type="button">对比存在</button>
                <button class="button" value="sql_field" type="button">SQL字段</button>

            </li>


        </ul>
    </form>


    <div id="run" style="margin-top:1.6em;"></div>
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

        el('run').innerHTML = ''
        new Form('form').post({'action': item.getAttribute('value')}).then(data => {

            if(el('data').value == '' && localStorage.getItem('action').substr(0,5) == 'lang:'){
                el('data').value = data; 
            }else{
                el('run').innerHTML = data;
            }
            
        }).catch(e => console.error( e.message));

    }
});


</script>

<?php

class Tool{

    public $file = '';
    public $output =  null;
    public $data =  '';
    public $lang = '';
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

        $this->file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'Main';
        $this->data = preg_replace('/(\r\n|\r|\n)+/', PHP_EOL, trim($data));
    }

    public function __call($method, $args){

        if(substr($method, 0, 5) != 'lang:') return false;
        list($cmd, $ext) = $this->resolve(substr($method, 5));

        if (empty($this->data)){
            $this->lang = true;
            if(isset($this->template[$ext])) echo trim($this->template[$ext][0]);

        }else{

            if ($ext == 'php')  $this->data = "<?php\n\n" . $this->data;
            if ($ext == 'java'){
                preg_match('/public\s+class\s+(\w+)/', $this->data, $subject);
                if(!empty($subject)) $this->file = dirname($this->file) . DIRECTORY_SEPARATOR .$subject[1];
            }
            $this->file .= ".$ext";
            $this->save();
            $this->lang = empty($this->template[$ext][1]) ? implode(' ', $cmd) . " $this->file" : str_replace('{file}', $this->file, $this->template[$ext][1]);
            $this->output = shell_exec($this->lang . ' 2>&1');
            $this->output = preg_replace('/\[sudo\].*:\s+/', '', $this->output);
            $this->output = [$this->lang, htmlspecialchars($this->output, ENT_NOQUOTES)];
        }

    }

    public function __destruct(){

        if($this->lang && empty($this->data)){
            return false;
        }

        if(!is_array($this->output)) $this->output = [$this->output];
        array_map(function($item){self::p($item); }, $this->output);

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

            $item = Data::toWord($item, "\n");
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


    public function pregMatch(){

        $this->data = explode("\n", $this->data, 2);
        if($this->data[0]{0} != '/') $this->data[0] = '/'.$this->data[0].'/';
        preg_match_all($this->data[0], $this->data[1], $this->output, PREG_SET_ORDER); //PREG_PATTERN_ORDER

        if (preg_last_error() !== PREG_NO_ERROR) {
            $this->output = 'Error:' . preg_last_error();
        }else{

            $this->output = array_reduce($this->output, function($carry, $item){

                $carry .= '<tr>' .  array_reduce($item, function($carry, $item){
                        $carry .= '<td style="padding:4px 10px;">' . $item . '</td>';
                        return $carry;

                }, '') . '</tr>';

                return $carry;
            }, '');


            $this->output = '<table><tr><td colspan="100" style="text-align:center;padding:4px 10px;">匹配/分组</td></tr>' . $this->output . '</table>';

        }

    }

    /**
     * 时间戳转换
     */
    public function timestamp(){

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


    public  function table_array(){


        $this->title = '';
        if($this->title !== ''){
            $this->title = explode(',', trim($this->title));
        }

        $html = Html::toArray($this->data, 'tr');

        $result['th'] = Html::toArray($html[0], 'th', true);
        unset($html[0]);

        foreach($html as $row){
            $result['td'][] = Html::toArray($row, 'td', true);
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


        self::p(var_export($result, true));
        $this->output = $html;

    }

    /**
     * 打印的数组元素转成模板变量调用
     * -v 遍历时的变量名
     */
    public function template(){

        $this->output = '';
        $this->command += array('-v'=>'$row');

        $regex = "/[\['](\w*)['\]]/";
        preg_match_all($regex, $this->data, $matches);

        foreach($matches[1] as $key => $val){
            $this->output .= '&lt;th&gt;'.$val.'&lt;/th&gt;<br/>';
        }
        $this->output .= '<hr/>';
        foreach($matches[1] as $key => $val){
            $this->output .= "&lt;td&gt;{" . $this->command['-v'] . ".$val}" . "&lt;/td&gt;<br/>";
        }

    }




    /**
     * 从建表语句中分离出字段
     */
    public function sql_field(){

        preg_match('/\(.*\)/ms', $this->data, $this->data);
        $this->data = preg_split('/,\n/', current($this->data));

        foreach($this->data as $item){

            preg_match('/`(\w+)`(?:.*(?<=COMMENT)\s+\'(.+)\')?/',  $item, $item);
            if(empty($item)) continue;
            $item = array_pad($item, 3, '');
            $this->output .= "    '$item[1]' => null, //$item[2]\n";
        }

        $this->output = "\$field = [\n$this->output];";

    }


    public function select2Array(){

        if(substr($this->data, 0, 5) === 'array'){

            $data= eval("return $this->data;");
            $html = '';
            foreach($data as $key => $value){
                $html .= sprintf("\t<option value=\"%s\">%s</option>\n", $key, $value);
            }

            $this->output = sprintf("<select name=\"\" class=\"form-control\">\n%s</select>", $html);
            $this->output = htmlentities($this->output, ENT_QUOTES, "UTF-8");

        }else{

            preg_match_all('/<option\s+.*value="(.*)">(.*)<\/option>/U', $this->data, $this->data, PREG_SET_ORDER);
            $this->output = array_combine(array_column($this->data, 1), array_column($this->data, 2));
            $this->output = var_export($this->output, true) . ';';

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


        if(substr($this->data, 0, 5) === 'array' || $this->data{0} == '['){

            $data= eval("return $this->data;");
            $this->output = json_encode($data, JSON_UNESCAPED_UNICODE);

        }else if(substr($this->data, 0, 1) === '{' || substr($this->data, 0, 1) === '['){

            $data = json_decode($this->data, true);
            for($i = 1; $i < 3; $i++){
                if(!empty($data)) break;

                $this->data = stripslashes($this->data);
                $data = json_decode($this->data, true);
            }

            $this->output = var_export($data, true) . ';';


        }else{
            parse_str($this->data, $this->output);
            return;
        }



    }


}


?>