<?php
namespace app\admin\controller;

use think\exception;
use think\Db;
use app\common\helper\Form;
use app\common\model\ExamModel;
use app\common\model\ExamQuestionModel;
use app\common\dao\ExamDao;
use app\common\helper\Runtime;


class ExamQuestions extends Controller {
    
    
    public static $header = [
        'title'=>'标题',
        'explain'=>'说明',
        'duration'=>'答题时长',
        'status'=>'状态',
        'startime' => '开始时间',
        'endtime'=>'结束时间',
        'create_time'=>'创建时间',
    ];
    
    
    public function index(ExamModel $model){
    
        $where = Form::get();
        $record = $model->search($where)->paginate(20, false, ['query'=>request()->get()]);
        $paginator = $model->getPaginator($record);
        
        $this->assign('header', self::$header);
        $this->assign('record', $record);
        $this->assign('paginator', $paginator);

        return $this->fetch();
        
    }
    
    
    public function delete(ExamModel $model){
    
        try{
            $id = $this->request->get('id');
            if(!is_array($id)) $id = [$id];
            $model::where('id', 'in', $id)->delete();

        }catch(Exception $e){
            $this->error(Runtime::instance()->error($e)->error());
        }
    
        $this->success();
    }
    
    /**
     * 启用/禁用
     */
    public function switcher(ExamModel $model){
    
        try{
            $id = $this->request->get('id');
            $value = $this->request->get('value');
            if(!is_array($id)) $id = [$id];
            $option = ['on'=>$model::STATUS_USABLE, 'off'=>$model::STATUS_FORBID];
            $model::update(['status'=> $option[$value]], ['id'=>['in', $id]], true);

        }catch(Exception $e){
            $this->error(Runtime::instance()->error($e)->error());
        }
    
        $this->success();
    }
    
    
    public function create(ExamDao $dao){
        
        if($this->request->isGet()){
    
            $this->assign('url', 'create');
            $this->assign('data', 'null');
            return $this->fetch('edit');

        }
        
        Db::startTrans();
        try{
            $dao::create($this->post());
            Db::commit();
     
        
        } catch (Exception $e){
        
            Db::rollback();
            $this->error(Runtime::instance()->error($e)->error());
        }
        
        $this->success();
    }
    
    
    public function edit(ExamModel $model, ExamDao $dao){
        
        if($this->request->isGet()){
    
            $id = $this->request->get('id');
            $record = $model::get($id)->toArray();
            $question = ExamQuestionModel::instance()->byExamId($record['id'])->order('sort')->select();

            foreach($question as $key => $row){
        
                $options = json_decode($row['option'], true);
                $record['question'][] = $row['question'];
                $record['qid'][] = $row['id'];
                $record['option'][] = $options;
                $record['option['.$key.']'] = $options;
                $record['default['.$key.']'] = $row['default'];
            }
    
    
            $this->assign('record', $record);
            $this->assign('data', json_encode($record, JSON_UNESCAPED_UNICODE));
            $this->assign('url', 'edit');
            return $this->fetch();
            
            
        }

        Db::startTrans();
        try{
            $dao::update($this->request->post());
            Db::commit();
        
        } catch (Exception $e){
        
            Db::rollback();
            $this->error(Runtime::instance()->error($e)->error());
        }
    
        $this->success();
    }
    

    
    
    
}
