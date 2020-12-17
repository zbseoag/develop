<?php

/**
 * 文件读写类
 * 支持每次读取时使用回调函数
 * QQ:617937424
 * 
 * 示例:
$file = new File('bbb.csv', 'a+');
$file->write(array(1,2,3,4));

$file->rewind();
$data = $file->limit(function($line){
    return  $line;
});

print_r($data);

 * 
 */

class File {
    
    public $file = '';
    public $fp = null;
    public $ext = '';
    
    public function __construct($file, $mode = 'r'){
        
        $this->file = $file;
        $this->ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $this->fp = fopen($file, $mode);
    }
    
    /**
     * 增加对文件函数的直接调用
     * @param string $method
     * @param mixed $args
     * @return mixed
     */
    public function __call($method, $args){
        
        array_unshift($args, $this->fp);
        return call_user_func_array($method, $args);
    }
    
    public function ext($ext = null){
    
        if(!is_null($ext)) return $this->ext;
        $this->ext = $ext;
        return $this;
        
    }
    
    
    /**
     * 文件写入
     * @param $content
     * @return mixed
     */
    public function write($content){
        
        $args = func_get_args();
        array_unshift($args, $this->fp);
        $content = &$args[1];
        
        if(is_object($content)) $content = (array) $content;
        
        $func = 'fwrite';
        if($this->ext == 'csv'){
            $func = 'fputcsv';
        }else if(!is_string($content)){
            $content = json_encode($content);
        }

        return call_user_func_array($func, $args);
    }
    
    
    /**
     * 文件读取指定行数
     * @param int $start
     * @param null $line
     * @param null $callback
     * @return array
     */
    public function limit( $start = 0, $line = null, $callback = null){
        
        if(is_callable($start)){
            $callback = $start; $start = 0;
        }
        
        //跳过行数
        for($i = 0; $i < $start; $i++){
            call_user_func_array('fgets', array());
        }
    
        $data = array();
        while(($buffer = call_user_func_array(array($this, 'read'), array())) !== false){
            
            if(is_callable($callback)) $buffer = $callback($buffer);
            //组装返回数据数组 或 字符串
            if($buffer !== null) $data[] = $buffer;
            //中止返回
            if($buffer === false) break;
            //读取限定行数
            if(!is_null($line)){ $line--; if($line < 1) break; }
        }
        
        return $data;
        
        
    }
    
    /**
     * 跳过行数
     * @param $line
     * @return $this
     */
    public function line($line){
        
        for($i = 0; $i < $line; $i++){
            if(call_user_func_array(array($this, 'read'), array()) === false) break;
        }
        
        return $this;
    }
    
    
    /**
     * 读取整个文件
     * @param int $length
     * @return bool|string
     */
    public function byte($length = 0){
        
        if(empty($length)) $length = filesize($this->file);
        return fread($this->fp, $length);
        
    }
    
    /**
     * 文件读取
     * @param string $method
     * @return mixed
     */
    public function read($method = ''){

        $method = empty($method)? 'fgets' : $method;
        
        if($this->ext == 'csv') $method = 'fgetcsv';

        $args = array_slice(func_get_args(), 1);
        array_unshift($args, $this->fp);
        $content = call_user_func_array($method, $args);
        
        return $content;
        
    }
    
    
    /**
     * 读取文件字符
     * 返回带换行的字符串
     */
    public function char($callback = null){
        
        $char = fgetc($this->fp);
        return is_callable($callback)? $callback($char) : $char;
        
    }
    
    /**
     * 文件指针定位到开头
     * @return boolean
     */
    public function rewind(){
        
        if(!rewind($this->fp)) throw new \Exception('rewind fail');
        return $this;
    }
    
    
    /**
     * 获取当前头文件指针位置
     * @return number
     */
    public function ftell(){
        
        $tell = ftell($this->fp);
        if($tell === false) throw new \Exception('get ftell fail');
        return $tell;
    }
    
    
    /**
     * 设置文件指针位置
     * @param unknown $offset
     * @param string $whence
     * @return number
     */
    public function fseek($offset, $whence = SEEK_SET){
        
        if(fseek($this->fp, $offset, $whence)== -1) throw new \Exception('fseek fail');
        return $this;
    }
    
    /**
     * 文件指针是否到结尾
     * @return number
     */
    public function feof(){
        return feof($this->fp);
    }
    
    
    public function size($file = null){
        
        if($file == null) $file = $this->file;
        return filesize($file);
    }
    
    
    /**
     * 内容格式化
     * @param $file
     * @return array|null|string|string[]
     */

