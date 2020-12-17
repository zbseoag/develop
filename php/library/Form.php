<?php


class Form {
    

    public static function get($name = ''){

        if(empty($data)) $data = \think\Request::instance()->get();
        return self::input($data, $name);
    }
    
    
    public static function post($name = ''){
    
        $data = \think\Request::instance()->post();
        return self::input($data, $name);
    }
    
    /**
     $input = Array(
         [_field] => field
         [_value] => value
         [_datetime] => Array(
             [field] => time
             [start] => 2014-06-18 23:00:00
             [end] => 2014-06-12 23:00:00
         )
     )
     * @param array $input
     * @param boolean $timeint
     * @return array
     */
    public static function input($input, $name = ''){
        
        if(isset($input['_field']) && isset($input['_value']) && !empty($input['_value'])) {
            $input[$input['_field']] = trim($input['_value']);
    
        }
 
        if(isset($input['_datetime']['field']) && !empty($input['_datetime']['field'])){
            
            if(isset($input['_datetime']['start']) && !empty($input['_datetime']['start'])){
                $input[$input['_datetime']['field']]['>='] =  $input['_datetime']['start'];
            }
            
            if(isset($input['_datetime']['end']) && !empty($input['_datetime']['end'])){
                $input[$input['_datetime']['field']]['<'] = $input['_datetime']['end'];
            }
            
        }
        
        if(isset($input['_sort']) && !empty($input['_sort'])){
            $sort = strstr($input['_sort'], ':');
            if(empty($sort) && $sort == ':'){
               unset($input['_sort']);
            }else{
               $input['_sort'] = str_replace(':', ' ', $input['_sort']);
            }
        }
        
        unset($input['_field'], $input['_value'], $input['_datetime']);

        $options = ['@empty' => '', '@null' => null];
        foreach($input as $key => $value){
         
            if($value == ''){
                unset($input[$key]); continue;
            }
            //处理这种数据 'create'=>array('egt' => '2017-07-02', 'lt' => '2017-08-04',)
            if(is_array($value) && is_string(current($value))){
           
                foreach($value as $l => $v){
                    unset($input[$key][$l]);
                    if(empty($v)) continue;
                    $input[$key][] = array($l, $v);
                }
                if(empty($input[$key])) unset($input[$key]);
                else if(count($input[$key]) < 2) $input[$key] = $input[$key][0];
            }else{
                
                if(is_string($value)) $value = trim($value);
                if(is_string($value) && isset($options[$value])) $value = $options[$value];
                $input[$key] = $value;
            }
        }
        
        if(!empty($name)){
            //必要参数检测
            if(is_array($name)){
                foreach($name as $key => $val){
                    if(!isset($input[$val])) return '';
                }
            }else{
                $input = isset($input[$name])? $input[$name] : '';                
            }
        }
        
        return $input;
    
    }
    
    /**
     * 表单提交的多条记录二维数据转换
     * @param unknown $input
     * @return unknown
     */
    public static function format($input){
        
        foreach($input as $field => $row){
            foreach($row as $key => $value){
                $data[$key][$field] = $value;
            }
        }
        return $data;
    }


    
}