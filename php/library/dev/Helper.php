<?php

class Helper {



    /**
     * 分页方法
     * @param unknown $page
     * @param unknown $count
     * @param number $size
     * @return boolean|number[]
     */
    public static function page($num, $count, $size = 50){
        $total = ceil($count / $size);
        return ($num > $total)? false : array(($num - 1) * $size, $size, $total);

    }



}