<?php

/**
 * 括号是否匹配
 * @param $s
 * @return false
 */
function isPairBrackets($s){

    $len = strlen($s);
    if($len % 2 == 1) return false;

    $pairs = [
        ')' => '(',
        ']' => '[',
        '}' => '{',
        '>' => '<',
    ];
    $stack = new SplStack();

    for($i = 0; $i < $len; $i++){

        $char = $s[$i];
        if(empty($char)) continue;

        //如果遇到右括号
        if(key_exists($char, $pairs)){

            //如果当前队列是空的,以右括号开始不合法。如果栈顶的左括号与当前正规的右括号不匹配
            if($stack->isEmpty() || $stack->top() != $pairs[$char]) {
                return false;
            }
            //若能匹配，则弹出
            $stack->pop();
        }else{
            //括号入栈
            $stack->push($char);
        }
    }
    return $stack->isEmpty();

}



$word = $argv[1];
$lines = 0;
$handle = fopen("bbe.txt", "r");

while(!feof($handle)){

    //读取一行数据
    $buffer = fgets($handle, 4096);

    $position = word_pos($buffer, $word);

    $lines++;
    if(!empty($position)){
        echo "$lines \t 行 : ".implode('，',$position)." 列\n";
    }
}

fclose($handle);


function word_pos($content, $word){

    $length = strlen($word);
    $offset = 0;
    $positions = [];

    //当前内容中查找字符串
    while(($position = mb_stripos($content, $word, $offset)) !== false){

        //如果字符串前一位是空白字符，且后一位是一个标点符号，则可断定查到的字符串是一个单词。
        if(trim(mb_substr($content, $position - 1, 1)) == '' && is_symbol(mb_substr($content, $position + $length , 1))) $positions[] = $position + 1;

        $offset = $position + $length;

    }

    return $positions;
}




function cny($money){

    $capital   = array('零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖');
    $unit      = array('圆', '拾', '佰', '仟', '万', '拾', '佰', '仟', '亿', '拾', '佰', '仟', '兆', '拾', '佰', '仟', '京');
    $small     = array('角', '分');

    if($decimal = strrchr( $money, '.')) $decimal = substr($decimal, 1);

    $integer = strrev(intval($money));
    $str = '';

    for($i = 0; $i < strlen($integer); $i++){

        //如果数字大于 0 或者在分割位上
        if($integer[$i] > 0 || $i % 4 == 0){

            if($integer[$i] == 0){
                //如果是0,则只保留单位
                $str = $unit[$i] . $str;
            }else{
                //数值与单位都保留
                $str = $capital[$integer[$i]] . $unit[$i] . $str;
            }

        }else{
            //仅保留"零"值
            $str = '零' . $str;
        }

    }

    //如果有小数位
    if($decimal){
        for($i = 0; $i < strlen($decimal); $i++){

            if($decimal[$i] > 0){
                $str .= $capital[$decimal[$i]] . $small[$i];
            }else{
                $str .= $capital[$decimal[$i]];
            }
        }
    }

    $str = preg_replace('/\x{96f6}{2,}/u', '零', $str);
    return str_replace('零圆', '圆', $str);

}


/**
 * 缩略图
 * @param $image
 * @param $width
 * @param $height
 * @param $file
 * @param int $quality
 */
function thumbnail($image, $width, $height, $file, $quality = 100) {

    $image = getimagesize($image);

    switch($image[2]){
    case 1: $im = imagecreatefromgif($image);
        break;
    case 2: $im = imagecreatefromjpeg($image);
        break;
    case 3: $im = imagecreatefrompng($image);
        break;
    }

    $src_W = $image[0]; //获取大图片宽度
    $src_H = $image[1]; //获取大图片高度
    $tn = imagecreatetruecolor($width, $height); //创建缩略图
    imagecopyresampled($tn, $im, 0, 0, 0, 0, $width, $height, $src_W, $src_H); //复制图像并改变大小
    imagejpeg($tn, $file, $quality); //输出图像

}



/**
 * 排列组合算法
 * @param $array
 * @param $count 多少个元素一组
 * @return array
 *
$array = ['1', '2', '3', '4', '5'];
combination($array, 2);
 *
 */
