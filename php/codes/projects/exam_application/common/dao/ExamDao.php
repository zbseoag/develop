<?php
namespace app\common\dao;

use think\Exception;
use app\common\model\Exam;
use app\common\model\ExamQuestion;


class ExamDao extends Dao {
    
    
    public static function format($input){
    
        
        if(isset($input['title']) && empty($input['title'])){
            throw new Exception('请填写问卷标题');
        }
    
        if(isset($input['explain']) && empty($input['explain'])){
            throw new Exception('请填写问卷说明');
        }
    
        if(isset($input['question']) && empty($input['question'])){
            throw new Exception('请添加问卷的问题');
        }

        $id = self::shift($input, 'qid');
        $question = self::shift($input, 'question');
        $option = self::shift($input, 'option');
        $default = self::shift($input, 'default');
        
        if(!is_array($question)) $question = [];
        $items = [];
        
        foreach($question as $key => $row){

            $items[] = array(
                'id' => isset($id[$key])? $id[$key] : 0,
                'question' => $row,
                'option' => json_encode($option[$key], JSON_UNESCAPED_UNICODE),
                'default' => isset($default[$key])?  $default[$key] : '',
                'sort' => $key,
            );
            
        }
        
        $input['question'] = $items;
        
        return $input;
    
    }
    
    
    public static function create($input){
        
        $input = self::format($input);
        $question = self::shift($input, 'question');

        
        if(empty($input['startime'])) $input['startime'] = date('Y-m-d H:i:s');
        if(empty($input['endtime'])) $input['endtime'] = date('Y-m-d H:i:s');
    
        //插入问卷
        $exam = ExamModel::create($input, true);
        foreach($question as $row){
    
            //插入问题
            $row['exam_id'] = $exam->id;
            $result = ExamQuestionModel::instance()->isUpdate(false)->allowField(true)->save($row);
        }
        
        return $exam->id;
    }
    
    
    public static function update($input){
    
        $input = self::format($input);
        $question = self::shift($input, 'question');
        
        if(empty($input['startime'])) $input['startime'] = date('Y-m-d H:i:s');
        if(empty($input['endtime'])) $input['endtime'] = date('Y-m-d H:i:s');
    
        //更新问卷
        $exam = ExamModel::update($input, '', true);
  
        $id = [];
        $ExamQuestion = ExamQuestionModel::instance();
        foreach($question as $row){
            
            if(empty($row['id'])){
                $row['exam_id'] = $exam->id;
                $ExamQuestion->data($row)->allowField(true)->isUpdate(false)->save();
    
                $id[] =  $ExamQuestion->id;
            }else{
  
                //收集更新的问题id
                $id[] =  $row['id'];
                $ExamQuestion::update($row, '', true);
       
      
            }
            
        }
        
        //删除被删除的问题
        if($id) $result = ExamQuestionModel::where('exam_id', $exam->id)->where('id', 'not in', $id)->delete();
        
        
    }
    



}