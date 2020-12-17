<?php
//抽奖类

class Draw{
    
    protected static $drawId;
    protected static $uid;
    protected static $default;
    private static $endTime;
    
    protected $prize = array();
    
    //奖品类型配置 0未中奖, 1实物奖 2加息券 7会员等级奖 8现金奖 9积分奖
    protected static $prizType = array(0=>'without', 2=>'increase', 9=>'score', 7=>'vip', 8=>'money' );

    protected static $table = array(
        'prize' => 'd_prize',
        'draw_count' =>'d_prize_count',
        'winner_user' => 'd_prize_user',
        'score_list'=> 'd_score_list',
        'money_list'=>'d_user_fund',
        'user' => 'd_user',
        'repay_list'=>'d_repay_list',//待收
        'invest_list'=>'d_invest_list',
        'user_card' => 'd_user_card'//用户卡包
    );
    
    
    /**
     * 初始化模型
     * @param number $uid
     * @param number $drawId
     * @param string $endTime
     * @param number $default
     */
    public function __construct($uid, $drawId, $endTime, $default){
        
        self::$uid = $uid;
        self::$drawId = $drawId;
        self::$endTime = $endTime;
        self::$default = $default;
    }
    
    
    /**
     * 用户抽奖机会
     * @param number $uid 
     * @param number $aid 活动id
     */
    public static function getUserChance($uid, $drawId){

        if(empty($uid) || empty($drawId)) return 0;
        return M(self::$table['draw_count'], null)->where(array('uid'=>$uid, 'status'=>0, 'pid'=>$drawId))->count();
    }
    
    
    /**
     * 中奖记录总数
     * @param number $drawId
     */
    public static function getWinnerCount($drawId){
    
        return M(self::$table['winner_user'], null)->where(array('pid'=>$drawId))->count();
    }
    