function combination($array, int $count){

    foreach($array as $key => $value){

        if(count(array_slice($array, $key, $count)) < $count) break;

        $buffer = array_slice($array, $key, $count - 1);
        $append = array_slice($array, $key + $count - 1);

        foreach($append as $k => $v){
            $compose[] = array_merge($buffer, [$v]);
        }

    }
    return $compose;

}




/**
 * 猴王算法
 * @param $start
 * @param $end
 * @param $cycle 周期
 * @return int|null|string
 *
 * print_r(monkeyKing(0, 6, 7));
 */
function monkeyKing($start, $end, int $cycle){

    $array = range($start, $end);

    $now = 1;
    while(count($array) > 1){

        //从1开始数数，数到指定周期，就销毁元素
        if($now % $cycle == 0){

            unset($array[key($array)]);
            $now = 1;
            //当 unset 一个数组元素后，指针向后移一位，这就可能超越边界，判断下超越边界就重置指针。
            //if(!key($array)) reset($array);
        }else{
            $now++;
        }

        if(!next($array)) reset($array);
    }

    return key($array);

}


/**
 * 二维数组，根据多个键名去重
 * @param $arr
 * @param $key
 * @return mixed
 *unique_by_key([['a'=>1, 'b'=>2, 'c'=>5], ['a'=>1, 'b'=>2, 'c'=>5],['a'=>1, 'b'=>3, 'c'=>5],['a'=>1, 'b'=>4, 'c'=>5]], 'a', 'b');
 */
function unique_by_key($arr, $key){

    $keys = func_get_args();
    array_shift($keys);

    $vkeys = [];
    foreach ($arr as $key => $value){

        $vitem = array_reduce($keys, function($carry, $item)use($value){ return $carry . '-' . $value[$item];});

        if(in_array($vitem, $vkeys)){
            unset($arr[$key]);
        } else {
            $vkeys[] = $vitem;
        }
    }

    return $arr;
}


function unzip($file, $dir = ''){

    try{
        //检测压缩包是否存在
        if(!is_file($file)) throw new Exception('文件 '.$file.' 不存在！');

        if($dir === '') $dir = basename($file, '.zip');

        if(!is_dir($dir)){
            if(!mkdir($dir,0777,true)) throw new Exception('创建文件夹 ' . $dir . '）失败！');
        }

        $zip = new \ZipArchive();

        if($zip->open($file) !== true) throw new Exception('打开文件 '.$file.' 失败！');
        if(!$zip->extractTo($dir)) throw new Exception('文件 '.$file.' 解压失败！');
        if(!$zip->close()) throw new Exception('关闭文件 '.$file.' 失败！');

    }catch(Exception $e){
        throw $e;
    }

    return true;

}





/**
 * 遍历目录
 * @param $dir
 * @return Generator
 *
$generator = loopdir('/home');
foreach ($generator as $value){
print_r($value);
}
 */
function loopdir($dir){

    $list = scandir($dir);
    if($list === false) $list = array();

    unset($list[0], $list[1]);
    foreach($list as $file){

        $file = $dir . '/' . $file;
        if(is_dir($file)){

            foreach(loopdir($file) as $value){
                yield $value;
            }
        }else{
            yield $file;
        }
    }
}


/**
 * 单词在当前字符串中的位置
 * @param $content
 * @param $word
 * @return array
 */
function word_pos2($content, $word){

    $length = strlen($word);
    $offset = 0;
    $positions = [];

    //当前内容中查找字符串
    while(($position = mb_stripos($content, $word, $offset)) !== false){

        //如果字符串前一位是空白字符，且后一位是一个标点符号，则可断定查到的字符串是一个单词。
        if(trim(mb_substr($content, $position - 1, 1)) == '' && is_symbol(mb_substr($content, $position + $length , 1))) $positions[] = $position + 1;

        $offset = $position + $length;

    }

    return $positions;
}


/**
 * 重复数组元素
 * @param $arr
 * @param $num
 * @return mixed
 */
function array_repeat($arr, $num){

    $arr = array_fill(0, $num, $arr);
    return call_user_func_array('array_merge', $arr);

}


