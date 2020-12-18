<?php
//Swoole\Coroutine::create 等价于go函数
//Coroutine\Channel 可以简写为chan
//Swoole\Coroutine::defer可以直接用defer


$config = [

     ['启动 PHP 服务器', 'php', [ '-S'=>'地址', '-t'=>'根目录' ] ]

];


if(!empty($_POST)){

    $cmd = $_POST['cmd'];
    unset($_POST['cmd']);
    foreach($_POST as $key => $value){
        $cmd .= " $key $value";
    }

    echo exec($cmd . " &" );
}

?>
<form action="/" method="post">

    PHP 服务器 <input placeholder="域名:" name="-S" /> <input placeholder="根目录:" name="-t" /> <button type="submit" name="cmd" value="php">启动</button>

</form>

