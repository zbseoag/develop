<?php

/**
 * author: zbseoag
 * QQ: 617937424

用法:
初始化或调用setting 方法可以设置 default 固定选项，option 是单次请求的选项。
$content = Curl::set()->url($url)->get($data);
//数组参数可以累加
$content = Curl::set()->data(['abc'=>123])->data(['def'=>456])->post(['gef'=>789]);
//字符串数据
$content = Curl::set()->url($url)->data('{"aaa":"111"}')->post();
//发送文件
$content = Curl::set()->url($url)->file('img.jpg')->post();
//静态方法调用
$content = Curl::post($url, $data);
//仅生成curl选项
$options = Curl::set()->url($url)->file('img.jpg')->method('post')->create();

 *
 */

class Curl{

    //curl对象
    protected $curl = null;
    //对象实例
    protected static $instance = null;

    //默认选项
    public $default = array(
        CURLOPT_HEADER => false,//是否显示头信息
        CURLOPT_RETURNTRANSFER => true,//是否返回信息
        CURLOPT_TIMEOUT => 30,//超时
        CURLOPT_ENCODING => '',//支持的压缩类型
    );

    //动态选项，只用一次
    public $options = array();

    protected $data = '';
    protected $debug = false;
    protected $sleep = 0;
    protected static $info = array();
    protected static $error = '';


    /**
     * 设置默认选项
     * @param array|string $name
     * @param string $value
     * @return $this
     */
    public static function set($name, $value=''){

        if(is_null(self::$instance)) self::$instance = new static($name, $value);
        return self::$instance;
    }

    private function __construct($name, $value=''){

        if(is_array($name)){
            $this->default = $name + $this->default;
        }else{
            $this->default[$name] = $value;
        }

        $this->curl = curl_init();

    }


    public function __call($method, $args){

        if(!empty($args)) $this->data(current($args));
        return $this->method($method)->exec();
    }


    /**
     * 快捷静态方法
     * Curl::post($url, $data);
     * @param $method
     * @param $args
     * @return mixed
     */
    public static function __callStatic($method, $args){

        if(!isset($args[1])) $args[1] = '';
        return static::set()->url($args[0])->data($args[1])->$method();
    }


    public static function error(){
        return self::$error;
    }

    public static function info(){
        return self::$info;
    }



    public function log($args=''){

        $args = func_get_args();
        $count = func_num_args();
        $file = sys_get_temp_dir() . '/debug.curl.log';
        foreach($args as $key => $data){

            file_put_contents($file, (is_string($data)? $data : var_export($data, true)), FILE_APPEND | LOCK_EX);
            if($key < $count - 1) file_put_contents($file, "\n----------------------------------------------------------------------------------------------------------------------------------------------------------------------\n", FILE_APPEND | LOCK_EX);
        }

        file_put_contents($file, "\n================================================================================================[time ".date('Y-m-d H:i:s')."]================================================================================================\n", FILE_APPEND | LOCK_EX);
    }

    public function __destruct(){
        curl_close($this->curl);
    }

    public function debug($value=true){

        $this->debug = $value;
        return $this;
    }

    public function sleep($value = 0){

        $this->sleep = $value;
        return $this;
    }

    /**
     * 设置请求方法
     * @param $value
     * @return $this
     */
    public function method($value){

        $this->options[CURLOPT_CUSTOMREQUEST] = strtoupper($value);
        return $this;
    }


    /**
     * 超时选项
     * @param int $time
     * @return $this
     */
    public function timeout($value=30){

        $this->options[CURLOPT_TIMEOUT] = $value;
        return $this;
    }


    public function url($value){

        $this->options[CURLOPT_URL] = $value;
        return $this;
    }


    /**
     * 设置header
     *
	 * 标准: ['key: value']
     * 字符串: 'key: value'
     * 键值对: ['key' => value]
     * @param string|array $header
     * @return $this
     */
    public function header($header=''){

        if(is_array($header) && key($header) == 0){

            foreach($header as $key => $value){
                $header[] = $key . ': ' . $value;
                unset($header[$key]);
            }

        }

        if(is_string($header)) $header = array($header);

        if(!isset($this->options[CURLOPT_HTTPHEADER])) $this->options[CURLOPT_HTTPHEADER] = array();
        $this->options[CURLOPT_HTTPHEADER] = array_merge($this->options[CURLOPT_HTTPHEADER], $header);

        return $this;

    }


    /**
     * 组装数据
     * @param $args
     * @return $this
     */
    public function data($value=''){

        if(is_array($value)){
            if(!is_array($this->data)) $this->data = array();
            $this->data = array_merge($this->data, $value);
        }else{
            $this->data = $value;
        }

        return $this;

    }


