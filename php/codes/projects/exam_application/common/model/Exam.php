<?php
namespace app\common\model;


class Exam extends Model {
    
    protected $search = ['%title%', 'create_time', 'status', 'startime', 'endtime'];
    
    public function getStatusAttr($value = ''){
        
        $options = array(
            self::STATUS_FORBID => '已关闭',
            self::STATUS_USABLE=> '已启用',
        );
        
        //获取所有选项
        if(func_num_args() == 0) return $options;
        return isset($options[$value])? $options[$value] : $value;
        
    }
    
    
    
    
}
