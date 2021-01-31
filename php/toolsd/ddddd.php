


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


    <style>
        * {padding: 0; margin: 0;font: 14px "微软雅黑"; }
        html,body { width: 100%; }
        button {  padding: 4px 10px; }
        .button {padding: 4px 10px; min-width: 110px; margin: 4px 0; }
        li {margin-bottom: 10px; }
        table{ border-collapse: collapse;  }
        tr{   border: 1px solid  #CCC; }
        td, th{  border: 1px solid  #CCC; }
        .CodeMirror {border: 1px solid black; font-size:14px; height:800px;}
    </style>

</head>
<body style="padding:10px; box-sizing: border-box;">
    <form id="form" autocomplete="off">
        <ul style="list-style:none;">
            <li><textarea name="data" id="data" style="width: 100%;padding: 4px;font:16px 'Courier New'; box-sizing: border-box;" rows="20"></textarea></li>
            <li style="position: absolute; top: 10px; right: 10px;">
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


var buttons = document.querySelectorAll(".button");
buttons.forEach(function(item){
    item.onclick = function(){

        localStorage.setItem('action', item.getAttribute('value'));
        el('run').innerHTML = el('code').innerHTML = '';

        new Form('form').post({'action': item.getAttribute('value'), 'data':editor.doc.getValue() }).then(data => {

            if(editor.doc.getValue() == ''){
                editor.doc.setValue(data) ; 
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
        } else{
            self::p(htmlspecialchars($this->output, ENT_NOQUOTES));
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



}

?>