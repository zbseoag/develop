<?php
class Date {

    /**
     * 求两个日期的差数
     * @param $date1
     * @param $date2
     * @return mixed
     * @throws Exception
     */
    public static function diffDays($date1, $date2){

        $date1 = new DateTime($date1);
        return $date1->diff(new DateTime($date2))->days;
    }



    /**
     * 每加一个月,月份最后一天
     * @param unknown $nowtime
     * @param number $addmonth
     * @return number
     */
    function month_lastday($nowtime, $addmonth = 1){

        $strtime = strtotime('+'.$addmonth. ' month', $nowtime);
        if(date('j', $nowtime) != date('j', $strtime)){
            $strtime = strtotime('-1 day', strtotime(date('Y-m-1',$strtime)));
        }
        return $strtime;
    }


    /**
     * 取得时间区间状态
     * @param string $start
     * @param string $end
     * @param string $config
     * @return string
     */
    function time_period($start, $end, $config='auto'){
        $nowtime = time();
        $start = strtotime($start);
        $end = strtotime($end);
        $key = ($nowtime < $start)? 'wait' : (($nowtime > $end)? 'end' : 'ing');
        if($config == 'auto') $config = array('wait'=>'未开始', 'ing'=>'进行中', 'end'=>'已结束');
        return is_array($config)? $config[$key] : $key;
    }



//格式化时间
    function time_format($time, $start='d', $keep='s', $unit=true){

        $day = $hours = $minutes = $seconds = '';
        if($start == 'd'){
            //$time / 3600 得到小时数,再除以24小时,余数则为不足一天的小时数
            $day = floor($time / 3600 / 24);
            $hours = floor($time / 3600) % 24;
            $minutes = floor($time / 60) % 60;
            $seconds = $time % 60;
        }elseif($start == 'h'){
            $hours = floor($time / 3600);
            $minutes = floor($time / 60) % 60;
            $seconds = $time % 60;
        }elseif($start == 'i'){
            $minutes = floor($time / 60);
            $seconds = $time % 60;
        }

        //如果要带单位
        if($unit){
            $day = ($day)? $day.'天' : '';
            $hours = ($hours)? $hours.'小时' : '';
            $minutes = ($minutes)? $minutes.'分钟' : '';
            $seconds = ($seconds)? $seconds.'秒' : '';
        }
        $new['d'] = $day;
        $new['h'] = $hours;
        $new['i'] = $minutes;
        $new['s'] = $seconds;

        $time = '';
        foreach($new as $key => $val){
            if($key == $start) $go = true;
            if($go){
                if($unit){
                    $time .= $val;
                }else{
                    $time[$key]= $val;
                }
            }
            if($key == $keep) break;
        }
        return $time;
    }



}