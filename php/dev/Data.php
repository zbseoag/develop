<?php


class Data {


    /**
     * 字符串分割成单词
     * @param string $delimiter
     * @return string
     */
    public static function toWord(string $string, string $delimiter=' ') :string {

        $string = str_replace(['_', '-','/', '\\', '*', '"', '.', ":"], ' ', $string);
        return strtolower(trim(preg_replace(['/([A-Z])[a-z]/', '/\s+/'], [' ${1}', $delimiter], $string)));

    }


    /**
     * 清徐空白
     */
    public function filter(string $string){

        return preg_replace('/(\s|&nbsp;|　|\xc2\xa0)/', '',  $string);
    }



    /**
     * 滑动时间窗口
     * 每次成功访问时，记录访问时间点
     * 每次清理N分钟之前的访问时间点
     * 对访问次数进行计数，判断是否超过次数
     * @param $minute
     * @param $count
     * @param $times
     * @return bool
     */
    public static function timeWindows($minute, $count, &$times){

        $now = time();
        $point = $now - $minute * 60;//从当前时间往前推N分钟的时间点
        foreach($times as $key => $item){
            if($item < $point) unset($times[$key]); //把N分钟之前的访问清理掉
        }

        if(count($times) <= $count){
            $times[] = $now; //成功时，记录本次访问时间点
            return true;
        }
        return false;

    }
    
    /**
     * 取得值
     * @param fixed $variable
     * @param string $default
     * @return fixed
     */
    public static function get(&$var, $default = ''){
        
        return (isset($var) && !empty($var))? $var: $default;
    }




    /**
     * 判断变量是否为空白字符
     * @param fixed $data
     * @param array $options
     * @throws Exception
     * @return boolean
     */
    public static function space(&$data, $options = []){
        
        if(!is_array($data)){
            return (!isset($data) || trim($data) === '')? true : false;
        }
        
        if(empty($options)){
            foreach($data as $var){
                if(!isset($var) || trim($var) === '') return true;
            }
            return false;
        }
        
        foreach($options as $key => $message){
            
            if(!isset($data[$key]) || trim($data[$key]) === ''){
                throw new Exception($message . '不能为空');
            }
        }
        
    }
    
    
    /**
     * 二维数组保存成 csv 文件
     * @param string $file 文件路径
     * @param array $data 二维数组
     * @param array $header 表头配置
     * @param bool $download 是否下载
     *
    $header = ['name'=>'名字', 'age'=>'年龄'];
    $data = array(
    ['age'=>34, 'name'=>'张三'],
    ['age'=>15, 'name'=>'李四'],
    );
     
     */
    public static function csv($file, $data, $header = array(), $download = true, $format = null){
        
        $file = iconv('UTF-8', 'GBK', $file);
        
        $thead = ($download || !file_exists($file))? true  : false;
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
                        $line[] = ''; continue;
                    }
                    $line[] = trim($row[$field]);
                }
            }else{
                $line = array_values($row);
            }
            
            fputcsv($fp, $line);
        }
        if($download){
            fseek($fp, 0); echo stream_get_contents($fp);
        }
        fclose($fp);
        if(!$download) return $file;
        
    }
    
    
    
    public static function combine($keys, $values = null){
        
        if($values == null){
            $values = $keys;
            $keys = array_values($keys);
        }
        return array_combine($keys, $values);
        
    }
    
    
    /**
     * 替换空白字符
     * @param $string
     * @param string $replace
     * @return string
     */
    public static function replace($content, $pattern = '', $replace = ''){
        
        if(empty($pattern)) $pattern = '/(\s|&nbsp;|　|\xc2\xa0)+/';
        
        if(is_string($content)){
            $content = trim(preg_replace($pattern, $replace, $content), $replace);
        }else{
            foreach($content as $key => $string){
                $content[$key] = trim(preg_replace($pattern, $replace, $string), $replace);
            }
        }
        return $content;
    }
    
    /**
     * 替换空白字符
     * @param $string
     * @param string $replace
     * @return string
     */
    public static function trim($content, $charlist = " \t\n\r\0\x0B"){
        
        if(is_string($content)){
            $content = trim($content, $charlist);
        }else{
            foreach($content as $key => $string){
                $content[$key] = trim($string, $charlist);
            }
        }
        return $content;
        
    }
    
    /**
     * 数组键名转换
     * @param $param
     * @return mixed
     */
    public function convert($param, $convert = array()){
        
        foreach($convert as $key => $field){
            
            if(isset($param[$key])){
                if(!empty($field)) $param[$field] = $param[$key];
                unset($param[$key]);
            }
        }
        
        return $param;
        
    }
    
    
    
    /**
     * 根据当前列号,返回 excel 列名
     * @param int $i 第i列
     * @return string
     */
    function getExcelRow($i){
        
        $char1 = floor($i / 26);
        $char2 = $i % 26;
        if($i % 26 == 0) $char1--;
        if($char2 == 0) $char2 = 26;
        
        if($i <= 26){
            return chr(64 + $char2);
        }else{
            return chr(64 + $char1) . chr(64 + $char2);
        }
        
    }
    
    
    /**
     * 解析 pathinfo 参数
     * @param null $path
     * @return array
     */
    public function parsePath($path = null){
        
        if(func_num_args() == 0) $path = $_SERVER['PATH_INFO'];
    
        $array = array();
        $path = array_chunk(explode('/', trim($path, '/')), 2);
        foreach($path as $row){
            $array[$row[0]] = $row[1];
        }
        
        return $array;
        
    }
    
    
    
    /**
     * 从浏览器粘贴的 header 信息或表单数据转换成数据
     * @param $data
     * @return array
     */
    public function format($data, $header = false){
        
        //字符串转数组
        if(is_string($data)){
            $data = explode("\n", trim($data));
            foreach($data as $key => $value){
                $data[$key] = trim($value);
            }
            $format = true;
        }
    
        $return = array();
        if(($header && is_numeric(key($data))) || isset($format)){
        
            foreach($data as $value){
                $value = explode(':', trim($value), 2);
                $return[rtrim($value[0])] = ltrim($value[1]);
            }
        
        }else if($header){
            foreach($data as $key => $value){
                $return[] = "$key: $value";
            }
        }
        
        return empty($return)? $data : $return;
        
    }

    function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child_', $root = 0){
        //创建基于主键的数组
        foreach($list as $key => $row){
            $refer[$row[$pk]] = &$list[$key];
        }
        foreach ($refer as $row){
            $refer[$row[$pid]][$child][$row[$pk]] = &$refer[$row[$pk]];
        }
        return $refer[$root][$child];
    }

    /**
     * @param $bytes
     * @param int $precision
     * @return string
     *
     * echo bytes(memory_get_peak_usage());
     */
    function bytes($bytes, $precision = 2) {

        $units = array("b", "kb", "mb", "gb", "tb");

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . " " . $units[$pow];
    }






}
