<?php
namespace zbs\obj;

class Validate{
    

    /**
     * 密码强度验证
     * @param unknown $password
     * @return multitype:number string
     */
    public function strong($password){
        //不能是纯数字/字母/符号[_@#.*]或长度小于8或大于32
        $status = !preg_match('/(?:^\d+$)|(?:^[a-zA-Z]+$)|(?:^[_@#\.\*]+$)/', $password) && preg_match('/.{8,32}/', $password);
        return $status? array('status'=>1, 'info'=>'success') : array('status'=> -1, 'info'=>'密码必须是数字+字符组合且长度在8至32个字符');
    }
    
    /**
     * 手机号验证
     * @param unknown $mobile
     * @return number
     */
    public static function isMobile($mobile){
        return preg_match('/^1[34578]\d{9}$/', $mobile);
    }
    
    /**
     * 邮箱验证
     * @param unknown $email
     * @return number
     */
    public static function isEmail($email){

        ///^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)*(\.[a-z]{2,})$/
        return preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $email);
    }
    
    /**
     * 中文验证
     * @param unknown $string
     * @return number
     */
    public static function isChinese($string){
        return preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $string);
    }


    /**
     * 字符串类型
     */
    public static function getType($string){

        $return = 'unknow';
        if(is_numeric($string)){
            $return = 'number';
            if(self::isMobile($string)){
                $return = 'mobile';
            }
        }else{
            if(self::isEmail($string)){
                $return = 'email';
            }else if(self::isChinese($string)){
                $return = 'chinese';
            }
        }
        return $return;
    }



    /**
     * 身份证号解析
     * @param string $number
     * @return array
     */
    function idcard_analytic(string $number){

        if(!is_string($number) && empty($number)) return false;
        $area = str_split(substr($number, 0, 6), 2);
        $areas[] = $area[0].'0000';
        $areas[] = $area[0].$area[1].'00';
        $areas[] = $area[0].$area[1].$area[2];
        $areas= M('area')->where(array('id'=>array('in', $areas)))->order('id')->select();
        if(empty($areas) || count($areas) < 3){
            return false;
        }
        $birthday = date('Y-m-d', strtotime(substr($number, 6, 8)));
        $age = date('Y') - substr($number, 6, 4);
        if(intval(substr($number, 10, 4)) > intval(date('md'))) $age--;
        $sex = (substr($number, 14, 3) % 2 == 0)? '女' : '男';
        return array('area'=>$areas[0]['name'].'-'.$areas[1]['name'] .'-'.$areas[2]['name'], 'number'=>$number, 'sex'=>$sex, 'birthday'=>$birthday, 'age'=>$age);

    }


    /**
     * 身份证最后一个校验
     * @param $code
     * @return bool
     */
    public static function idcardCheck($code){

        $wi = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        $ai = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $sigma = 0;
        for ($i = 0; $i < 17; $i++) {
            $sigma += ((int) $code{$i}) * $wi[$i];
        }

        return $ai[($sigma % 11)] == $code[-1]? true :false;
    }



}


