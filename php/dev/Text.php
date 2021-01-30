<?php
declare(strict_types=1);

class Text {
  
    public static $value = '';
    public static $alias = '$0';

    public function __construct($text, bool $trim=true){

        if(!is_string($text)) $text = (string) $text;

        if($trim) $text = trim($text);

        self::$value = $text;

    }


    /**
     * 魔术方法调用任何 string 函数
     * 如 $object->explode("\n", '$0'); 其中 '$0' 表示 self::$text 的值
     *
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

        return call_user_func_array($func, $argument);

    }

    public static function __callStatic($method, $argument){

       // p($method, $argument);
    }


    public static function set(...$argument){

        return new static(...$argument);

    }

    public function get(){

        return self::$value;
    }



    /**
     * 字符串分割成单词
     * @param string $delimiter
     * @return string
     */
    public static function toWord(string $string=null, string $delimiter=' ') :string {

        $string = $string ?? self::$value;
        $string = str_replace(['_', '-','/', '\\', '*', '"', '.', ":"], ' ', $string);
        return trim(preg_replace(['/([A-Z][a-z])/', '/\s+/'], [' ${1}', $delimiter], $string));

    }


    /**
     * 清徐空白
     */
    public static function filter(string $string=null){

        $string = $string ?? self::$value;
        return preg_replace('/(\s|&nbsp;|　|\xc2\xa0)/', '',  $string);
    }


    /**
     * 查找字符串出现在位置
     * @param string $string
     * @param null $find
     * @param int $offset
     * @param bool $case
     * @return false|int
     */
    public static function find(string $string, $find=null, $offset=0, bool $case=true){

        if(func_num_args() == 1) list($string, $find) = [ self::$value, $string ];

        if($case){
            return strpos($string, $find, $offset);
        }else{
            return stripos($string, $find, $offset);
        }

    }


    public static function beginWith(string $string, $find=null, bool $case=true){

        if(func_num_args() == 1) list($string, $find) = [ self::$value, $string ];
        return self::find($string, $find, 0, $case) === 0;

    }

    public static function endWith(string $string, $find=null, bool $case=true){

        if(func_num_args() == 1) list($string, $find) = [ self::$value, $string ];

        $offset = strlen($string) - strlen($find);
        return self::find($string, $find, $offset, $case) === $offset;

    }


