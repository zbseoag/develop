<?php
/**
 * Created by IntelliJ IDEA.
 * User: zbseoag
 * Date: 2019/8/25
 * Time: 18:43

$html = "<b>example: </b><div align=left>this is a test</div>";

$pattern = Pattern::init()->append('<(\w+?)', '标签名')
->append('\s*?', '空格')
->append('.*?>','属性')
->append('.*?', '内容')
->append('<\/\1>', '回溯引用标签名')
->exec();

echo $pattern . "\n";
preg_match_all($pattern, $html, $out);
print_r($out);

 *
 */




class Pattern {

    public $pattern = '';

    public static function init(){

        return new static();
    }

    public function start($char){

        $this->pattern .= '^' . $char;
        return $this;
    }

    public function end($char){

        $this->pattern .=  $char . '$';
        return $this;
    }

    //匹配次数
    public function count($range = null){

        if(is_array($range)) $range = implode(',', $range);

        if(is_numeric($range) || strpos($range, ',') ) $range =  '{' . $range . '}';
        $this->pattern .=  $range;

        return $this;
    }



    public function match($pattern, $get = true){

        $this->pattern .= '(' . ($get ? '' : '?:'). $pattern . ')';
        return $this;
    }

    //转义
    public function escape($char){

        $this->pattern .=  '\\' . $char;

        return $this;
    }

    public function append($string, $comment = null){


        $this->pattern .= $string;
        //$this->pattern .= $string . ($comment ? '(?#'. $comment .')' : '');
        return $this;
    }


    public function flush(){

        $pattern = $this->pattern;
        $this->pattern = '';

        return $pattern;
    }

    public static function help(){

        $help = <<<'help'
        [\b] => 单词边界
        [\B] => 非单词边界
        [\cx] => x 指明的控制字符，值必须为 A-Z 或 a-z 之一
        [\d] => 数字，[0-9]
        [\D] => 非数字，[^0-9]。
        [\f] => 换页符，等价于 \x0c 和 \cL
        [\n] => 标识一个八进制转义值或一个向后引用。如果 \n 之前至少 n 个获取的子表达式，则 n 为向后引用。否则，如果 n 为八进制数字 (0-7)，则 n 为一个八进制转义值。
        [\r] => 回车符，等价于 \x0d 和 \cM
        [\s] => 空白字符，等价于 [\f\n\r\t\v]
        [\S] => 非空白字符，等价于 [^\f\n\r\t\v]
        [\t] => 水平制表符，等价于 \x09 和 \cI
        [\v] => 垂直制表符。等价于 \x0b 和 \cK
        [\w] => 字母、数字、下划线，等价于 [A-Za-z0-9_]
        [\W] => 非字母、非数字、非下划线。等价于 [^A-Za-z0-9_]
        [\xn] => 匹配某个十六进制表示的字符，值必须为确定的两个数字长。\x41B 表示 AB
        [\num] => 对所获取的匹配的引用，"(.)\1" 匹配两个连续的相同字符
        [\nm] => 标识一个八进制转义值或一个向后引用。如果 \nm 之前至少有 nm 个获得子表达式，则 nm 为向后引用。如果 \nm 之前至少有 n 个获取，则 n 为一个后跟文字 m 的向后引用。如果前面的条件都不满足，若 n 和 m 均为八进制数字 (0-7)，则 \nm 将匹配八进制转义值 nm
        [\nml] => 如果 n 为八进制数字 (0-3)，且 m 和 l 均为八进制数字 (0-7)，则匹配八进制转义值 nml。
        [\un] => n 是四个十六进制数字表示的 Unicode 字符
help;
        echo $help;

    }


    //正向查找
    public function forward($string, $positive = true){

        $this->pattern .=  '(?' . ($positive ? '=' : '!') . $string . ')';
        return $this;

    }


    //反向查找
    public function backward($string, $positive = true){

        $this->pattern .=  '(?<' . ($positive ? '=' : '!') . $string . ')';
        return $this;

    }


    public function exec($mode = null){

        $mode = $mode ? $mode : '';
        $pattern = '/' . $this->pattern . '/' . $mode;

        return $pattern;
    }


    //条件查找
    public function condition($condition, $string){

        $this->pattern .= '(?(' . $condition. ')'. $string .')';
        return $this;
    }





}

$html = "<b>example: </b><div align=left>this is a test</div>";

$pattern = Pattern::init()->append('<(\w+?)', '标签名')
    ->append('\s*?', '空格')
    ->append('.*?>','属性')
    ->append('.*?', '内容')
    ->append('<\/\1>', '回溯引用标签名')
    ->exec();

echo $pattern . "\n";
preg_match_all($pattern, $html, $out);
print_r($out);