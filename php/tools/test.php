<?php

/**
author：zbseoag
仅限于本地使用,不要放在服务器上
 **/
error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');

if (!empty($_REQUEST)){
    (new Tool($_REQUEST['data']))->{$_REQUEST['action']}();
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
    .button {padding: 4px 10px; min-width: 110px; margin: 4px 0; }
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
                <button type="button" id="seecode">源码</button>
                <a target="_blank" href="http://tmp.com"><button type="button">浏览</button></a>
                <button type="reset">清空</button>
            </li>
        </ul>
    </form>

    <button class="button" type="button" accesskey="a" value="toArray">转数组</button>

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

var buttons = document.querySelectorAll(".button");
buttons.forEach(function(item){
    item.onclick = function(){

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

        array_walk($this->output, function($item){self::p($item);});
    }

    public function toArray(){

        $this->data = explode("\n", $this->data);

        $array = array_map(function($item){
            return preg_replace('/(\w+)\s.*/', '$1:', $item);
        }, $this->data);

        $this->output[] = '<p contenteditable="true" style="width:100%;box-sizing:border-box;padding:5px 0 20px 5px;">' . implode("\n", $array) . '</p>';

        $array = array_map(function($item){
            return preg_replace('/(\w+)\s(.*)/', "\t'$1' => 'null', //$2", $item);
        }, $this->data);

        $this->output[] = '[<br>' . implode('<br/>', $array) . '<br>]';
  

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