    public static function format($file){
    
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        $content = file_get_contents($file);
        if($ext == 'sql'){
            $content = preg_replace(['/[\r|\n]+\s?(((--\s+|#).*)|\/\*(.|\n)*\*\/)/', '/[\r|\n]+/'], '', $content);
            $content = array_filter(explode(';', $content));
        }

        return $content;
        
    }
    
    
    public static function upload($file, $path = '', $accept = 'img'){
    
        $type = array(
            'img' => array(
                'image/pjpeg',
                'image/gif',
                'image/jpeg',
            ),
            'excel' => array(
                'text/csv',
                'application/vnd.ms-excel'
            ),
        );
        
        if(is_string($accept)) $accept = isset($type[$accept])? $type[$accept] : $type['img'];
        
        $file = isset($_FILES[$file])? $_FILES[$file] : '';
        
        if(empty($file) ) return (object) array('info'=>'File is empty');
        if(!in_array($file['type'], $accept))  return (object) array('info'=>'Invalid file');
        
        $path .= $file['name'];
        if(!move_uploaded_file($file['tmp_name'], $path)) return (object) array('info'=>'Upload fail');
        
        $file = (object) array('name'=>$file['name'], 'size'=>$file['size'], 'type'=>$file['type'], 'path'=>$path);
        
        return $file;
        
    }
    
    public static function dirlist($dir, $func = null){
        
        if(!is_dir($dir)) return false;
        if(!($handle = opendir($dir))) return false;
        
        while(($file = readdir($handle)) !== false){
            
            if($file == '.' ||  $file == '..') continue;
            $child = $dir . '/' . $file;
            if(!is_callable($func)){
                $func = function($file){ echo  $file . '<br/>'; };
            }
            if(is_dir($child)){
                self::dirlist($child, $func);
            }else{
                $func($child);
                
            }
        }
        closedir($handle);
        
    }
    
    
    /**
     * 自动加载
     * @param $prefix 类名前缀
     * @param string $dir 基点目录
     * @param string $ext 扩展名
     */
    public function autoload($prefix, $dir = __DIR__, $ext = '.php'){
        
        spl_autoload_register(function($class) use($prefix, $dir, $ext){
            
            $find = (DIRECTORY_SEPARATOR == '/')? '\\' : '/';
            if(strpos($class, $prefix . '\\') === 0){
                require_once strtr($dir . '/' . $class, $find, DIRECTORY_SEPARATOR) . $ext;
            }
            
        });

    }
    
    /**
     * 关闭文件指针
     * @return boolean
     */
    public function close(){
        fclose($this->fp);
        $this->fp = null;
    }
    
    
    public function __destruct(){
        if(!is_null($this->fp)) fclose($this->fp);
        
    }
    
    
    public function exists(){
        return file_exists($this->file);
    }


    /**
     * 生成下载world文档
     * @param unknown $content
     * @param string $file
     */
    function download_word($content, $file='newfile.doc'){
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=$file");
        $ext = substr(end(explode('.', $file)), 0, 3);
        switch($ext){
            case 'doc' :
                echo '<html xmlns:v="urn:schemas-microsoft-com:vml"xmlns:o="urn:schemas-microsoft-com:office:office"xmlns:w="urn:schemas-microsoft-com:office:word" xmlns:m="http://schemas.microsoft.com/office/2004/12/omml"xmlns="http://www.w3.org/TR/REC-html40">';
                break;
            case 'xls':
                echo '<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        }
        echo '<head></head><body>';
        echo $content;
        exit('</body></html>');
    }


    /**
     * 文件编码转换
     * @param unknown $files 文件名,多个文件用数组,支持通配符
     * @param string $charset 编码类型
     * @param string $path 文件夹
     * @return array $return 被转过的文件, unexist不存在的, unknown未知编码的
     *
     * $a = file_charset(array('aaa.php', 'bbb.php', 'c?.php', 'd*.php'));
     */
    function file_charset($files, $charset='UTF-8', $path=''){


        if(is_string($files))  $files = array($files);

        $append = array();
        foreach($files as $key => $filename){
            if(stripos($filename, '?') === false && stripos($filename, '*') === false) continue;
            $append +=  glob($path . $filename);
            unset($files[$key]);
        }


        if(!empty($append)) $files += $append;

        $return = array();
        foreach($files as $filename){
            $fullname = $path . $filename;
            if(!file_exists($fullname)){
                $return['unexist'][] = $fullname;
                continue;
            }


            $data = file_get_contents($fullname);
            $encode = mb_detect_encoding($data, 'GB2312, GBK, ASCII, BIG5,EUC-CN, UTF-8');

            if(!empty($encode) && $encode != $charset){
                $data = mb_convert_encoding($data, $charset, $encode);
                file_put_contents($fullname, $data);
                $return[] = $fullname;
            }else{
                $return['unknown'][] = $fullname;
            }
        }
        return $return;

    }


    /**
     * 清空目录
     * @param $dirname
     */
    public static function clear($dirname){

        $dirname .=  '/*';
        //删除目录下所有空目录
        array_map('rmdir', glob($dirname, GLOB_ONLYDIR));
        //删除目录所有文件
        array_map('unlink', array_filter(glob($dirname), 'is_file'));

    }










}

