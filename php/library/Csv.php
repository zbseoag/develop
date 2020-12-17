<?php

/**
 * CSV 文件读写类
 * QQ:617937424
 * 
 * 示例:
$header = array('name' => '名字', 'age' =>'年龄', 'idcard' => '身份证号');

$data = array(
    array('age' => 12, 'name' => '李四', 'idcard' => '42088751564616131312'),
    (object) array('idcard' => '72888751564616131312', 'age' => 17, 'name' => '张三'),
);

Csv::open('e:/abc1.csv')->header($header)->data($data)->close();
Csv::open()->header($header)->data($data)->download('用户数据.csv');

 */


class Csv {

    public $header = array();
    public $file = null;
    public $fp = null;
    public static $init = null;

    /**
     * 打开/创建文件
     * @param string $file
     * @param string $mode
     * @return $this
     */
    public static function open($file='', $mode='w+'){

        if(!self::$init) self::$init = new static();

        if(empty($file)){
            self::$init->fp = tmpfile();
        }else{
            self::$init->file = iconv('UTF-8', 'GBK', $file);
            self::$init->fp = fopen(self::$init->file, $mode);
        }

        return self::$init;

    }


    /**
     * 写入表头
     * @param $value
     * @return $this
     */
    public function header($value){

        $this->header = is_array($value)? $value : (array) $value;
        fputcsv($this->fp, array_values($this->header));
		
        return $this;
    }


    /**
     * 写入一行
     * @param $data
     * @return $this
     */
    public function write($data){

        if(is_string($data)){
            $data = json_decode($data, true);
        }else if(!is_array($data)){
            $data = json_decode(json_encode($data), true);
        }

		if(empty($data)) return $this;

        if($this->header){
            $column = array();
            foreach($this->header as $key => $value){
                $column[$key] = isset($data[$key])? $data[$key] : 'NULL';
            }
            $data = &$column;
        }

        foreach($data as $key => &$value){
            if(is_numeric($value) && strlen($value) > 10){
                $value .= "\t";
			}else if(!is_scalar($value)){
				$value = json_encode($value, JSON_UNESCAPED_UNICODE);
			}
			
        }

        fputcsv($this->fp, $data);

        return $this;
    }

    /**
     * 读取一行
     * @param int $length
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @return array|false|null
     */
    public function read($length=0, $delimiter=',', $enclosure='"', $escape='\\'){

        return fgetcsv($this->fp, $length, $delimiter, $enclosure, $escape);
    }


    /**
     * 获取文件内容
     * @param null $callback
     * @param int $start
     * @param int $length
     * @return array
     */
    public function content($callback=null, $start=1, $length=0){

        if($start > 1){
            for($start; $start > 1; $start--){
                $this->read(1);
            }
        }

        $data = array();
        $key = 0;
        while(($row = $this->read()) !== false){

            if($length < 0) break;
            if($length > 0) $length--;

            if(is_callable($callback)){
                $row = $callback($row);
                if($row === null) continue;
            }
            $data[$key] = $row;
            $key++;

        }

        return $data;
    }

    /**
     * 快速写入文件
     * @param $data
     * @return $this
     */
    public function data($data){

        if(!empty($data)){

            if(is_string($data)) $data = json_decode($data, true);
            foreach($data as $row){
                $this->write($row);
            }
        }

        return $this;
    }


    /**
     * 下载
     * @param $file
     */
    public function download($file=''){

        $file = empty($file)? $this->file : iconv('UTF-8', 'GBK', $file);
        header('Content-Type:application/application/octet-stream');
        header("Content-Disposition:attachment;filename=$file");

        fseek($this->fp, 0);
        echo stream_get_contents($this->fp);
        $this->close();

    }

    public function create($data=''){

        $this->data($data)->close();
    }

    public function close(){
        fclose($this->fp);
    }


}
