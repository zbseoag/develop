<?php
declare(strict_types=1);

class Arrays {

    public static $value = [];
    public static $alias = '$0';

    public function __construct($value, bool $filter=false){

        if($filter) $value = array_filter($value);
        self::$value = $value;

    }

    public static function set(...$argument){

        return new static(...$argument);

    }

    public function get(){

        return self::$value;
    }


    /**
     * 魔术方法调用任何函数
     * @param $func
     * @param $argument
     * @return false|mixed
     */
    public function __call($func, $argument){

        //如果参数为空,则把当前 self::$alias 值作为唯一参数
        if(empty($argument)){
            $argument = [self::$alias];
        }

        //如果没有 self::$alias 参数,则表示占第一位
        if(!in_array(self::$alias, $argument, true)){
            array_unshift($argument, self::$alias);
        }

        //查找 self::$alias 替换成实际的 self::$text 值
        $argument = array_map(function($item) {
            return $item === self::$alias? self::$value : $item;
        }, $argument);

        if(function_exists('array_' . $func)) $func = 'array_' . $func;

        return call_user_func_array($func, $argument);

    }


    /**
     * 插入元素
     * @param $index 索引,-1表示末尾
     * @param mixed ...$items 元素,可以是数组或元素,个数任意
     * @return array|mixed
     */
    public function insert(int $index, ...$items){

        $array = $index == -1? self::$value : array_merge(array_slice(self::$value, 0, $index));

        foreach($items as $item){
            if(is_array($item)){
                $array = array_merge($array, $item);
            }else{
                array_push($array, $item);
            }
        }

        return $index == -1? $array : array_merge($array, array_slice(self::$value, $index));

    }


    public static function toTree($list, $pk = 'id', $pid = 'pid', $child = '_child_', $root = 0) {

        //创建基于主键的数组
        foreach($list as $key => $row){
            $refer[$row[$pk]] = &$list[$key];
        }
        foreach($refer as $row){
            $refer[$row[$pid]][$child][$row[$pk]] = &$refer[$row[$pk]];
        }
        return $refer[$root][$child];

    }


    /**
     * 二维数组保存成 csv 文件
     * @param string $file 文件路径
     * @param array $data 二维数组
     * @param array $header 表头配置
     * @param bool $download 是否下载
     *
     * $header = ['name'=>'名字', 'age'=>'年龄'];
     * $data = array(
     * ['age'=>34, 'name'=>'张三'],
     * ['age'=>15, 'name'=>'李四'],
     * );
     */
    public static function csv($file, $data, $header = array(), $download = true, $format = null) {

        $file = iconv('UTF-8', 'GBK', $file);

        $thead = ($download || !file_exists($file)) ? true : false;
        if($download == true){
            header('Content-Type:application/application/octet-stream');
            header("Content-Disposition:attachment;filename=$file");
            $fp = tmpfile();

        }else{
            $fp = fopen($file, 'a');
        }

        if(mb_detect_encoding(current(current($data)), array('UTF-8')) == 'UTF-8'){
            fwrite($fp, pack('H*', 'EFBBBF')); //写入 BOM 头
        }

        //写入表头
        if($thead) fputcsv($fp, array_values($header));

        foreach($data as $row){

            if(is_callable($format)) $row = $format($row);

            $line = array();
            if(!empty($header)){

                foreach($header as $field => $name){
                    if(!isset($row[$field])){
                        $line[] = '';
                        continue;
                    }
                    $line[] = trim($row[$field]);
                }
            }else{
                $line = array_values($row);
            }

            fputcsv($fp, $line);
        }
        if($download){
            fseek($fp, 0);
            echo stream_get_contents($fp);
        }
        fclose($fp);
        if(!$download) return $file;

    }


}
