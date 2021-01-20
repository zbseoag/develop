<?php
namespace app\home\controller;

use app\common\model\ExamModel;
use app\common\model\ExamQuestionModel;
use app\common\dao\ExamAnswerDao;
use think\Exception;
use think\Db;
use think\Session;


class Exam extends Controller {
    
    public function index(ExamModel $model){
    
        $id = $this->request->get('id');
        
        $record = $model::get($id);
        if(!$record) $this->error('问卷不存在');
    
        $status = ['enable'=>'on', 'text'=> '提 交 答 案'];
        if($record->getData('status')  < ExamModel::STATUS_USABLE){
            $status = ['enable'=>'off', 'text'=> '问 卷 已 关 闭'];
        }else if(strtotime($record->startime) > time()){
            $status = ['enable'=>'off', 'text'=> '问 卷 未 开 始'];
        }else if(strtotime($record->endtime) < time()){
            $status = ['enable'=>'off', 'text'=> '问 卷 已 结 束'];
        }
        
        $record = $record->toArray();
        $question = ExamQuestionModel::instance()->byExamId($record['id'])->order('sort')->select();
        
        $checked = [];
        foreach($question as &$row){
            $options = json_decode($row->option, true);
            $row['option'] = $options;

            if(isset($options[ord($row->default) - 65])) $checked['answer[' . $row->id . ']'] = $row->default;
    
        }
        $record['status'] = $status;
        
        $this->assign('record', $record);
        $this->assign('question', $question);
        $this->assign('checked', json_encode($checked));
        $this->assign('title', '问卷调查');
        return $this->fetch();
        
    }
    
    
    public function answer(ExamAnswerDao $dao){
    
        Db::startTrans();
        try{
    
            if(Session::get('answer') == 'done'){
               // $this->error('不能重复提交问卷');
            }
            
            $dao::create($this->post());
            
            Db::commit();
            Session::set('answer', 'done');
            $this->success();
        
        } catch (Exception $e){
        
            Db::rollback();
            $this->error($this->toString($e));
        }
    
    
    }
    
    
    
}