    /**
     * Cookie选项
     * @param $cookie
     * @param bool $refresh
     * @return $this
     */
    public function cookie($cookie, $refresh=false){

        if(strpos($cookie, '=')){
            $this->options[CURLOPT_COOKIE] = $cookie;
        }else{
            $this->options[CURLOPT_COOKIEJAR] = $cookie;
            $this->options[CURLOPT_COOKIEFILE] = $cookie;
        }

        if($refresh) $this->options[CURLOPT_COOKIESESSION] = true;

        return $this;

    }


    /**
     *
     * @param int $verify_host 主机校验
     * @param int $version 版本
     * @return $this
     */
    public function ssl($verify_host = 0, $version=CURL_SSLVERSION_DEFAULT){

        $this->options[CURLOPT_SSL_VERIFYHOST] = $verify_host; //0不检测，2检查公用名是否与提供主机名匹配
        $this->options[CURLOPT_SSLVERSION] = $version; //建议用默认值，在 SSLv2 和 SSLv3 中存在弱点

        return $this;
    }


    public function cainfo($value=''){

        $this->options[CURLOPT_CAINFO] = $value;
        return $this;
    }


    public function capath($value=''){

        $this->options[CURLOPT_CAPATH] = $value;
        return $this;
    }

    /**
     * 生成选项
     * @return array
     */
    public function create(){

        $data = is_array($this->data)? http_build_query($this->data) : $this->data;
        if($this->data != ''){

            if($this->options[CURLOPT_CUSTOMREQUEST] == 'GET'){

                $link = strpos($this->options[CURLOPT_URL], '?') === false? '?' : '&';

                if(is_array($this->data) && is_numeric(key($this->data))){
                    $link = strpos($this->options[CURLOPT_URL], '/') === false? '/' : '';
                    $data = implode('/', $this->data);
                }

                $this->options[CURLOPT_URL] .= $link . $data;
            }else{
                $this->options[CURLOPT_POSTFIELDS] = $data;
            }
        }
        $this->data = '';
        $scheme = parse_url($this->options[CURLOPT_URL]);
        $scheme = $scheme['scheme'];
        if($scheme == 'https' && !isset($this->options[CURLOPT_SSL_VERIFYHOST])) $this->ssl();

        //验证对等证书
        if((isset($this->options[CURLOPT_CAINFO]) && !empty($this->options[CURLOPT_CAINFO])) || (isset($this->options[CURLOPT_CAPATH]) && !empty($this->options[CURLOPT_CAPATH]))){
            $this->options[CURLOPT_SSL_VERIFYPEER] = true;//当设置了 CURLOPT_CAINFO 或 CURLOPT_CAPATH 则表示要验证的交换证书
        }else{
            $this->options[CURLOPT_SSL_VERIFYPEER] = false;
        }

        //带上默认选项
        $options = $this->options + $this->default;
        $this->options = array();
        ksort($options);

        return $options;

    }

    /**
     * 执行请求
     * @return bool|string
     */
    public function exec(){

        $options = $this->create();

        curl_setopt_array($this->curl, $options);
        $result = curl_exec($this->curl);

        //记录日志
        if($this->debug){

            if(self::$error = curl_errno($this->curl)) self::$error = curl_error($this->curl) . '('. self::$error .')';
            if(empty(self::$error)) self::$error = '';

            self::$info = curl_getinfo($this->curl);
            $this->log(self::$error? self::$error : '请求成功:' . $result, $options, self::$info);
        }

        curl_reset($this->curl);
        if($this->sleep) sleep($this->sleep);

        return $result;

    }


    public function host($value){

        $this->header('Host: ' . $value);
        return $this;
    }

    public function origin($value){

        $this->header('Origin: ' . $value);
        return $this;
    }

    public function agent($value=''){

        if(empty($value)) $value = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.86 Safari/537.36';
        $this->header('User-Agent: ' . $value);

        return $this;
    }

    public function accept($value=''){

        if(empty($value))  $value = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8';
        $this->header('Accept: ' . $value);

        return $this;
    }

    public function language($value=''){

        if(empty($value)) $value = 'zh,zh-CN;q=0.9,en;q=0.8,en-US;q=0.7';
        $this->header('Accept-Language', $value);
        return $this;
    }


    public function referer($value){

        $this->header('Referer: ' . $value);
        return $this;
    }


    public function location($value=3){

        $this->options[CURLOPT_FOLLOWLOCATION] = true; //根据返回的Location重定
        $this->options[CURLOPT_MAXREDIRS] = $value; //限制重定向次数

        return $this;
    }


    /**
     * 发送文件
     * @param $file
     * @param string $upname
     * @param string $mime
     * @param string $name
     * @return $this
     */
    public function file($file, $upname='file',  $mime='', $name=''){

        $data = array($upname => curl_file_create(realpath($file), $mime, $name));

        if(isset($this->options[CURLOPT_POSTFIELDS])){
            $this->options[CURLOPT_POSTFIELDS] = $data + $this->options[CURLOPT_POSTFIELDS];
        }else{
            $this->options[CURLOPT_POSTFIELDS] = $data;
        }
        return $this;

    }



}