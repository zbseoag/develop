<?php
/**
+------------------------------------------------------------+
代码调试快捷函数
@author: 相望成伤(zbseoag)

Debug::p(1,2,3);
Debug::log(1, 2, 3);
+------------------------------------------------------------+
*/

class Debug {

    //日志文件
    public static $file = '';

    /**
     * 设置日志文件路径
     * @param string $file
     * @return mixed|string
     */
    public static function file($file = ''){

        self::$file = empty($file)? sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'debug.txt' : str_replace('\\', DIRECTORY_SEPARATOR, $file);
        return self::$file;
    }

    /**
     * 格式化数据
     * @param $data
     * @return string
     */
    public static function format($data){
        
        if(in_array($data, array('TRUE','true', 'false', 'FALSE', 'null', 'NULL'), true )) $data = "\"$data\"";
        if(is_bool($data)) $data = $data? 'true' : 'false';
        if(is_null($data)) $data = 'null';
        if($data === '') $data = '""';
        
        //thinphp 支持
        if(is_array($data) && current($data) instanceof \think\Model){ $data = collection($data)->toArray();}

        if(is_string($data)) $data = self::unicode($data);

        $output = array();
        if(is_string($data) && function_exists($data)){
            $object = new \ReflectionFunction($data);
            $output = array('Function-Name' => $data, 'Function-Namespace' => $object->getNamespaceName(),  'Function-File' => $object->getFilename());
        }

        if(is_object($data) || (is_string($data) && class_exists($data, false))){

            $message = '';
            if(is_object($data)){

                if($data instanceof \Exception){

                    $file = $data->getFile() . ' (' .$data->getLine() .')';
                    $message =  $data->getMessage() . ' (' .$data->getCode() .')';
                }

                $name = get_class($data);
                $fields = get_object_vars($data);

            }else{
                $name = $data;
                $fields = get_class_vars($data);
            }

            $methods = get_class_methods($data);

            $object = new \ReflectionClass($data);
            if(!isset($file)) $file = $object->getFilename();

            $output += array('Class-Name' => $name, 'Class-Namespace' => $object->getNamespaceName(), 'Class-Exception' => $message, 'Class-File' => $file, 'Class-Attr' => $fields, 'Class-Method' => $methods);
            
        }
        
       

        return empty($output)? $data : $output;

    }

    /**
     * 打印当前输入数据
     *
     */
	public static function input(){

        self::p('$GLOBALS:', $GLOBALS);
        self::p('php://input:', file_get_contents('php://input'));

	}

    /**
     * unicode 解码
     * @param $string
     * @return string|string[]|null
     */
    public static function unicode($string) {
        
        return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', function($match){
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        }, $string);
        
    }
    
    /**
     * 打印数据
     * $args 参数列表
     */
    public static function v(){

        $args = func_get_args();
        $count = func_num_args();
        if($count == 0) $args = array();
   
        $cli = PHP_SAPI == 'cli'? true : false;
        $output = $cli? '' : '<pre style="background:#f3f3f4;padding:5px;border:1px solid #aaa;">' ;
        foreach($args as $key => $data){
            $data = self::format($data);
            $output .= print_r($data, true);

            if($key < $count - 1) $output .= $cli? PHP_EOL."--------------------------------------------------------".PHP_EOL : '<hr/>';
        }

        $output .= $cli? PHP_EOL : '</pre>';
        return $output;
        
    }
    
    
    public static function p(){
        
        echo call_user_func_array(array('Debug', 'v'), func_get_args());
    }

    
    public static function stop(){

        call_user_func_array(array('Debug', 'p'), func_get_args());
        exit;
    }


    /**
     * 浏览器控制台打印数据
     */
    public static function console(){
    
        $output = '';
        $args = func_get_args();
        foreach($args as $key => $data) $output .= self::format($data);
        
        echo '<script>console.log("';
        echo preg_replace('/\r|\n/', '', $output);
        echo '")</script>';
        
    }
    

    /**
     * 写入格式化的日志内容
     */
    public static function log($args = ''){

        $args = func_get_args();
        $count = func_num_args();

        self::file();
        foreach($args as $key => $data){

            $data = self::format($data);
            if(!is_string($data)){
                $data = var_export($data, true);
                $data = preg_replace(array('/(=>)(\s+)\n\s+(array)/'), array('\1\2\3'), $data);
            }

            file_put_contents(self::$file, $data, FILE_APPEND | LOCK_EX);
            if($key < $count - 1) file_put_contents(self::$file, "\n----------------------------------------------------------------------------\n", FILE_APPEND | LOCK_EX);
    
        }
    
        file_put_contents(self::$file, "\n==================================================[time ".date('Y-m-d H:i:s')."]==================================================\n", FILE_APPEND | LOCK_EX);
    }


    /**
     * 写文件
     */
    public static function write($args = ''){

        $args = func_get_args();
        if(empty(self::$file)) self::file();

        foreach($args as $key => $data){
            file_put_contents(self::$file, (is_string($data)? $data : var_export($data, true)), FILE_APPEND | LOCK_EX);
        }

    }

    /**
     * 读取文件内容
     */
    public static function read($file = ''){

        if(empty($file)) $file = self::$file;
        self::p('调试文件：', file_get_contents($file));
    }

    /**
     * 清空日志文件
     */
    public static function clear(){

        self::file();
        file_put_contents(self::$file, '',LOCK_EX);
    }


}


function p(){
    call_user_func_array(array('Debug', 'p'), func_get_args());
}

function v(){
    return call_user_func_array(array('Debug', 'v'), func_get_args());
}





