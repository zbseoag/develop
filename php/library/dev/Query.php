<?php


class Query {

    protected static $instance = null;
    
    protected static $sql = array();
    
    protected function __construct(){}

    public static function instance(){
        
        if (is_null(self::$instance)) {
            self::$instance = new Query();
        }
        return self::$instance;
    }

    
    public function select($field = '*'){

        $this->options['select'] = 'SELECT ' . $field;
        return $this;
    }
    
    public function count($field = '*'){
        
        return $this->select("COUNT($field)");
    }

    public function from($table, $alias = ''){

        $this->options['from'] = ' FROM `'. str_replace('.', '`.`', $table) .'`' . ' ' . $alias;
        return $this;
    }

    public function where($where = null){

        if(is_numeric($where)) $where = array('id'=>$where);
        if(!empty($where)) $this->options['where'] = $where;
        return $this;
    }

    public function order($order){

        $this->options['order'] = ' ORDER BY '. $order;
        return $this;
    }
    
    public function join($join, $on, $type = 'LEFT'){
        
        $this->options['join'][] = strtoupper($type) . ' JOIN ' . $join . ' ON ' . $on;
        return $this;
    }
    
    public function group($group){
        $this->options['group'] = ' GROUP BY '. $group;
        return $this;
    }

    public function limit($start = 0, $length = 0){

        if($length == 0 && $start > 0){
           $length = $start;  $start = 0;
        }

        if($length > 0) $this->options['limit'] = ' LIMIT '. $start .','. $length;

            $options = array('select','insert','update', 'delete', 'from', 'data', 'join', 'where', 'group', 'order', 'limit');
            $sql = '';
            
            if(isset($this->options['from']) && !isset($this->options['select'])) $this->select('*');
            
            foreach($options as $keyword){
                
                if(!isset($this->options[$keyword])) continue;

                if($keyword == 'where'){

                    if(is_array($this->options['where'])){
                        
                        foreach($this->options['where'] as $field => $value){
                        
                            $link = '=';
                            $tpl = " @link '@value'";
                            if(is_array($value)){
                                
                                if(is_string($value[0])){
                                    
                                    $link = $value[0]; $value = $value[1];
                                    $link = strtoupper($link);
                                    
                                    if(preg_match('/LIKE/', $link)){
                                        $value = str_replace('LIKE', $value, $link);
                                        $link = str_replace('%', '', $link);
                                    }else if($link == 'IN'){
                                        if(is_array($value)){
                                            $value =  rtrim(implode("','", $value), ',');
                                            $tpl = " @link('@value')";
                                        }
                                    }
                                    
                                }else{
                                    
                                    foreach($value as $item){
                                        $where[] = '`'.str_replace('.', '`.`', $field).'`'. str_replace(array('@link','@value'), array($item[0], $item[1]), $tpl);
                                    }
                                    continue;
                                }

                            }
                       
                            $where[] = '`'.str_replace('.', '`.`', $field).'`'. str_replace(array('@link','@value'), array($link, $value), $tpl);
                        
                        }
                        
                        $this->options['where'] = implode(' AND ', $where);
                    }
                    
                    $this->options['where'] = ' WHERE ' . $this->options['where'];
                    
                    
                }
            
                if($keyword == 'join'){
                    $this->options['join'] = ' ' . implode(' ', $this->options['join']) . ' ';
                }
                
                $sql .= $this->options[$keyword];
            }

            $sql .= ';';
            
            if((isset($this->options['update']) || isset($this->options['delete'])) && !preg_match('/\s+WHERE\s+/', $sql)){
                self::$sql[] = array('msg'=>'更新或删除,必须要带 WHERE 条件', 'sql'=>$sql);
                return false;
            }

            $method = isset($this->options['delete'])? 'exec' : 'query';
            
            //如果有初始化的sql键名，表明调用过page方法
            if(isset($this->page['sql'])){
                $this->page['sql'] = preg_replace(array('/SELECT\s+.*\s+FROM/', '/LIMIT\s+.*/ '), array('SELECT COUNT(*) FROM', 'LIMIT 1'), $sql);
            }
            
            unset($this->options);

            $this->limit = $length;
            return $this->$method($sql);

    }

    

    public function delete($table){
        
        $this->options['delete'] = "DELETE FROM `$table`";
        return $this;
    }

