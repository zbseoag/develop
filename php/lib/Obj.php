<?php
/**

echo Obj::new('Redis');
echo Obj::new('Redis')->method('set')?->isPublic();

 */

class Obj extends ReflectionClass {

    public static function std(?array $attrs = null){

        $std = new \stdClass();
        foreach($attrs as $key => $value){

            is_numeric($key)? $std->$value = '' : $std->$key = $value;
        }
        return $std;
    }

    public static function new($argument){

        return new static($argument);
    }

    public function constant($name){

        return $this->getConstant($name);
    }

    public function property($name){

        try{
            return $this->getProperty($name);
        }catch(\ReflectionException $e){
            return null;
        }

    }

    public function method($name){

        try{
            return $this->getMethod($name);
        }catch(\ReflectionException $e){
            return null;
        }

    }


    public function __toString(){

        $items = $this->getReflectionConstants();
        foreach($items as $key => $item){
            $class['const'][] = sprintf('const %s = %s;', $item->getName(), $item->getValue());
        }

        $items = $this->getProperties();
        foreach($items as $key => $item){
            $class['prop'][] = sprintf('%s %s = "%s"', implode(' ', Reflection::getModifierNames($item->getModifiers())), $item->getName(), $item->getDefaultValue());
        }

        $items = $this->getMethods();
        foreach($items as $key => $item){

            $params = $item->getParameters();
            $param = '';
            if($params){
                foreach($params as $item1){

                    $param .= $item1->getType() . ' ' . $item1->getName();
                    if($item1->isDefaultValueAvailable()) $param .= '=' . $item1->getDefaultValue() . ', ';
                    else $param .= ', ';
                }
                $param = rtrim($param, ', ');
            }

            $class['method'][] = sprintf('%s %s(%s )', implode(' ', Reflection::getModifierNames($item->getModifiers())), $item->name, $param);
        }

        $output = array_merge($class['const'] ?? [], $class['prop'] ?? [], $class['method'] ?? []);

        return implode("\n", $output);

    }


}

