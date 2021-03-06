<?php

/**
html 生成类

echo Html::tag('a', ['id'=>'a', 'href'=>'www.baidu.com'], '百度')->make();
$label = Html::tag('label')->inner('标签');
$form = Html::tag('form')->attr(['id' => 'form'])->child($label);
echo $form->make();
$form->get('label', 0); //获取第一个 label子元素
$form->child('input', ['class'=>'text', 'name'=> 'username'] );//除了传对象外,还能直接传参数来构造子元素.

//通过魔术方法设置属性值
$input = Html::tab('input')->style('color:red');

//通过魔术方法生成标签
Html::img()->attr('width', 100)->make();

参考 table方法,生成表格

 */

class Html{

    public $attr = [];
    public $tag = '';
    public $inner = '';
    public $elements = [];
    public $closings = ['br', 'hr', 'input', 'source', 'area', 'base', 'link', 'img', 'meta', 'basefont', 'param', 'col', 'frame', 'embed'];

    public function __construct(string $tag, $attr=[], string $inner=''){

        if(!is_array($attr)){
            $inner = $attr; $attr = [];
        }

        $this->tag = $tag;
        $this->attr = $attr;
        $this->inner = $inner;

    }


    /**
     * 魔术方法
     * 设置标签的属性
     */
    public function __call($name, $argument){

        array_unshift($argument, $name);
        return call_user_func_array([$this, 'attr'], $argument);
    }

    /**
     * 魔术静态方法
     * 生成标签
     */
    public static function __callStatic($name, $argument){

        array_unshift($argument, $name);
        return call_user_func_array('self::tag', $argument);
    }


    public static function tag(...$argument){

        return new static(...$argument);
    }

    public function attr($name, $value = ''){

        if(is_array($name)){
            $this->attr = $name + $this->attr;
        }else{
            $this->attr[$name] = $value;
        }

        return $this;
    }

    public function inner(string $value = ''){

        $this->inner = $value;
        return $this;
    }

    public function child($element){

        if(is_string($element)) $element = func_get_args();
        if(is_array($element)){
            $element = self::tag(...$element);
        }

        $this->elements[$element->tag][] = $element;

        return $this;
    }


    public function make(){

        $attr = array_reduce(array_keys($this->attr), function($carry, $item){

            $carry .= sprintf(" %s=\"%s\"", $item, $this->attr[$item]);
            return $carry;
        });

        $html = sprintf("<%s%s>", $this->tag, $attr);
        //遍历子元素调用 make 方法，形成递归
        foreach ($this->elements as $element){

            foreach($element as $item){
                if(is_object($item)){
                    $item = $item->make();
                }
                $html .= $item;
            }

        }

        if(!in_array($this->tag, $this->closings)) $html .= sprintf("%s</%s>", $this->inner, $this->tag);

        return  $html;

    }


    public static function base($href = '', $target = null){

        return self::tag('base', ['href' => $href, 'target' => $target])->make();
    }


    public static function input($name, $value='', $type = ''){

        return self::tag('input', [ 'name'=>$name, 'value'=>$value, 'type'=>$type ]);
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
     * 生成下拉
     */
    public static function select($attr, array $option = ['' => '请选择']){

        $select = self::tag('select', $attr);

        if(is_array($option)){

            foreach($option as $value => $text){
                $select->child(self::tag('option', ['value'=>$value], $text)) ;
            }

        }
        return $select->make();

    }

    /**
     * 获取子元素
     * @param string $name
     * @param int $index
     * @return mixed
     */
    public function get($name = '', $index=0){

        return $this->elements[$name][$index];
    }


    /**
     * 生成table
     */
    public static function table($attr, $data, $head = []){

        if(empty($head)){
            $head = array_keys(current($data));
        }

        $table = self::tag('table', $attr);
        $table->child('thead');
        $table->get('thead', 0)->child('tr');

        foreach($head as $key => $value){
            $table->get('thead')->get('tr')->child('th', $value);
        }

        $table->child('tbody');
        foreach($data as $row){

            $tr = self::tag('tr');
            foreach($head as $key => $value){

                if(is_numeric($key)) $key = $value;
                $tr->child('td', $row[$key]);
            }

            $table->get('tbody')->child($tr);

        }

        return $table->make();

    }

    
    /**
     * 包裹标签
     */
    public function warp(...$argument){

        return self::tag(...$argument)->child($this);
    }

    public static function  toArray($html,  $tag='td', $strip=false, $keys=[], $append=[]){

        if($strip) $html = strip_tags($html, "<$tag>");
        $pattern = "/<{$tag}\b.*?>(.*?)<\/{$tag}>/s";
        preg_match_all($pattern , $html, $matches);

        $matches = $matches[1];
        foreach($matches as &$value) $value = trim($value);

        if($append) $matches = $matches + $append;
        if(!empty($keys)) $matches = array_combine($keys, $matches);

        return $matches;

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