    /**
     * 解析 pathinfo 参数
     * @param null $path
     * @return array
     */
    public function pathToArray($path = null){

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
    public function httpFormat($data, $header = false) {

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

        return empty($return) ? $data : $return;

    }


    /**
     * 取得值
     * @param fixed $variable
     * @param string $default
     * @return fixed
     */
    public static function value(&$var, $default='') {

        return (isset($var) && !empty($var)) ? $var : $default;
    }


  
  public function toArray($pattern = ''){
    
      if(substr($this->text, 0, 5) === 'Array'){
        
          //打印数组转数组代码
          $pattern = array('/Array\s+\(/', '/\[(\w+)\]\s=>\s/', '/=>#~#(.*)/', '/[\r\n]\',/', '/\'NULL\'/i', "/'array\(',/", '/([\r\n]+\s+)\)/');
          $replacement = array('array(', "'$1'=>#~#", " => '$1',", "',", 'null', 'array(', "$1),");
          $output = preg_replace($pattern, $replacement, $this->text);
          eval('$output='.$output.';');

      }else if(substr($this->text, 0, 1) === '[' || substr($this->text, 0, 1) === '{'){
          //json 转数组
          $data = json_decode($this->text, true);
          for($i = 1; $i < 3; $i++){
              if(!empty($data)) break;
              $output = json_decode(stripslashes($this->text), true);
          }
        
      }else if(preg_match('/(.*?=.*?&?)+/', $this->text)){
          //query 参数参数转数组
          parse_str($this->text, $output);
      }else{
    
          //按正则表达式转转数组,默认:换行符
          if($pattern == '') $pattern = '/(.*?)\n/';
          preg_match_all($pattern, $this->text, $output);
    
          if(count($output) == 2){
              return $output[1];
          }else if(count($output) == 3){
              return array_combine($output[1], $output[2]);
          }
    
      }
      
      return $output;
      
  }
  
  public function toJson(){
    
    $output = '';
    if(substr($this->text, 0, 5) === 'array'){
        //数组代码转json
        eval('$output='.$this->text.';');
        $output = json_encode($output, JSON_UNESCAPED_UNICODE);
      
    }
    
    return $output;
    
  }



    /**
     * 文本文件转数组:
     * 文件内容:
    北京   111
    天津   222
     * @param unknown $content 文件路径或文本内容
     * @return multitype:
     */
    public function text2array($content){

        if(is_file($content)) $content = file_get_contents($content);
        $content = array_filter(explode("\r\n", trim($content)));
        foreach($content as $value){
            $data[] = array_values(array_filter(explode(' ', $value)));
        }
        return $data;
    }



    public function toUnique($str){

        return implode('', array_unique(str_split($str)));
    }


    public static function getExt($string){
        return strtolower(pathinfo($string, PATHINFO_EXTENSION));
    }


    public static function tfstyle($name){

        return str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
    }

    /**
     * 数组保存到文件
     * @param unknown $file
     * @param string $content
     * @return number
     */
    public function arr2file($file, $content=''){
        if(is_array($content)) $content = var_export($content, true);
        $content = "<?php\nreturn $content; \n"; //生成配置文件内容
        return file_put_contents($file, $content);
    }




    /**
     * 删除清除bom头
     * $content 文件内容
     */
    public function clearBom(&$content){

        if(substr($content, 0, 3) == chr(239).chr(187).chr(191)){
            $content = substr($content, 3);
        }
    }



    /**
     * 随机字符
     * @param number $length 长度
     * @param string $type 类型
     * @param number $convert 转换大小写
     * @return string
     */

    public function random($length = 6, $string = 'string'){

//        $default = array(
//            'number'=>'1234567890',
//            'letter'=>'aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ',
//            'string'=>'1aA2bBc3CdDe4EfFgG5hHiIjJ6kKlLmMn7NoOpPqQr8RsStTuUvV9wWxXyYzZ0',
//        );
//
//        if(isset($default[$string])) $string = $default[$string];
//        $code = '';
//        $strlen = strlen($string) -1;
//        for($i = 0; $i < $length; $i++){
//            $code .= $string{mt_rand(0, $strlen)};
//        }
//        return $code;

    }

    /**
     * 表字段转文字说明
     * @param unknown $field 字段名
     * @param unknown $data 字据值
     * @param unknown $default 为空时,返回值
     * @return string 文本值
    //'exptime'=>function($row){ return dateformat($row['exptime']); },
    //'exptime'=>array('callback', 'dateformat', array('Y-m-d')),
    //'exptime'=>array('callback', array($this, 'actName'), array('Y-m-d')),
     */
    function field_text($field, $data, $config=array(), $default=false){
        $value = is_array($data)? $data[$field] : $data;
        if(empty($config)) return $value;

        if($config[$field][0] == 'callback'){
            $args = isset($config[$field][2])? array($row[$field], $config[$field][2], $data) : array($row[$field], $data);
            $return = call_user_func_array($config[$field][1], $args);
        }else{
            $return = isset($config[$field][$value])? $config[$field][$value] : (is_string($default)? $default : ($default? $value : ''));
        }
        return $return;

    }



    /**
     * 中文字姓氏换成*号
     * @param unknown $string 名字
     * @param string $encode 编码
     */
    function shortName($string, $encode='UTF-8'){
        $surnames = array(
            '欧阳','太史','端木','上官','司马','东方','独孤','南宫','万俟','闻人','夏侯','诸葛','尉迟','公羊','赫连','澹台','皇甫','宗政','濮阳','公冶',
            '太叔','申屠','公孙','慕容','仲孙','钟离','长孙','宇文','司徒','鲜于','司空','闾丘','子车','亓官','司寇','巫马','公西','颛孙','壤驷','公良',
            '漆雕','乐正','宰父','谷梁','拓跋','夹谷','轩辕','令狐','段干','百里','呼延','东郭','南门','羊舌','微生','公户','公玉','公仪','梁丘','公仲',
            '公上','公门','公山','公坚','左丘','公伯','西门','公祖','第五','公乘','贯丘','公皙','南荣','东里','东宫','仲长','子书','子桑','即墨','达奚','褚师','吴铭'
        );
        $length = mb_strlen($string, $encode);
        $count = 1;
        if($length > 2){
            $surname = mb_substr($string, 0, 2, $encode);
            if(in_array($surname, $surnames)){
                $count = 2;
            }elseif($length > 4){
                $count = -2;
            }
        }
        return '*'.mb_substr($string, $count, 30, $encode);
    }

    /**
     * 字符截取按个数
     * @return string
     */
    function strcut($string, $length, $encode='UTF-8'){
        $len = mb_strlen($string, $encode);
        $return = mb_substr($string, 0, $length, $encode);
        return ($len > $length)? $return .'...' : $return;

    }


    /**
     * 判断字符是否为符号
     * @param $char
     * @return bool
     */
    function isSymbol($char){

        $asc = ord($char);
        return !((48 <= $asc && $asc <= 57) || (65 <= $asc && $asc <= 90) || (97 <= $asc && $asc <= 122));
    }



}







