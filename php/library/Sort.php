<?php

class Sort {

    /**
     * 交换数组中的元素
     * @param $input
     * @param $i
     * @param $j
     */
    protected static function swap(&$input, $i, $j){

        $tmp = $input[$i];
        $input[$i] = $input[$j];
        $input[$j] = $tmp;
    }


    /**
     * 冒泡排序
     * @param $input
     * @return array
     */
    public static function bubble($input){

        if(!is_array($input)) $input = func_get_args();
        if(empty($input)) return $input;

        $length = count($input);
        for($i = 0; $i < $length - 1; $i++){
            $swaped = false; //用于判断一轮比较是否发生过交换，如果没有，表示排序完成

            //从后面往前比较，每轮都排序好一个元素，因此必须 $j>$i
            for($j = $length - 1; $j > $i; $j--){
                if($input[$j] > $input[$j - 1]){
                    $swaped = true;
                    self::swap($input, $j, $j - 1);
                }
            }
            if(!$swaped) break;

        }
        return $input;

    }



    /**
     * 选择排序法: 每次从数组中找到一个最值，放在相应位置
     * O(n^2)
     */
    public static function select($input){

        if(!is_array($input)) $input = func_get_args();
        if(empty($input)) return $input;

        $length = count($input);
        for ($i = 0; $i < $length - 1; $i++) {

            $max = $i;
            //从 i+1 开始比较，因为 max 默认为i了，i就没必要比了
            for ($j = $i + 1; $j < $length; $j++) {
                if ($input[$max] < $input[$j]) $max = $j;
            }
            //如果 max 不为 i，说明找到了更大的值，则交换
            if($max != $i) self::swap($input, $i, $max);

        }

        return $input;

    }

    /**
     * 插入排序法:从第二个元素开始比较,然后放到相应的位置,就想打牌一样.
     * @param $input
     * @return array
     */
    public static function insert($input){

        if(!is_array($input)) $input = func_get_args();
        if(empty($input)) return $input;

        $length = count($input);
        //从第二个元素开始比较,然后放到相应的位置
        for($i = 1; $i < $length; $i++){

            //j与它前面的元素相比，所以每一轮要比较的个数会越来越多
            for($j = $i; $j > 0; $j--){

                if($input[$j - 1] < $input[$j]){
                    self::swap($input, $j, $j - 1);
                }else{
                    break;
                }
            }

        }

        return $input;
    }


    /**
     * 快速排序法
     * @param $input
     * @return array
     */
    public static function quick($input) {

        if(!is_array($input)) $input = func_get_args();

        //先判断是否需要继续进行
        $length = count($input);
        if($length <= 1) return $input;

        $base = $input[0]; //选择第一个元素作为基准

        //初始化两个数组
        $left = $right = array();
        for($i = 1; $i < $length; $i++) {
            if($base > $input[$i]){
                $left[] = $input[$i]; //小的放入左边
            } else{
                $right[] = $input[$i];//大的放入右边
            }
        }

        //分段之后分别递归调用
        $left = self::quick($left);
        $right = self::quick($right);

        return array_merge($left, array($base), $right);

    }

}


