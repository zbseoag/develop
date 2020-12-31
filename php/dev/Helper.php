<?php

class Helper {



    /**
     * 分页方法
     * @param unknown $page
     * @param unknown $count
     * @param number $size
     * @return boolean|number[]
     */
    public static function page($page, $count, $size = 20){
        $total = ceil($count / $size);
        return ($page > $total)? false : array(($page - 1) * $size, $size, $total);

    }



}