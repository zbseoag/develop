<?php

/**

用法:
echo Html::script('/abc.js?v=1.2');
echo Html::script('alert(111)');
echo Html::instance()->open('form')->select('t', ['o'=>'sss'])->close()->html();
*/

class Html{
    
    
    protected static $instance = null;
    protected static $html = '';
    protected static $open = '';
    
    
    /**
     * 返回类的实例
     * @return string|Html
     */
    protected static function instance(){
        
        return is_null(self::$instance)? self::$html : self::$instance;
    }
    
    
    /**
     * 构造函数
     * @access protected
     * @param array $options 参数
     */
    protected function __construct($options){
        
        $options += array();
        $this->options = $options;
        
    }
    
    
    /**
     * 魔术方法,生成各种标签
     */
    public function __call($name, $arguments){
        
        array_unshift($arguments, $name);
        return call_user_func_array(__NAMESPACE__ .'\Html::tag', $arguments);
    }
    
    public static function __callStatic($name, $arguments){
        
        array_unshift($arguments, $name);
        return call_user_func_array(__NAMESPACE__ .'\Html::tag', $arguments);
    }
    

    /**
     * 生成标签
     */
    public static function tag($tag, $attrs = '', $inner = null){
        
        if(is_string($attrs)){ $inner = $attrs; $attrs = []; }
        if(is_null($attrs)) $attrs = [];
        
        self::$html .= "<{$tag}";
        foreach($attrs as $attr => $value){
            if(is_null($value)) continue;
            self::$html .= is_numeric($attr)? " $value" : " {$attr}=\"{$value}\"";
        }
        
        if($inner == false){
            self::$html .= '>'.PHP_EOL;
        }else{
            self::$html .= is_null($inner)? '/>'.PHP_EOL : ">{$inner}</{$tag}>".PHP_EOL;
        }
        return self::instance();
    }
    
    
    public static function trim($content, $tag){
        
        return preg_replace("/<$tag\s+.*>\s*(.*?)\s*<\/$tag>/", "<$tag>".'$1'."</$tag>", $content);
    }
    

  
    public static function base($href = '', $target = null){
        return self::tag('base', ['href' => $href, 'target' => $target]);
    }
    
    
    public static function br(){
        return self::tag('br', null);
    }
    

    public static function input($name, $value = '', $type = '', $class = ''){
        return self::tag('input', [ 'type'=>$type, 'name'=>$name, 'value'=>$value ]);
        
    }
    
    
    /**
     * 生成下拉
     */
    public static function select($name, $options = ['' => '请选择'], $class = ''){
        
        $instance = self::$instance;
        if(is_array($options)){
            $html = self::html();
            foreach($options as $value => $text){
                self::tag('option', ['value'=>$value], $text);
            }
            $options = self::html();
            self::$html = $html;
        }
        self::$instance = $instance;
        
        return self::warp('select', [ 'name'=>$name, 'class'=>$class], $options);
    }
    

    
    public static function img($src, $class = ''){
        return self::tag('img', ['src'=>$src, 'class'=>$class]);
        
    }


    
    /**
     * 过滤标签
     * @param unknown $content
     * @param unknown $tags
     * @param string $allow
     * @return unknown|string
     */
    public  static function filter($content, $tags, $allow = true){
        
        if(empty($tags)) return $content;
        
        $tags = explode(',', preg_replace('/\s+/', '', $tags));
        if($allow){
            
            $contents = [];
            foreach($tags as $tag){
                $pattern = '/<'.$tag.'\s[^>]*>[\s\S]*?<\/'.$tag.'>|<'.$tag.'[^>]*>/';
                preg_match_all($pattern, $content, $matches);
                $contents = array_merge($contents, $matches[0]);
            }
            return implode($contents);
            
        }else{
            
            foreach($tags as $tag){
                $pattern[] = '/<'.$tag.'\s[^>]*>[\s\S]*?<\/'.$tag.'>|<'.$tag.'[^>]*>/';
            }
            return preg_replace($pattern, '', $content);
        }
        
    }
    
    
    /**
     * 包裹标签
     */
    public static function warp($tag, $attrs = null, $html = null){
        
        if(is_string($attrs) || is_null($attrs)){ $html = $attrs; $attrs = [];}
        
        if(is_null($html)){ $html = self::$html; self::$html = ''; }
        if(is_object($html)){ $html = $html->html(); self::$html = ''; }
        
        self::tag($tag, $attrs, $html);
        return self::instance();
        
    }
    
    
    public static function script($src = ''){
        
        $inner = '';
        $attrs['type'] = 'text/javascript';
        if(strtolower(substr(strrchr($src, '.'), 0, 3)) == '.js'){
            $attrs['src'] = $src;
        }else{
            $inner =  $src;
        }
        return self::tag('script', $attrs, $inner);
        
    }
    
