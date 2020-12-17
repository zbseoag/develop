<?php
namespace app\common\dao;

use think\Exception;
use app\common\model\ExamAnswer;
use app\common\model\ExamUser;
use app\common\model\Exam;
use app\common\model\ExamQuestion;



class ExamAnswerDao extends Dao {
    
    
    public static function format($input){
        
        $exam_id = self::shift($input, 'id');
        $answer = self::shift($input, 'answer', array());
        
        $exam = ExamModel::instance()->byId($exam_id)->find();
        
        if(!$exam){
            throw new Exception('问卷不存在');
        }

        if($exam->getData('status') < ExamModel::STATUS_USABLE){
            throw new Exception('问卷已关闭');
        }
        
        if(strtotime($exam->startime) > time()){
            throw new Exception('问卷未开始');
        }
    
        if(strtotime($exam->endtime) < time()){
            throw new Exception('问卷已结束');
        }
    
        $count = ExamQuestionModel::where('exam_id', $exam_id)->count();
        if($count != count($answer)){
            throw new Exception('问卷没有答完,无法提交');
        }

    
        $input['startime'] = date('Y-m-d H:i:s', $input['startime']);
        foreach($answer as $key => $value){

            $items[] = array(
                'exam_id' => $exam_id,
                'question_id' => $key,
                'answer' => $value,
            );
            
        }
        $input['answer'] = $items;
        
        return $input;
    
    }
    
    
    public static function create($input){
        
        $input = self::format($input);
        $answer = self::shift($input, 'answer');

        //插入答题者
        $user = ExamUserModel::create($input, true);
 
        foreach($answer as $row){
    
            //插入答案
            $row['exam_user_id'] = $user->id;
            ExamAnswerModel::instance()->isUpdate(false)->allowField(true)->save($row);
        }
        
    }
    


}