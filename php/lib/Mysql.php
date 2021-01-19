<?php

/**
** author: zbseoag
** QQ: 617937424
** 封装的mysql查询构造器
** 我的构思是按照 sql语句结构来构建,即: select ... from ... where ... limit
** 其他框架各有各的写法,没有统一规范.
** 封装此类,是因为有时候要脱离框架,可以用用.
** 示例:
$db = new Mysql();
$db->connect($host, $username, $password, $database);
$record = $db->select('*')->from('user')->where('id = :id', [ ':id'=>4 ])->limit();
$record = $db->select('*')->from('user')->where('id = ? AND name=?', [ $id, $name ])->limit();

** 
**/

class Mysql {

    protected $sql = [];
    protected $error = '';
    protected $connect = null;
    protected $sth = null;
    protected static $instance = null;
    
    
    public static function instance($options = []){
        
        if(is_null(self::$instance)) self::$instance = new static($options);
        return self::$instance;
    }
    
    public function __construct($options){
    

    }
    

    public function connect($hostname='', $username='', $password='', $database='', $charset='utf8'){

        if(is_array($hostname)) extract($hostname);

        try{
            $this->connect = new PDO("mysql:dbname=$database;host=$hostname", $username, $password);
        }catch(PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
        $this->connect->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $this->connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->connect->query("SET NAMES $charset;");
        return $this;
    }


    public function beginTransaction(){
        $this->connect->beginTransaction();
    }

    public function commit(){
        $this->connect->commit();
    }

    public function rollBack(){
        $this->connect->rollBack();
    }

    public function query($sql=''){

        $this->sql($sql);
        return $this->connect->query($sql)->fetchAll();
    }

    public function select($field='*'){

        $this->options['select'] = 'SELECT ' . $field;
        return $this;
    }

    public function from($table){

        $this->options['from'] = 'FROM `'. str_replace('.', '`.`', $table) .'`';
        return $this;
    }

    public function where($where, $param=null){

        $this->options['where'] = 'WHERE '. $where;
        if(!empty($param)){
            $this->options['param'] = $param;
        }

        return $this;
    }

    public function order($order){

        $this->options['order'] = 'ORDER BY '. $order;
        return $this;
    }

    public function limit($start=0, $length=null){

        if($length == null){
            $length = $start; $start = 0;
        }

        $this->options['limit'] = ' LIMIT '. $start .','. $length;

        $options = array('select', 'delete', 'from', 'join', 'where', 'order', 'limit');
        $sql = '';
        foreach($options as $keyword){
            if(isset($this->options[$keyword])) $sql .= $this->options[$keyword];
        }

        if(isset($this->options['param'])){

            $this->sth = $this->connect->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $this->sth->execute($this->options['param']);
            $query = $this->sth;

        }else{
            $query = $this->connect->query($sql);
        }
        $this->sql($sql);
        unset($this->options);
        return ($length == 1)? $query->fetch(): $query->fetchAll();

    }


    public function insert($table, $data){

        if(isset($data['id'])) unset($data['id']);

        $fields =  array_keys($data);
        $values = array_values($data);
        $sql = 'INSERT INTO `'. $table .'`(`' .implode('`,`', $fields). '`)VALUES("' .implode('","', $values).'")';
        $this->sql($sql);
        $result = $this->connect->exec($sql);
    }

    public function update($table, $data){

        $sql = 'UPDATE `'. $table .'` SET ';

        if(isset($data['id'])){
            $this->where('id="'.$data['id'].'"');
            unset($data['id']);
        }

        foreach($data as $key => $value){
            $sql .= "`$key`='$value', ";
        }
        $sql = rtrim($sql, ', ') . ' ';
        $sql .= $this->options['where'];
        $this->sql($sql);
        $result = $this->connect->exec($sql);
    }

    public function delete(){
        
        $this->options['delete'] = 'DELETE';
        return $this;
    }

    public function sql($sql=''){

        if(!empty($sql)) $this->sql[] = $sql;
        else return $this->sql;
    }

    public function __destruct(){


    }
    
    /**
     * 修改表结构
     * @param string $table 表名
     * @param string $field 字段
     * @param string $column 类型
     * @param string $value 值
     * @return array 状态与消息
     * 例如:修改user表的uname字段的注释
     * 其他类型可根据 SHOW FULL COLUMNS  FROM $table_name 来理解
     * alter_table('user', 'uname', array('Field'=>'uanme', 'Type'=>'varchar(150)'), 'comment', '这是用户名');
     */
    public function alter($table, $field, $columns, $column, $value){
        
        $column = ucfirst($column);
        $options = array_map('ucfirst',array_keys(current($columns)));
        
        if(!in_array($column, $options))  return '';
        $sql = '';
        
        foreach($columns as $row){
            
            //$columns键名转换首字母大写
            foreach($row as $key => $val){
                $row['new'][ucfirst($key)] = $val;
            }
            $row = $row['new'];
            if($value == 'NULL' || $row['Field'] != $field ) continue;
            $sql = "ALTER TABLE $table";
            if($column == 'Field'){
                $sql .= " CHANGE COLUMN `$field` `$value`";
            }else{
                $sql .= " MODIFY COLUMN `$field`";
                $row[$column] = $value;
            }
            
            $sql .= $row['Type'];
            if($row['Collation']) $sql .= ' COLLATE '. $row['Collation'];
            $sql .= ($row['Null'] == 'YES')? ' NULL' :' NOT NULL';
            if($row['Default']) $sql .= ' DEFAULT '. $row['Default'];
            if($row['Extra']) $sql .= ' ' . strtoupper($row['Extra']);
            if($row['Comment']) $sql .= ' COMMENT ' . $row['Comment'];
            break;
        }
        
        return $sql . ';';
    }

}



