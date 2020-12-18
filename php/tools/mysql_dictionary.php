<?php
/**

生成数据表字典工具

**/


$host = 'localhost';
$username = 'root';
$password = 'root';
$dbname = 'test';

$db = new Mysql();
$db->connect($host, $username, $password, $dbname);
$tables = empty($show)? $db->query('SHOW TABLES') : $show;


foreach($tables as $key => $row){
    
    $table = is_string($row)? $row : $row['Tables_in_'. $dbname];
    
    if(!empty($filter) && !in_array($table, $filter)) continue;
    if(preg_match('/.*_back/', $table)) continue;
    
    //表详情
    $tables['new'][$key]['info'] = $db->select('*')->from('INFORMATION_SCHEMA.TABLES')->where('table_name = ? AND table_schema=?', [$table, $dbname])->limit(1);
  
    //表字段详情
    $tables['new'][$key]['columns'] = $db->select('*')->from('INFORMATION_SCHEMA.COLUMNS')->where('table_name = ? AND table_schema=?', [$table, $dbname])->limit();
    
    //表数据
    $tables['new'][$key]['record'] = $db->select('*')->from($table)->order('RAND()')->limit(0, 2);


}
$tables = $tables['new'];
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>数据库数据字典</title>
<style>
body{ font-family: "微软雅黑"; font-size: 14px; }
.warp h3{margin:0px; padding:0px; line-height:30px; margin-top:10px;}
table { border-collapse: collapse; border: 1px solid #CCC; background: #efefef; }
table th { font-weight: bold; line-height: 30px; font-size: 14px; border: 1px solid #CCC; text-align:left;  padding:0 4px;}
table td { height: 20px; font-size: 14px; border: 1px solid #CCC; background-color: #fff; padding:4px;}
td, th{white-space: nowrap;  word-break: keep-all;}

</style>
</head>
<body>
<h2 style="text-align:center;"><?php echo $dbname;?> 数据字典</h2>


<?php foreach($tables as $key => $row){ ?>
<h3><?php echo ($key+1) . '、' . $row['info']['TABLE_NAME'] .(empty($row['info']['TABLE_COMMENT'])? '' : " ( {$row['info']['TABLE_COMMENT']} )") ?></h3>
<table border="1" cellspacing="0" cellpadding="0" width="100%">
<tbody>
<tr>
    <th>字段名</th>
    <th>数据类型</th>
    <th>默认值</th>
    <th>允许为空</th>
    <th>备注</th>
    <th style="background:#FFDAC8;">记录1</th>
    <th style="background:#FFDAC8;">记录2</th>
</tr>
     

    <?php foreach($row['columns'] as $column){ ?>
    <tr>
    <td><?php echo $column['COLUMN_NAME']; ?></td>
    <td><?php echo $column['COLUMN_TYPE']; ?></td>
    <td><?php echo $column['EXTRA']=='auto_increment'? '自动递增': $column['COLUMN_DEFAULT']; ?> </td>
    <td><?php echo $column['IS_NULLABLE']; ?></td>
    <td><?php echo $column['COLUMN_COMMENT']; ?></td>
    
    <td><?php echo empty($row['record'][0])? ' ' : $row['record'][0][$column['COLUMN_NAME']]; ?></td>
    <td><?php echo empty($row['record'][1])? ' ' : $row['record'][1][$column['COLUMN_NAME']]; ?></td>
    </tr>
    <?php }?>

</tbody>
</table>
<?php }?>


</body>
</html>

<?php

class Mysql {
    
    protected $sql = [];
    protected $error = '';
    protected $connect = null;
    protected $sth = null;

    public function connect($host, $user='', $password='', $dbname, $charset='utf8'){

        try{
            $this->connect = new PDO("mysql:dbname=$dbname;host=$host", $user, $password);
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
        
        $this->options['from'] = ' FROM `'. str_replace('.', '`.`', $table) .'`';
        return $this;
    }
    
    public function where($where, $param=null){

        $this->options['where'] = ' WHERE '. $where;
        if(!empty($param)){
            $this->options['param'] = $param;
        }
        
        return $this;
    }
    
    public function order($order){
    
        $this->options['order'] = ' ORDER BY '. $order;
        return $this;
    }
    
    public function limit($start=0, $length=null){
        
        if($length == null){
           if($start > 0){
               $length = $start; $start = 0;
           }
           
        }
        
        if($length > 0)     
        $this->options['limit'] = ' LIMIT '. $start .','. $length;
        
        $options = array('select', 'from', 'join', 'where', 'order', 'limit');
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
    
    
    public function sql($sql=''){
        
        if(!empty($sql)) $this->sql[] = $sql;
        else return $this->sql;
    }
    
    public function __destruct(){
        

    }
    
}



?>