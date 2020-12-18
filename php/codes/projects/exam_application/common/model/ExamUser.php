<?php
namespace app\common\model;


class ExamUser extends Model {
    

    
    public function getStatusAttr($value = ''){
        
        $options = array(
            self::STATUS_FORBID => '已禁用',
            self::STATUS_USABLE=> '已启用',
            self::STATUS_DELETED => '已删除',
        
        );
        
        //获取所有选项
        if(func_num_args() == 0) return $options;
        return isset($options[$value])? $options[$value] : $value;
        
    }
    
    
}