    /**
     * page分页
     * 保留page参数值到数组
     * 初始化page数据里的sql
     * @param unknown $page
     * @param number $size
     * @return unknown
     */
    public function page($page, $size = 20){
        
        $this->page['page'] = $page;
        $this->page['size'] = $size;
        $this->page['sql'] = '';
        return $this->limit(($page - 1) * $size, $size);
    }
    
    
    
    
    protected function query($sql){
        
        self::$sql[] = $sql;
        $recrod = class_exists('L')? L::sql($sql) : $sql;
        if(empty($recrod)) $recrod = array();
        if($this->limit == 1 && is_array($recrod)){

            if(empty($recrod)){
                $recrod = '';
            }else{
                $recrod = current($recrod);
                if(count($recrod) == 1) $recrod = current($recrod);
            }

        }
        
        if(isset($this->page['sql']) && !empty($this->page['sql'])){
            
            self::$sql[] = $this->page['sql'];
            if(class_exists('L')){
                $this->page['count'] = L::sql($this->page['sql']);
                $this->page['count'] = current($this->page['count'][0]);
                //使用完，清除sql
                unset($this->page['sql']);
            }
        }
        return $recrod;
        
    }
    
    protected function exec($sql){
    
        return $this->query($sql);
    }
    
    public static function sql($return = false){
        
        if($return) return self::$sql;
        return class_exists('debug')? debug::p(self::$sql) : self::$sql;
    }
    
    public function paginator($page = 10){

        if(is_numeric($page)) $page = array('barsize' => $page);
        $page += $this->page;

        $size = $page['size'];
        $count = $page['count'];
        $barsize = $page['barsize'];
        $page = $page['page'];

        $pageMax = ceil($count / $size);

        $link = '';
        
        $start = 1;
        if($page > $barsize){
            $mod = $page % $barsize;
            $start = ($mod == 0)? $page - $barsize : $page - $mod;
            $start++;
        }
       
        $start = min($start, $pageMax);
        $end = min($pageMax,  $start + $barsize - 1);

        if($page - 1 > 0){
            $_GET['page'] = $page - 1;
            $url = http_build_query($_GET);
            $link .= '<li><a href="?'.$url.'">«</a></li>';
        }else{
            $link .= '<li class="disabled"><span>«</span></li>';
        }

        for($i = $start; $i <= $end; $i++){
            
            if($page == $i){
                $link .= '<li class="active"><span>'. $i . '</span></li>'; continue;
            }
            $_GET['page'] = $i;
            $url = http_build_query($_GET);
            $link .= '<li><a href="?'.$url.'">'. $i . '</a></li>';
        }
       
        if($page + 1 < $pageMax){
            $_GET['page'] = $page + 1;
            $url = http_build_query($_GET);
            $link .= '<li><a href="?'.$url.'">»</a></li>';
        }else{
            $link .= '<li class="disabled"><span>»</span></li>';
        }
        
        $page = (object) array(
            'info' => "当前{$page} / {$pageMax}页, {$count}共条记录",
            'page' => '<nav><ul class="pagination">'. $link . '</ul></nav>',
        
        );
        
        return $page;
    }
    
    

    public function insert($table){
        
        $this->table = $table;
        $this->options['insert'] = "INSERT INTO `$table`";
        return $this;

    }
    
    public function data($data){
        
        if(isset($this->options['insert'])){
            
            if(!is_array(current($data))) $data = array($data);
  
            $fields = current($data);
            if(isset($fields['id'])) unset($fields['id']);
            $this->options['data'] = '(`' .implode('`,`', array_keys($fields)). '`)VALUES';
            foreach($data as $key => $row){
                if(isset($row['id'])) unset($row['id']);
                $values = array_values($row);
                $this->options['data'] .=  '("' .implode('","', $values).'"),';
            }
            $this->options['data'] = rtrim($this->options['data'], ',');
            
        }elseif(isset($this->options['update'])){

            if(isset($data['id'])){
                $this->where('`id`="'.$data['id'].'"'); unset($data['id']);
            }
            
            $sql = '';
            foreach($data as $key => $value){
                $sql .= "`$key`='$value', ";
            }
            $this->options['data'] = rtrim($sql, ', ') . ' ';
        }
        
        return $this;
        
    }
    
    
    public function update($table){
    
        $this->options['update'] = "UPDATE `$table` SET ";
        return $this;

    }
    

    
    
}



