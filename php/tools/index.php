<?php
error_reporting(E_ALL);
ini_set('display_errors', true);
date_default_timezone_set('Asia/Shanghai');
session_start();
if (!empty($_REQUEST)){

    $action = $_REQUEST['action'];
    $tool = new Tool($_REQUEST['data']);

    if(preg_match('/\(.*\)/', $action)){
        $tool->output = eval('return ' . str_replace('$0', $tool->data ?? '', $action) . ';');

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
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>我的工具包</title>
    <script src="form.js"></script>
    <link rel="stylesheet" href="lib/codemirror.css">
    <script src="lib/codemirror.js"></script>

    <script src="addon/selection/active-line.js"></script>
    <script src="addon/edit/matchbrackets.js"></script>
    <script src="mode/htmlmixed/htmlmixed.js"></script>
    <script src="mode/xml/xml.js"></script>
    <script src="mode/javascript/javascript.js"></script>
    <script src="mode/css/css.js"></script>
    <script src="mode/clike/clike.js"></script>
    <script src="mode/php/php.js"></script>


    <!--引入css文件，用以支持主题-->
    <link rel="stylesheet" href="theme/eclipse.css">
    <link rel="stylesheet" href="theme/seti.css">
    <link rel="stylesheet" href="theme/dracula.css">

    <!--支持代码折叠-->
    <link rel="stylesheet" href="addon/fold/foldgutter.css"/>
    <script src="addon/fold/foldcode.js"></script>
    <script src="addon/fold/foldgutter.js"></script>
    <script src="addon/fold/brace-fold.js"></script>
    <script src="addon/fold/comment-fold.js"></script>


    <!--全屏模式-->
    <link rel="stylesheet" href="addon/display/fullscreen.css">
    <script src="addon/display/fullscreen.js"></script>

    <!--括号匹配-->
    <script src="addon/edit/matchbrackets.js"></script>

    <!--自动补全-->
    <link rel="stylesheet" href="addon/hint/show-hint.css">
    <script src="addon/hint/show-hint.js"></script>
    <script src="addon/hint/anyword-hint.js"></script>
</head>
<style>
    * {padding: 0; margin: 0; font: 14px "微软雅黑"; box-sizing:border-box; }
    html,body { width: 100%; height: 100%;}
    #form button {  padding: 4px 10px; margin:0px -4px; min-width: 80px; }
    .button { min-width: 100px!important; margin:0px -4px 10px -4px!important;}
    li { margin-bottom: -2px; }
    table{ border-collapse: collapse;  }
    tr{   border: 1px solid  #CCC; }
    td, th{  border: 1px solid  #CCC; }
    #win{list-style:none;position: absolute; z-index: 10; border:1px solid red;left:40%;top:10%;visibility: hidden;padding:20px; padding-top: 0; width: 20%;}
    #win input{padding:10px;margin-bottom: 10px;width: 100%;}
    #win .close{float:right;position:relative;top:-5px; right: -10px;cursor:default;padding:6px;}
    .CodeMirror {border: 1px solid black; font-size:14px; width: 100%; height:500px; }
</style>

<ul  id="win">
    <li><h3 style="line-height: 20px;padding:10px 0;font-size: 16px;">连接服务器 <i class="close" onclick="el('win').style.visibility='hidden'">X</i></h3></li>
    <li>
        <form action="database.php" target="_blank">
            <input name="h" placeholder="主机:"><br>
            <input name="u" placeholder="用户名:"><br/>
            <input name="p" placeholder="密码:"><br/>
            <input name="d" placeholder="数据库:"><br/>
            <button style="margin-left: 0; padding:5px 20px;" type="submit" onclick="el('win').style.visibility='hidden';return true;">确定</button>
        </form>
    </li>
</ul>


<body style="padding:2px; box-sizing: border-box;">
    <form id="form" autocomplete="off">
        <ul style="list-style:none;">

            <li style="float:left; padding:10px 0 0 4px;">
                <button type="reset" onclick="editor.doc.setValue('');el('run').innerHTML='';">清空</button>
                <button type="button" onclick="editor.doc.setValue(el('run').innerText)">加载</button>
                <button type='button' class='action' value='eval("return $0;")'>计算</button>
                <button class="action" data-switch-value="strtolower|strtoupper" value="" type="button">大小写</button>
                <button type="button" class="action" value="translates">翻译</button>
                <button class="action" value="timestamp" type="button">时间戳</button>
                <button class="action" value="md5" type="button">MD5</button>
                <button class="action" data-switch-value="urlencode|urldecode" type="button">URL</button>
                <button class="action" data-switch-value="deunicode|unicode" type="button">Unicode</button>
                <button class="action" value="nameStyle" type="button">命名</button>
                <button class="action" value="pregMatch" type="button">正则</button>
                <button onclick="el('win').style.visibility='visible';" type="button">数据字典</button>
            </li>

            <li style="float:right;padding:10px 4px 0 0px;">
                <button class="action" type="button" accesskey="s" value="lang:bash_sh">Shell (S)</button>
                <button class="action" type="button" accesskey="h" value="lang:php7.2_php" >PHP (H)</button>
                <button class="action" type="button" accesskey="p" value="lang:python3.8_py" >Python (P)</button>
                <button class="action" type="button" accesskey="l" value="lang:lua" >Lua (L)</button>
                <button class="action" type="button" accesskey="c" value="lang:c" >C 语言 (C)</button>
                <button class="action" type="button" accesskey="j" value="lang:java" >Java (J)</button>
                <button class="action" type="button" accesskey="g" value="lang:go_run_go" >Go (G)</button>
                <button type="button"   onclick="el('run').innerHTML=editor.doc.getValue();">Html</button>
                <button class="action" type="button" value="getFile">查看</button>

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


    <div id="run" style="margin-top:1.6em; height: calc(100% - 596px);;overflow: scroll;"></div>
</body>

</html>
<script>
var log = console.log;
function el(id){
    return document.getElementById(id);
}

var editor = CodeMirror.fromTextArea(el('data'), {

    styleActiveLine: true,
    matchBrackets: true,
    mode:"application/x-httpd-php",

    //显示行号
    lineNumbers:true,

    //设置主题
    theme:"eclipse",

    //代码折叠
    lineWrapping:true,
    foldGutter: true,
    gutters:["CodeMirror-linenumbers", "CodeMirror-foldgutter"],

    //全屏模式
    fullScreen:false,

    //括号匹配
    matchBrackets:true,
    //智能提示
    extraKeys:{"Ctrl-Alt":"autocomplete"}
});

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
        new Form('form').post({'action': item.getAttribute('value'), 'data':editor.doc.getValue() }).then(data => {

            if(editor.doc.getValue() == '' && localStorage.getItem('action').substr(0,5) == 'lang:'){
                editor.doc.setValue(data);
            }else{
                el('run').innerHTML += data;
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
        $this->data = preg_replace(['/\r/', '/\n+/'], ['', "\n"], trim($data));
    }

    public function __call($method, $args){

        if(substr($method, 0, 5) != 'lang:') return false;
        list($cmd, $ext) = $this->resolve(substr($method, 5));

        $this->lang = true;
        if (empty($this->data)){
            if(isset($this->template[$ext])) echo trim($this->template[$ext][0]);

        }else{

            switch($ext){

                case 'php':

                    if(!preg_match('/\n+/', $this->data)){

                        $this->data = sprintf("
                            \$output = eval(\"return %s;\");
                            if(is_scalar(\$output)) echo \$output;
                            else if(is_array(\$output)) print_r(\$output);
                            else var_dump(\$output);",
                        $this->data);

                    }
                    if(substr($this->data, 0, 5) != '<?php') $this->data = "<?php $this->data";

                    break;

                case 'java':
                    preg_match('/public\s+class\s+(\w+)/', $this->data, $subject);

                    if(!empty($subject)) $this->file = dirname($this->file) . DIRECTORY_SEPARATOR .$subject[1];
                    break;
            }

            $this->file .= ".$ext";
            $this->save();
            $this->lang = empty($this->template[$ext][1]) ? implode(' ', $cmd) . " $this->file" : str_replace('{file}', $this->file, $this->template[$ext][1]);
            $this->output = shell_exec($this->lang . ' 2>&1');
            $this->output = preg_replace('/\[sudo\].*:\s+/', '', $this->output);
            $this->output = htmlspecialchars($this->output, ENT_NOQUOTES);
        }

    }

    public function __destruct(){

        if($this->lang && empty($this->data)) return false;
        if(!is_array($this->output)) $this->output = [$this->output];
        array_map(function($item){self::p($item); }, $this->output);

    }

    public function save(){
        $_SESSION['codingfile'] = $this->file;
        file_put_contents($this->file, $this->data, LOCK_EX);
    }

    public function getFile(){
        $this->output = htmlspecialchars(shell_exec('cat ' . $_SESSION['codingfile']), ENT_NOQUOTES);
    
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
        $this->data = str_replace(['*', '$', '@', '%', '=', '/', "\n"], '', $this->data);
        $text = Text::toWord($this->data);

        if(preg_match('/\s/', $text)) $text .= "\n" . Text::toWord($this->data, "\n");

        if(preg_match('/[\x{4e00}-\x{9fa5}]+/u', $text)) {
            $this->output[] = translate($text, 'zh', 'en');
        }else{
            $this->output[] = translate($text, 'en', 'zh');
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
        if($this->data[0][0] != '/') $this->data[0] = '/'.$this->data[0].'/';
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


        if(substr($this->data, 0, 5) === 'array' || $this->data[0] == '['){

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

    /**
     *
     */
    public function file_copy(){

        $newfile = $this->data . '/' . basename($this->title);
        if(!is_dir($this->data)) mkdir($this->data, '0777', true);
        copy($this->title, $newfile);
        $this->output = realpath($newfile);

    }


}


class Tool3333 {


    /**
     * 字符行转数组
     * -d 分割符
     * -k 设置为索引的列
     * -v 设置为值的列
     * -l 分割成几个元素
     * -rm 过滤字符
     * -w 几维数组
     */
    function textToArray() {

        $this->command += array('-d' => ' ', '-k' => null, '-l' => 100, '-v' => null, '-rm' => '', '-w' => 2);

        $this->data = str_replace(str_split($this->command['-rm']), '', $this->data);//过滤一些字符
        $this->data = explode("\r\n", trim($this->data));//按换行转数组
        $this->data = array_filter($this->data);//过滤空行

        $this->output = array();
        foreach($this->data as $key => $value){

            $row = explode($this->command['-d'], preg_replace('/\s+/', ' ', trim($value)), $this->command['-l']);//行数据分割成数组

            $value = ($this->command['-v'] === null) ? $row : (isset($row[$this->command['-v']]) ? $row[$this->command['-v']] : $this->command['-v']);//如果指定了设置为值的列

            //如果设置了索引列
            if($this->command['-k'] === null){
                $this->output[] = $value;
            }else{
                $this->output[$row[$this->command['-k']]] = $value;
            }

        }

        //转一维数组
        if($this->command['-w'] == 1){

            $output = array();
            foreach($this->output as $key => $value){
                if(is_array($value)) $output = array_merge($output, $value);
                else $output[] = $value;
            }
            $this->output = $output;
        }

    }

    /**
     * 二维数组根据条件转一维
     * @param $data
     */
    public function arrayFluctuate() {

        $this->title = explode(':', str_replace(' ', '', $this->title));


        if(count($this->title) == 2){

            $_temp = explode('//', $this->title[1]);

            $this->title[1] = $_temp[0];
            if(count($_temp) == 2) $this->title[2] = $_temp[1];

        }else{

            $_temp = explode('//', $this->title[0]);
            $this->title[0] = $_temp[0];
            if(count($_temp) == 2) $this->title[2] = $_temp[1];
        }

        eval('$data=' . $this->data . ';');
        if(is_array(current($data))){

            $this->output = 'array(<br/>';
            foreach($data as $row){


                if(isset($this->title[0]) && !isset($row[$this->title[0]])) $row[$this->title[0]] = '';
                if(isset($this->title[1]) && !isset($row[$this->title[1]])) $row[$this->title[1]] = '';
                if(isset($this->title[2]) && !isset($row[$this->title[2]])) $row[$this->title[2]] = '';

                if(count($this->title) == 1){

                    $this->output .= "'{$row[$this->title[0]]}',\n";

                }else if(count($this->title) == 2){

                    if(isset($this->title[1])){
                        $this->output .= " '{$row[$this->title[0]]}' => '{$row[$this->title[1]]}',<br/>";
                    }else{
                        $this->output .= " '{$row[$this->title[0]]}',//{$row[$this->title[2]]}<br/>";
                    }

                }else{
                    $this->output .= " '{$row[$this->title[0]]}' => '{$row[$this->title[1]]}', //{$row[$this->title[2]]}<br/>";
                }

            }

            $this->output .= ');';

        }


    }


    /**
     * 转接口数据格式
     * -d 分割符
     * -k 设置为索引的列
     * -v 设置为值的列
     * -l 二维分割成几个元素
     * -rm 过滤字符
     *
     */
    public function api_array() {

        $this->command('to_array', $this->command);

        $data = '';
        foreach($this->output as $key => $row){
            $data .= "  '$key' => null, //" . implode(' ', $row) . '<br/>';
        }

        $this->output = 'array(<br/>' . $data . ');';

    }


    /**
     * where 条件
     * -d 分割符
     * -k 设置为索引的列
     * -v 设置为值的列
     * -l 二维分割成几个元素
     * -rm 过滤字符
     * -w 几维数组
     *
     * -link
     * -ad
     */
    public function sql_where() {

        $this->command += array('-link' => 'IN', '-ad' => '', '-w' => 1);
        $this->command('to_array', $this->command);

        $link = strtoupper($this->command['-link']);
        $this->output = " {$link}('" . implode("{$this->command['-ad']}','", $this->output) . "{$this->command['-ad']}');\n";

    }


    /**
     *
     * -glue
     */
    public function to_string() {

        $this->command += array('-glue' => ',', '-w' => 1);
        $this->command('to_array', $this->command);

        $this->output = implode($this->command['-glue'], $this->output);

        if($this->command['-glue'] == "','") $this->output = "'" . $this->output . "'";
        if($this->command['-glue'] == '","') $this->output = '"' . $this->output . '"';

    }


    /**
     *
     */
    public function sql_insert() {

        $this->command('to_array');

        $fields = array_slice($this->command, 2);
        foreach($fields as &$value){
            //array(0=>field, 1=>value);
            if(stripos($value, ':=')){

                $value = explode(':=', $value, 2);
                $temp = explode('|', $value[1], 2);

                //array(0=>field, 1=>value, 'value'=>'定值');
                $value = array($value[0], $temp[0], 'value' => $temp[1]);

            }else if(stripos($value, ':')){

                $value = explode(':', $value, 2);
            }else if(stripos($value, '=')){
                $value = explode('=', $value, 2);
                $value = array($value[0], 'null', 'value' => $value[1]);
            }
        }
        unset($value);

        $this->command['fields'] = '`' . implode('`,`', array_column($fields, 0)) . '`';
        $output = '';
        foreach($this->output as $key => $row){

            $values = array();
            foreach($fields as $field){

                $values[] = isset($row[$field[1]]) ? $row[$field[1]] : (isset($field['value']) ? $field['value'] : '');
            }
            $values = "'" . implode("','", $values) . "'";

            $output .= "INSERT INTO `{$this->command[0]}` ({$this->command['fields']}) VALUES ($values);<br/>";
        }

        $this->output = $output;

    }


    public function json_array() {

        $this->data = trim($this->data);

        $this->data = preg_replace(array('/^\$\w+\s+=\s+\'*/', '/\';$/'), array('', ''), $this->data);

        if(substr($this->data, 0, 5) === 'Array'){
            $pattern = array('/Array\s+\(/', '/\[(\w+)\]\s=>\s/', '/=>#~#(.*)/', '/[\r\n]\',/', '/\'NULL\'/i', "/'array\(',/", '/([\r\n]+\s+)\)/');
            $replacement = array('array(', "'$1'=>#~#", " => '$1',", "',", 'null', 'array(', "$1),");
            $this->data = preg_replace($pattern, $replacement, $this->data);
            $this->output = $this->data . ';';
            return;
        }


        if(substr($this->data, 0, 5) === 'array'){

            eval('$data=' . $this->data . ';');
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


    public function toArray() {

        $this->data = explode("\n", str_replace("\r", '', $this->data));
    }


    public function strip_tags() {

        $this->output = strip_tags($this->data, '<br><h1><h2><h3><h4><div><pre><p>');
    }


    public function htmlFormat() {

        $pattern = '/\<\/\w+?\>/';
        $replacement = '$1<br/>';
        $this->output = preg_replace($pattern, $replacement, $this->data);

    }


    public function ascii() {

        $string = $this->data;
        if(preg_match_all('/^\d+,?/', $string)){
            $return = '';
            $string = explode(',', $string);
            foreach($string as $key => $value){
                $return .= chr($value);
            }
        }else{
            $length = strlen($string);
            for($i = 0; $i < $length; $i++){
                $return[] = ord($string[$i]);
            }
            $return = implode(',', $return);
        }

        $this->output = $return;
    }


    public function notExist() {

        $this->data = preg_replace('/\s{3,}/', "#@@@#", trim($this->data));
        $this->data = explode('#@@@#', trim($this->data));

        $soruce = explode("\r\n", trim($this->data[0]));
        $this->data = explode("\r\n", trim($this->data[1]));
        foreach($soruce as $value){
            if(in_array($value, $this->data)) $this->output['in'][] = $value;
            else   $this->output['not'][] = $value;
        }

        self::write($this->output);

    }


    //rsa转64位一行
    public function rsaLength64() {

        $data = array_merge(array_filter(explode("\r\n", $this->data)));

        $this->output = '-----BEGIN RSA PRIVATE KEY-----' . "\r\n";
        $this->output .= implode("\r\n", str_split(trim($data[0]), 64)) . "\r\n";
        $this->output .= '-----END RSA PRIVATE KEY-----';
        $this->output .= '<br/><br/><br/>';
        $this->output .= '-----BEGIN PUBLIC KEY-----' . "\r\n";
        $this->output .= implode("\r\n", str_split(trim($data[1]), 64)) . "\r\n";
        $this->output .= '-----END PUBLIC KEY-----';

    }


    //下载github上的文件到桌面
    public function gitDownloadFile() {

        $url = parse_url($this->title);
        $path = explode('/', trim($url['path'], '/'));
        unset($path[2]);
        array_unshift($path, $url['scheme'] . ':', '/raw.githubusercontent.com');
        $url = implode('/', $path);
        $file = end($path);
        file_put_contents($_ENV['desktop'] . '/' . $file, file_get_contents($url));

        $this->output = $file . ' 下载完成';

    }


    public function createSetAndGetmethod() {

        if(strpos($this->data, '<table') === 0){
            $methods = $this->tableToArray(true)['td'];
        }else{
            eval('$methods=' . $this->data . ';');
        }


        $html = '';

        foreach($methods as $row){

            $html .= 'protected $' . $row[0] . '; //' . $row[1] . '<br/>';
        }
        $html .= '<br/><br/>';


        foreach($methods as $row){

            $name = str_replace(' ', '', ucwords(str_replace('_', ' ', trim($row[0]))));
            $html .= '/**<br/>*' . $row[1] . '<br/>*/<br/>public function set' . $name . '($value){</br><br/>$this->' . $row[0] . ' = $value;<br/>return $this;<br/><br/>}<br/></br>';
        }

        foreach($methods as $row){

            $name = str_replace(' ', '', ucwords(str_replace('_', ' ', trim($row[0]))));

            $html .= '/**<br/>*' . $row[1] . '<br/>*/<br/>public function get' . $name . '(){<br/><br/>return $this->' . $row[0] . ';<br/><br/>}<br/></br>';
        }

        $this->output = $html;

    }


    public function templateTable() {

        eval('$data=' . $this->data . ';');

        $th = $td = '';
        foreach($this->data as $filed => $text){

            $th .= htmlentities('<th>{$thead.' . $filed . '}</th>') . '<br/>';
            $td .= htmlentities('<td>{$row.' . $filed . '}</td>') . '<br/>';
        }

        $this->output = $th . '<br/><hr/>' . $td;

    }


    public function tabToTable() {

        $this->data = str_replace("\r", '', $this->data);
        $this->data = explode("\n", $this->data);

        $table = "<br/><table>\n";

        foreach($this->data as $key => $row){

            //表头
            $td = ($key == 0) ? 'th' : 'td';

            $table .= '<tr><' . $td . '>' . str_replace("\t", '</' . $td . '><' . $td . '>', $row) . '</' . $td . '></tr>' . "\n";

            $this->output .= '| ' . str_replace("\t", ' | ', $row) . " |\n";

            //表头
            if($key == 0) $this->output .= str_repeat('| --- ', substr_count($row, "\t") + 1) . " |\n";

        }

        $table .= "\n</table><br/>";

        echo $table;

    }


    

}


?>