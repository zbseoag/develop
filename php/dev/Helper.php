<?php

class Helper {



    /**
     * 分页方法
     * @param unknown $page
     * @param unknown $count
     * @param number $size
     * @return boolean|number[]
     */
    public static function page($page, $count, $size = 20){
        $total = ceil($count / $size);
        return ($page > $total)? false : array(($page - 1) * $size, $size, $total);

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
    public static function timeWindows($minute, $count, &$times) {

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
     * 判断变量是否为空白字符
     * @param fixed $data
     * @param array $options
     * @return boolean
     * @throws Exception
     */
    public static function space(&$data, $options = []) {

        if(!is_array($data)){
            return (!isset($data) || trim($data) === '') ? true : false;
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



    public static function combine($keys, $values = null) {

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
    public static function replace($content, $pattern = '', $replace = '') {

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
    public static function trim($content, $charlist = " \t\n\r\0\x0B") {

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
    public function convert($param, $convert = array()) {

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
    function getExcelRow($i) {

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
