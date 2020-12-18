<?php



function parse_header($header){

    $head = array();
    foreach($header as $key => $value){

        $t = explode(':', $value, 2);
        if(isset( $t[1] )) $head[trim($t[0])] = trim($t[1]);
        else $head[$key] = $value;
    }
    return $head;
}


/**
 * 数组保存到文件
 * @param unknown $file
 * @param string $content
 * @return number
 */
function array2file($file, $content=''){
    if(is_array($content)) $content = var_export($content, true);
    $content = "<?php\r\nreturn $content; \r\n"; //生成配置文件内容
    return file_put_contents($file, $content);
}



