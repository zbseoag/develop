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
$record = $db->select('*')->from('user')->where('id = :id', [ ':id'=>4 ])->join('inner tabelB', 'uid = b.id')->limit();
$record = $db->select('*')->from('user')->where('id = ? AND name=?', [ $id, $name ])->limit();

**/

$db = Mysql::new('127.0.0.1', 'admin', 'root', '123456');

$field = [
    'a' => 'a1,a2,a3',
    'b' => 'b1,b2,b3'
];


//echo $db->select($field)->select('c', 'c1,c2')->from('tableA')->join("inner", " bbbb b ", "b=b" )->limit(1, false);


echo $db->where('name', '>', 39)->where([   [['age', '>', 20], ['sss', 'in', '223']],    ['xxx', '<=', '223'] ])->where();

class Mysql extends PDO {

    protected $sql = [];
    protected $error = '';

    protected static $init = null;
    public $default = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    public $options = [];

    public function __construct($host, $database, $username = null, $password = null, $charset = 'utf8', $options = null){

        if(is_array($host)) extract($host);
        parent::__construct("mysql:dbname=$database;host=$host", $username, $password, $options ?? [] + $this->default);
        $this->query("SET NAMES $charset;");

    }

    public static function new(...$argument){
        
        if(is_null(self::$init)) self::$init = new static(...$argument);
        return self::$init;
    }

    public function select($alias = '*', $field = null){

        if(func_num_args() == 0){

            $fields = '';
            foreach($this->options[__FUNCTION__] as $key => $value){
                $value = explode(',', trim($value, ','));
                if(is_numeric($key)){
                    $fields .= implode("`, `", $value) .'`, ';
                }else{
                    $fields .= "`$key`.".implode("`, `$key`.`", $value) .'`, ';
                }

            }
            return trim($fields, ', ');

        }else{

            $field = func_num_args() == 2? [ $alias => $field ] : $alias;
            if(is_array($field)){
                foreach($field as $key => $value){
                    $this->options['select'][$key] = $value;
                }
            }else{
                $this->options['select'][] = $field;
            }
            return $this;

        }

    }

    public function from($table){

        $this->options['from'] = 'FROM `'. str_replace([' ', '.'], ['` `', '`.`'], trim($table)) .'`';
        return $this;
    }

    public function join($type, $table, $on){

        $this->options['join'][] = sprintf('%s JOIN %s ON %s', strtoupper($type), $table, $on);
        return $this;
    }

    public function group(){

    }

    public function where($field = null, $type = null, $value = null, $link = 'AND'){

        if(func_num_args() == 0){

            p($this->options[__FUNCTION__]);
            $where = 'WHERE';
            foreach($this->options[__FUNCTION__] as $key => $value){

                if(is_array($value)){

                    if(is_array(current($value))){
                        $group = '';
                        foreach($value as $item){
                            if(is_array($item)){
                                if(is_string($item[2])) $item[2] = "'$item[2]'";
                                $group .= sprintf(' %s %s %s ', $item[0], $item[1], $item[2]);
                            }else{
                                $group .= " $item ";
                            }
                        }
                        $where .= " ($group) ";
                    }else{
                        $value[2] = "'$value[2]'";
                        $where .= sprintf(' %s %s %s ', $value[0], $value[1], $value[2]);
                    }

                }else{
                    $where .= $value;
                }

            }
            return $where;

        }else{

            if(is_array($field)){
                foreach($field as $key => $value){

                    if(is_array(current($value))){

                        foreach($value as $key2 => $item){
                            $w[] = $item;
                            if(isset($value[$key2 + 1]) && !is_string($value[$key2 + 1])) $w[] = 'AND';
                        }

                        $this->options['where'][] = $w;

                    }else{

                        $this->options['where'][] = $value;
                        if(isset($field[$key + 1]) && !is_string($field[$key + 1])) $this->options['where'][] = 'AND';
                    }

                }
            }else{

                if(func_num_args() == 2) list($type, $value) = ['=', $type];
                $this->options['where'][] = [ $field, $type, $value ];
                $this->options['where'][] = $link;
            }

            return $this;
        }


    }

    public function order($order){

        $this->options['order'] = 'ORDER BY '. $order;
        return $this;
    }

    public function limit($offset = 0, $length = 1, $exec = true){

        if($offset === false){
            $exec = false;
        }else if($length === false){
            $exec = false;
            $this->options['limit'] = sprintf('LIMIT %s', $offset);
        }else{
            $this->options['limit'] = sprintf('LIMIT %s, %s', $offset, $length);
        }

        stop($this->options);
        $sql = '';

        $keyword = array('select', 'delete', 'from', 'join', 'where', 'order', 'limit');
        foreach($keyword as $method){

            if(isset($this->options[$method])){
                if(is_array($this->options[$method])) $sql .= $this->$method();
            }else{
                $sql .= $this->options[$method];
            }
        }

        unset($this->options);

        if(!$exec) return $sql;

        $statement = $this->query($sql);
        return ($length == 1)? $statement->fetch(): $statement->fetchAll();
        
//        if(isset($this->options['param'])){
//
//            $this->sth = $this->connect->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
//            $this->sth->execute($this->options['param']);
//            $query = $this->sth;
//
//        }else{
//            $query = $this->query($sql);
//        }



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