    /**
     * 中奖记录列表
     * @param number $drawId
     * @param number|array $limit
     */
    public static function getWinnerList($drawId, $limit=''){
        
        is_array($limit) || $limit = array('start'=>0, 'length'=>$limit);;
        
        $table['u'] = 'uname';
        $table['p'] = 'name,id';
        $table['pu'] = 'addtime';
        $field = table_field($table);
        $record = M(self::$table['winner_user'], null)->alias('pu')->field($field)
                ->join('d_user u ON u.uid=pu.uid')
                ->join('d_prize p ON p.id=pu.prizeid')
                ->where(array('pu.pid'=>$drawId))
                ->order('p.orderid DESC,pu.addtime DESC')->limit($limit['start'], $limit['length'])->select();
        return $record;
    }
    

    
    public function drawRun($rule=array()){
        $rule = self::$rule;
        $Model = M(self::$table['prize'], null);
        $Model->startTrans();
        $times = self::getUserChance(self::$uid, self::$drawId);
        if($times < 1){ $error = array(-1, '您没有抽奖机会');goto FunErr; }
        
        $this->_updateTodayAmount('day'); //更新当天出奖数量

        $record = $Model->where(array('pid'=>self::$drawId))->field('id,gid,name,value,min,max,today_amount,amount')->select();
        foreach($record as $key => $row){
            $data[$row['id']] = $row;
        }
    
        //默认奖品
        $this->prize = $data[self::$default];
        
        //当前抽奖机会
        $nowTimes = M(self::$table['draw_count'], null)->where(array('uid'=>self::$uid, 'status'=>0, 'pid'=>self::$drawId))->field('id,lid,utype')->order('id')->find();
        
        //抽奖
        $lottery = $this->_getRand($this->_getRule($data, $rule, $nowTimes['utype']));

        $lottery && $this->prize = $lottery;
        
        //更新奖品数量
        $data = array();
        $data['amount'] = ($this->prize['amount'] > 0)? $this->prize['amount'] - 1 : 0;
        if($this->prize['today_amount'] > 0){
            $data['today_amount'] = $this->prize['today_amount'] - 1;
        }else{
            $data['today_amount'] = 0;
        }
        if($Model->where(array('id'=>$this->prize['id']))->save($data) === false){ $error = array(-100, '抽奖失败'); goto FunErr; }
        
        //更新用户抽奖次数表
        if(!M(self::$table['draw_count'], null)->where(array('id'=>$nowTimes['id']))->save(array('status'=>1))){
            $error = array(-2, '抽奖失败'); goto FunErr;
        }

        
        //用户获奖列表数据
        $data = array(
            'uid'=>self::$uid,
            'prizeid'=>$this->prize['id'],
            'pid'=>self::$drawId,
            'prizegid' =>$this->prize['gid'],
            'addtime'=>time()
        );
        //可分发的奖品
        if(!empty(self::$prizType[$this->prize['gid']])){
            switch(self::$prizType[$this->prize['gid']]){
                case 'money':if($this->prize['value'] > 50){//超过50则是人工审核发现金
                                $funStep = true; goto FunErr;
                             }else{
                                 $done = $this->_addUserMoney($this->prize['value'], $nowTimes['lid']);
                             }
                    break;
                case 'score': $done = $this->_addUserScore($this->prize['value']);
                    break;
                case 'vip': $done = $this->_addUserScore($this->prize['value'], false);
                    break;
                case 'increase': $done = $this->_addUserCard($this->prize['value']);
                
                    break;
                case 'without':$funStep = true; goto FunErr;
                    break;
            }
            if(!$done){ $error = array(-3, '抽奖失败'); goto FunErr; }
            $data['status'] = 1;//已领
        }

        //添加用户获奖表
        if(!M(self::$table['winner_user'], null)->add($data)){ 
            $error = array(-7, '抽奖失败');goto FunErr;
        }
        
        $funStep = true;
        FunErr:
        if(empty($funStep)){
            $Model->rollback();
            $return = array('status'=>$error[0], 'info' =>$error[1]);
        }else{
            $Model->commit();
            $return['status'] = 1;
            $return['angle'] = mt_rand($this->prize['min'], $this->prize['max']);
            $return['name'] = $this->prize['name'];
            $return['times'] = $times - 1;
            $return['id'] = $this->prize['id'];
        }
        return $return;
        
    }
    
    
    //抽奖算法
    private function _getRand($data){
    
        $amountSum = M(self::$table['prize'], null)->where(array('pid'=>self::$drawId))->sum('today_amount');
        $start = $sum = 0;
        foreach($data as $key => $row){
            if($row['today_amount'] > 0){
                if(!empty($row['setChance'])){
                    if($row['setChance'] < 0){
                        $row['today_amount'] += $amountSum * $row['setChance'];
                    }else{
                        $row['today_amount'] = $amountSum * $row['setChance'];
                    }
                }
                $chance = intval($row['today_amount'] / $amountSum * 1000);
                $sum += $chance;
                $data[$key]['chance'] = array($start, $sum);
                $start += $chance;
            }else{
                unset($data[$key]);
            }
        }
        //p($data);exit;
        $rand = mt_rand(1, $sum);//随机取出
        
        foreach ($data as $key => $row){
            if($row['chance'][0] < $rand && $rand <=$row['chance'][1]){
                return $row;
            }
        }
        return false;
    }
    
    
    /**
     * 抽奖规则
     * @param unknown $data
     * @return unknown|string
     *
     */
    private static  $rule = array(
            //中奖数量限定规则
            'count'=>array(
                //array(array(13),        2), //小米电源
                array(array(63,65,67,69,71,73,75),    1),//ipad与iphone6
                //array(array(19),        1),//V4会员
            ),
            
            //投标次数规则
            'investloan' => array(
                //array('$investloan < 5', 'remove', array(30, 40)),
                array('$investloan < 1', 'doset', array(61)),
                ),
             );
             /*
            //删除部分人的规则
            'delete' => array(
                array(array(7507,70,50,16,54),    array(15,17)),
            ),
            //待收款规则
            'collect' => array(
                array('$collect < 200000', 'remove', array(15,17)),
                array('$collect >= 300000', 'raise', array(15 =>'0.05', 17=>'0.05', 23=>'-0.1')),
            ),
            
            //用户分组规则
            'usergroup' => array(
                array('$usergroup >= 4', 'remove', array(31, 33)),//删除v3/v4
                array('$usergroup >= 3', 'remove', array(33)),//删除v3
            ),
            
            //指定中奖规则
            'insider' => array(
                array(array(12379,1237), array(35)),
            ),
            
            'timestype' => array(
                array(array(1,3,6), 'remove', array(30, 40)),
                array(array(2,4), 'doset', array(20, 10)),
            );
        );
        */
    private function _getRule($data, $rule, $timestype=''){
        
        $where['uid'] = self::$uid;
        foreach($rule as $name => $group){
            switch($name){
                //数量
                case 'count': 
                    foreach($group as $item){
                        $where['prizeid'] = array('in', $item[0]);
                        if(M(self::$table['winner_user'], null)->where($where)->count() >= $item[1]){
                            foreach($item[0] as $id) unset($data[$id]);
                        }
                    }
                    break;
                //删除
                case 'delete':
                    foreach($group as $item){
                        if(in_array(self::$uid, $item[0])){
                            foreach($item[1] as $id) unset($data[$id]);
                        }
                    }
                    break;
                //待收    
                case 'collect': 
                    $collect = M(self::$table['repay_list'], null)->where(array('uid'=>self::$uid, 'status'=>0))->sum('now_amount');
                    foreach($group as $item){
                        eval('$condition=(' . $item[0] . ');');
                        if($condition){
                            if($item[1] == 'remove'){
                                foreach($item[2] as $id) unset($data[$id]);
                            }elseif($item[1] == 'raise'){
                                foreach($item[2] as $id => $value){
                                    $data[$id]['setChance'] = $value;
                                }
                            }
                        }
                        
                    }
                    break;
                //指定奖项
                case 'insider':
                    foreach($group as $item){
                        if(in_array(self::$uid, $item[0])){
                            foreach($data as $key => $row){
                                if(!in_array($key, $item[1]))unset($data[$key]);
                            }
                        }else{
                            foreach($item[1] as $id) unset($data[$id]);
                        }
                    }
                    break;
                
                //会员规则
                case 'usergroup':
                    $usergroup = M(self::$table['user'], null)->where(array('uid'=>self::$uid))->getField('groupid');
                    foreach($group as $item){
                        eval('$condition=(' . $item[0] . ');');
                        if($condition){
                            if($item[1] == 'remove'){
                                foreach($item[2] as $id) unset($data[$id]);
                            }
                        }
                    }
                    break;
                    

                //未投标规则
                case 'investloan':
                    $investloan = M(self::$table['invest_list'], null)->where(array('uid'=>self::$uid,'is_ty_loan'=>0))->count();
                    
                    foreach($group as $item){
                        eval('$condition=(' . $item[0] . ');');
                        if($condition){
                            if($item[1] == 'doset'){
                                foreach($data as $key => $row){
                                    if(!in_array($key, $item[2])) unset($data[$key]);
                                }
                            }elseif($item[1] == 'remove'){
                                foreach($item[2] as $id) unset($data[$id]);
                            }
                        }
                    }
                    break;
                    
                case 'timestype': 

                    break;
            }
            
        }
        return $data;
    }




    

    //更新当天出奖数量
    private function _updateTodayAmount($mode='time'){

        if(empty(self::$endTime)) return false;
        $today = date('Y-m-d');
        $where = array('pid'=>self::$drawId, 'update_day'=> array('neq', $today));
    
        $Model = M(self::$table['prize'], null);
        $record = $Model->where($where)->field('id,amount')->select();
        $leaveTimes = strtotime(self::$endTime) - strtotime($today); //活动剩余时数
        if($mode == 'time'){$leaveTimes += 86400;}
        if($leaveTimes < 86400) $leaveTimes = 86400;
        foreach($record as $key => $row){
            $todayAmount = ($row['amount'] > 0)? intval($row['amount'] / ($leaveTimes / 86400)) : 0;
            $Model->where('id='.$row['id'])->save(array('today_amount'=>$todayAmount, 'update_day'=>$today));
        }
    }

}
