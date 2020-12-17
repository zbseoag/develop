<?php
/**

/**
 * 所有组合对象要实现的接口
 * Interface RenderableInterface
 */
interface Renderable  {

    public function render(): string;
}



/**
 * Form 表单对象
 * Class Form
 */
class Form implements Renderable {


    private $elements;

    /**
     * 添加组合的对象
     * @param Renderable $element
     */
    public function addElement(Renderable $element){

        $this->elements[] = $element;
    }


    public function render(): string {

        $html = '<form>';
        foreach ($this->elements as $element){
            $html .= $element->render();
        }

        return  $html .= '</form>';
    }


}


/**
 * Input 对象
 * Class InputElement
 */
class InputElement implements Renderable {

    public function render(): string {
        return '<input type="text" />';
    }
}


/**
 * 普通文本对象
 * Class TextElement
 */
class TextElement implements Renderable {

    private $text;

    public function __construct(string $text){
        $this->text = $text;
    }

    public function render(): string{
        return $this->text;
    }
}



$form = new Form();

$form->addElement(new TextElement('Email:'));
$form->addElement(new InputElement());

$form->addElement(new TextElement('Password:'));
$form->addElement(new InputElement());


echo $form->render();



