<?php
/**
author：zbseoag
仅限于本地使用,不要放在服务器上
因为没有任何安全过滤.
而且还能执行 php 代码.
**/
error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');

if(!empty($_REQUEST)){

    echo '
    <style>
        *{padding:0; margin: 0; font:14px "微软雅黑";}
        table{ border-collapse: collapse;  }
        tr{   border: 1px solid  #CCC; }
        td, th{  border: 1px solid  #CCC; }
    </style>
    ';

    $action = $_REQUEST['action'];
    $data = $_REQUEST['data'];

    $tool = new Tool($data);
    if(method_exists($tool, $action)){
        $tool->$action();
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
button,input{padding:4px 10px; min-width: 110px; margin:4px 0;}
li{ margin-bottom: 10px;}

</style>
<body style="padding:10px; box-sizing: border-box; ">

<form id="form" action=""  method="post" target="iframe" autocomplete="off">
    <ul style="list-style:none;">
        <li>
            <textarea name="data"  id="data"style="width: 100%;padding: 4px;font:16px 'Courier New'; box-sizing: border-box;" rows="32"></textarea>
        </li>
    </ul>
    <input id="action" type="hidden" name="action" />
</form>

<button class="button" value="exec_c" type="button">C 语言</button>
<button class="button" value="exec_php" type="button">PHP 语言</button>
<button class="button" value="exec_python" type="button">Python 语言</button>



<iframe id="iframe" name="iframe" style="border:none; margin-top:20px; display:block; width:100%;" onload="this.height=iframe.document.body.scrollHeight;" ></iframe>
</body>
</html>

<script>

var buttons = document.getElementsByTagName('button');
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

class Tool{
    
    public $file = '';
    public $output =  null;
    public $data =  '';
    public $command = '';

    public function __construct($data){

        $this->file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'temp_run_code';
        $this->data = trim($data);

    }

    public function save(){
        file_put_contents($this->file, $this->data, LOCK_EX);
    }

    public static function p($args){
        call_user_func_array(array('Debug', 'p'), func_get_args());
    }

    public function __destruct(){

        if($this->command){
            $this->output = shell_exec($this->command . ' 2>&1');
            $this->output = preg_replace('/\[sudo\].*:\s+/', '', $this->output);
        }

        echo "<a style='line-height:2em;' target='_blank' href='?action=look_file&data=$this->file'>$this->file</a>";
        self::p(htmlspecialchars($this->output, ENT_NOQUOTES));


    }


    public function exec_c(){

        $this->file = $this->file . '.c';
        $include = "#include <stdio.h>\n#include <math.h>\n";

        $pattern = '/(\s*#\w+\s+.*\n)+/';
        if(!preg_match('/(int|void)\s+main\(.*\)\s*\{/', $this->data)){

            preg_match_all($pattern, $this->data, $out);
            if(isset($out[0][0])) $include .= $out[0][0];
            
            $this->data = $include . "\nvoid main(){\n\n\t" .preg_replace($pattern, '', $this->data) . "\n\n}\n";
        }else{

            $this->data = $include . $this->data;
        }

        $this->save();
        $this->command = "echo 'root' | sudo -S gcc -o $this->file.out $this->file 2>&1 && $this->file.out";

    }


    public function exec_php(){

        if(substr($this->data, 0, 5) != '<?php') $this->data = "<?php\n\n" . $this->data;

        $this->file = $this->file . '.php';
        $this->save();

        $this->command = "/usr/local/php7.4.11/bin/php $this->file";

    }

    
    public function exec_python(){

        $this->file = $this->file . '.py';
        $this->save();

        $this->command = "/usr/bin/python3.8 $this->file";

    }

    public function look_file(){

        $this->file = $this->data;
        $this->output = file_get_contents($this->file);
      
    }




}

?>







