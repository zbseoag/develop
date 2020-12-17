<?php

/**

生成：<form id="form"><label><b>用户名：</b><input name="usrname"></label></form>

$form = Html::tag('form')->attr(['id' => 'form']);
$label = Html::tag('label');
$b = Html::tag('b');
$input = Html::tag('input')->attr('name', 'usrname');

echo $form->add($label->add($b->add('用户名：'))->add($input))->make();

*/

class Html{

    protected static $init = null;
    protected $attr = [];
    protected $tag = '';
    protected $elements = [];
    public $closings = ['br', 'hr', 'input', 'source', 'area', 'base', 'link', 'img', 'meta', 'basefont', 'param', 'col', 'frame', 'embed'];

    protected function __construct($tag){

        $this->tag = $tag;
    }

    public static function tag($value){

        return new static($value);
    }

    public function attr($name, $value = ''){

        if(is_array($name)){
            $this->attr = $name + $this->attr;
        }else{
            $this->attr[$name] = $value;
        }

        return $this;
    }

    public function add($element){

        $this->elements[] = $element;
        return $this;
    }

    public function make(){

        $attr = array_reduce(array_keys($this->attr), function($carry, $item){
            $carry .= ' ' .$item . '=' . '"'.$this->attr[$item] .'"' ;
            return $carry;
        });

        $html = '<'.$this->tag. $attr.'>';
        //遍历子元素调用 make 方法，形成递归
        foreach ($this->elements as $element){

            if(is_object($element)){
                $element = $element->make();
            }
            $html .= $element;
        }

        if(!in_array($this->tag, $this->closings)) $html .= '</'.$this->tag.'>';

        return  $html;

    }


}


