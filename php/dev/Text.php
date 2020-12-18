<?php

//$data = Text::instance('a=1&b=2')->toArray();

class Text{
  
  public $text = '';
  public static $instance = null;
  
  public function __construct($text = ''){
    
    $this->setText($text);
  }
  
  public static function instance($text = ''){
    
    if(is_null(self::$instance)) self::$instance = new static($text);
    return self::$instance;
    
  }
  
  public function setText($text){
      $this->text = empty($text)? '' : preg_replace(array('/(\r\n|\n|\r){2,}/', '/\s{2,}/'), array("\n", ''), trim($text));
      return $this;
  }
  
  public function getText(){
    return $this->text;
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



    function text2array($file){

        $content = file_get_contents($file);
        $content = array_filter(explode("\r\n", trim($content)));
        foreach($content as $value){
            $data[] = array_values(array_filter(explode(' ', $value)));
        }

        return $data;
    }


    /**
     * 文本文件转数组:
     * 文件内容:
    北京   111
    天津   222
     * @param unknown $content 文件路径或文本内容
     * @return multitype:
     */
    public function text2array2222($content){

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
    function clear_bom(&$content){

        if(substr($content, 0, 3) == chr(239).chr(187).chr(191)){
            $content = substr($content, 3);
        }
    }


    function encode_json($array){

        if(version_compare(PHP_VERSION,'5.4.0','<')){
            $str = json_encode( $array);
            $str =  preg_replace_callback("#\\\u([0-9a-f]{4})#i", function($matchs){return iconv('UCS-2BE', 'UTF-8',  pack('H4',  $matchs[1])); },$str);
            return  $str;
        }else{
            return json_encode($array, JSON_UNESCAPED_UNICODE);
        }
    }




    /**
     * 随机字符
     * @param number $length 长度
     * @param string $type 类型
     * @param number $convert 转换大小写
     * @return string
     */

    function random($length = 6, $string = 'string'){

        $default = array(
            'number'=>'1234567890',
            'letter'=>'aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ',
            'string'=>'1aA2bBc3CdDe4EfFgG5hHiIjJ6kKlLmMn7NoOpPqQr8RsStTuUvV9wWxXyYzZ0',
        );

        if(isset($default[$string])) $string = $default[$string];
        $code = '';
        $strlen = strlen($string) -1;
        for($i = 0; $i < $length; $i++){
            $code .= $string{mt_rand(0, $strlen)};
        }
        return $code;

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
    function is_symbol($char){

        $asc = ord($char);
        return !((48 <= $asc && $asc <= 57) || (65 <= $asc && $asc <= 90) || (97 <= $asc && $asc <= 122));
    }



}







