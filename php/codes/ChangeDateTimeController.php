<?php
/**
 * 把时间改为时间戳
 * User: ZhengBaoshan<zbseoagy@qq.com>
 * Date: 2016-08-17
 * Time: 14:34
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use mongosoft\soapserver\Exception;

class ChangeDateTimeController extends Controller {
    
    private $db = null;
    
    //配置表(可以配置附加表,会追加到find()的结果中) $tables = array('table'=>[field1, filed2]);
    private $tables = [];
        
    private $cachename = 'tables_61bf3e_datetime';

    public function init(){

        $this->db = Yii::$app->getDb();
    }


    /**
     * 遍历所有表
     * 自动匹配数据值为日期型的字段
     * 并保存到缓存中
     */
    public function actionFind($table = ''){
        
        $sql = 'SHOW TABLES';
        $tables = empty($table)? $this->db->createCommand($sql)->queryColumn() : array($table);
        
        foreach($tables as $key => $tbname){
            
            //过滤掉特殊的表
            if(empty($table) && in_array($tbname, array('service_machines', 'service_machine_extends'))) continue;
            
            $count = $this->db->createCommand('SELECT COUNT(*) FROM `'. $tbname . '`')->queryScalar();
            $start = ($count < 10)? 0 : 4;
            try{
                $record = $this->db->createCommand('SELECT * FROM `'.$tbname.'` ORDER BY id DESC LIMIT '.$start.', 1')->queryOne();
            }catch(\Exception $e){
                $this->_echo('#Error => ' .$e->getMessage());
            }
            
            if(empty($record)){ $this->_echo('--> '. $tbname . ' => table is empty'); continue;}
            
            foreach($record as $field => $value){
                
                if($this->_check($value)){
                    $this->tables[$tbname][] = $field; //测试: $this->tables[$tbname][$field] = $value;
                }
                
            }

        }
        
        //存缓存
        Yii::$app->cache->set($this->cachename, $this->tables, 7200);

    }
    
    /**
     * 查看生成的缓存结果
     */
    public function actionShow(){
        $tables = Yii::$app->cache->get($this->cachename);
        print_r($tables);
    }
    
    
    /**
     * 运行把日期型数据改成时间戳
     * @param string $table 表名/all(执行缓存中的)
     * @param string $filed 一个或多个字段名,逗号分隔
     */
    public function actionRun($table = '', $filed = ''){

        if(empty($table)){
            $this->_echo('missing $field argument'); exit;
        }
        
        $tables = [];
        if($table == 'all'){
            $tables = Yii::$app->cache->get($this->cachename);
            if(empty($tables)){ exit('-->cache is empty');}
            
        }else{
            //可以单独修改一张表的字段
            if(empty($filed)){
                
                $count = $this->db->createCommand('SELECT COUNT(*) FROM `'. $table . '`')->queryScalar();
                $start = ($count < 10)? 0 : 4;
                try{
                    $record = $this->db->createCommand('SELECT * FROM `'.$table.'` ORDER BY id DESC LIMIT '.$start.', 1')->queryOne();
                    if(empty($record)){
                        $this->_echo('--> '. $table . ' => table is empty');
                    }
                    
                }catch(\Exception $e){
                    $this->_echo('#Error => ' .$e->getMessage());
                }
                
                foreach($record as $field => $value){
                    if($this->_check($value)) $tables[$table][] = $field;
                }
                
            }else{
                $tables = array( $table => explode(',', $filed));
            }
            
        }

        $this->db->createCommand('SET time_zone = "+0:00"')->execute();
        
        foreach($tables as $tbname => $fields){
            $this->_change($tbname, $fields);
        }
    
    }
    
    /**
     * 查看帮助
     * @param string $action 方法名
     */
    public function actionHelp($action = 0){
        
        $options = array(
            0 =>'Use Flow: find -> show -> run',
            'find' => 'Auto find datetime feild and save to cache.',
            'show' => 'View find() result.',
            'run' => 'Run all table => (run all) or singe one => (run table field1{,field2,...})',
        );
        $this->_echo(isset($action)? $options[$action] : $options[0]);
        
    }

    
    protected function _echo($string){
        echo $string . PHP_EOL;
    }
   
    
    
    /**
     * 正则匹配日期时间
     * @param string $string
     * @return number
     */
    protected function _check($string){
        
        //时间匹配'2016-07-14 21:30:00'
        return preg_match_all('/\d{4}-\d{1,2}-\d{1,2}(?:|\s\d{2}:\d{2}:\d{2})/', $string);
    }
    
    
    /**
     * 修改字段类型与数据值
     * @param string $table
     * @param string $field
     */
    protected function _change($table, $field){
    
        //修改字段为 varchar 型
        $sql = array_map(function($value){ return "MODIFY COLUMN `$value` varchar(20)"; }, $field);
        $sql = "ALTER TABLE `$table` " . rtrim(implode(',', $sql), ',');
        $this->db->createCommand($sql)->execute();
        
        //UPDATE `holidays` SET `date`=IF(`date` REGEXP '^[[:digit:]]{4}-[[:digit:]]{1,2}.*', FLOOR(UNIX_TIMESTAMP(`date`)), `date`) 
        
        //更新表数据
        $sql = array_map(function($value){ return "`$value`=IF(`$value` REGEXP '^[[:digit:]]{4}-[[:digit:]]{1,2}.*', FLOOR(UNIX_TIMESTAMP(`$value`)), `$value`)"; }, $field);

        $sql = "UPDATE `$table` SET ". rtrim(implode(',', $sql), ',')." WHERE {$field[0]} REGEXP '^[[:digit:]]{4}-[[:digit:]]{1,2}.*' LIMIT 100";
    
        try{
            do{
                $rowcount = $this->db->createCommand($sql)->execute();
            }while($rowcount > 0);
            
        }catch(\Exception $e){
            $this->_echo($e->getMessage());
        }
    
        //修改字段为int型
        $sql = array_map(function($value){ return "MODIFY COLUMN `$value` int(11) UNSIGNED NOT NULL DEFAULT '0'"; }, $field);
        $sql = "ALTER TABLE `$table` " . rtrim(implode(',', $sql), ',');
        $this->db->createCommand($sql)->execute();
    
        
    }
    

    
    
    
}