    /**
     * 返回或追加html
     */
    public static function html($string = ''){
        
        if(empty($string)){
            $html = self::$html;
            self::$html = '';
            self::$instance = null;
            return $html;

        }
        self::$html .= $string;
        return self::instance();

    }

    
    /**
     * 追加标签开关
     */
    public static function open($tag, $attrs = ''){
        
        self::$open = $tag;
        return self::tag($tag, $attrs, false);
    }
    
    /**
     * 追加标签结尾
     */
    public static function close($tag = ''){
        
        if(empty($tag)){
            $tag = self::$open; self::$open = '';
        }
        if(!empty($tag)) self::$html .= "</{$tag}>";
        return self::instance();
    }
    
    /**
     * html标签转数组
     * @param $html
     * @param string $tag 标签名
     * @param bool $strip 是否过虑其他标签
     * @param array $keys 创建关联数组的key
     * @param array $append 追加数组
     * @return array
     */
    public static function  tagToArray($html,  $tag = 'td', $strip = true, $keys = array(), $append = array()){
        
        if($strip) $html = strip_tags($html, "<$tag>");
        $pattern = "/<{$tag}\b.*?>(.*?)<\/{$tag}>/s";
        preg_match_all($pattern , $html, $matches);
        
        $matches = $matches[1];
        foreach($matches as &$value) $value = trim($value);
        
        if($append) $matches = $matches + $append;
        if(!empty($keys)) $matches = array_combine($keys, $matches);
        
        return $matches;
        
    }


    public static function tableToArray($html){
        
        $html = self::tagToArray($html, 'tr', false);
        
        $result['th'] = self::tagToArray($html[0], 'th');
        unset($html[0]);
        foreach($html as $row){
            $result['td'][] = self::tagToArray($row, 'td');
        }
        
        return $result;
    }

    /****************************************************************************/
    /****************************************************************************/
    /****************************************************************************/
    
    
    
    public static function login($form, $field, $submit = ''){
        
        $html = "\n".'<script>'."\n".'$(function(){'."\n";
        $html .= 'var $form = $("'. $form .'");'."\n";
        $html .= '$form.removeAttr("action").removeAttr("onsubmit");'."\n";
        if($submit) $html .= '$form.find("'.$submit.'").off().removeAttr("onclick");'."\n";
        
        if(is_string($field)) $field = array($field => '');
        foreach($field as $name => $vlaue){
            $html .= '$form.find("[name='.$name.']").val("'.$vlaue.'");';
        }
        
        $html .= "\n".'});'."\n".'</script>';
        
        return $html;
        
    }
    
    
    public static function captcha($keyword, $content, $url){
        
        $pattern = '/<img\s+(?:.|\n)*?'.$keyword.'(?:.|\n)*?>/';
        preg_match($pattern, $content, $match);
        if(isset($match[0])){
            $img = preg_replace('/src=\s*".*?"/', 'src="'.$url.'"', $match[0]);
            $content = preg_replace($pattern, $img, $content);
        }
        
        return $content;
        
    }
    
    

    
    
}


