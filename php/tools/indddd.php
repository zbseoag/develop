<?php


class Tool{

    public $command = array();
    public $data = '';

    public $output =  null;

    public function __construct(){

        $this->data = trim($_POST['data']);

        $command = $this->title = trim($_POST['command']);

        if(!empty($command)){

            preg_match_all('/[^\s"\']+|"([^"]*)"|\'([^\']*)\'/',  $command, $command);
            $command = $command[0];

            $this->command['cmd'] = array_shift($command);

            foreach($command as $key => $value){

                if($value[0] == '-'){

                    $next = $key+1;
                    $key = $value;
                    if($command[$next][0] == '-'){
                        $next--;
                        $this->command[$key] = false;
                    }else{
                        $this->command[$key] = $command[$next];
                    }

                }else if(!isset($next) || $key != $next){
                    $this->command[$key] = $value;
                }

                //去掉最外层的引号
                if(isset($this->command[$key]) && in_array($this->command[$key][0], array('"', "'"))) $this->command[$key] = substr($this->command[$key], 1, -1);

            }

        }
    }



    /**
     * 运行
     */
    public function execute(){


        $options = array(
            'array' => array('to_array', '数组', '-d:分割符  -k:设置为索引的列  -v:设置为值的列  -l:分割成几个元素  -rm:过滤字符  -w:几维数组'),
            'api-array' => array('api_array', '接口数据一维数组', ''),
            'string' => array('to_string', '转换字符串', '-v:可以'),
            'sql-field'  => array('sql_field', 'SQL建表语句分离出字段', '-v:可以'),
            'sql-insert' => array('sql_insert', 'SQL插入语句', '-v:可以'),
            'sql-where' => array('sql_where', '查询条件字符串', '-v:可以'),
            'sql-backup' => array('sql_backup', '备份表语句', '-v:可以'),
            'tag-array' => array('tag_array', '&lt;tag&gt;转数组', '')
        );

        if($this->command){

            $this->{$options[$this->command['cmd']][0]}();
        }else if(!empty($this->data)){
            //运行代码
            echo '<pre>';eval($this->data . ';');echo '</pre>';
        }else{

            $this->output = '';
            foreach($options as $key => $row){
                $this->output .= "<tr><td  style='border:1px solid #ddd;width:150px;'>$key</td><td style='border:1px solid #ddd;' >{$row[1]}</td><td style='border:1px solid #ddd;'>{$row[2]}</td></tr>";
            }

            echo '<table style="border-collapse: collapse;width:100%;">' . $this->output . '</table>';
            $this->output = null;
        }

    }


    /**
     * 二维数组根据条件转一维
     * @param $data
     */
    public function arrayFluctuate(){

        $this->title = explode(':', str_replace(' ', '', $this->title));


        if(count($this->title) == 2){

            $_temp = explode('//', $this->title[1]);

            $this->title[1] = $_temp[0];
            if(count($_temp) == 2)  $this->title[2] = $_temp[1];

        }else{

            $_temp = explode('//', $this->title[0]);
            $this->title[0] = $_temp[0];
            if(count($_temp) == 2)  $this->title[2] = $_temp[1];
        }

        eval('$data='.$this->data.';');
        if(is_array(current($data))){

            $this->output = 'array(<br/>';
            foreach($data as $row){


                if(isset($this->title[0]) && !isset($row[$this->title[0]])) $row[$this->title[0]] = '';
                if(isset($this->title[1]) && !isset($row[$this->title[1]])) $row[$this->title[1]] = '';
                if(isset($this->title[2]) && !isset($row[$this->title[2]])) $row[$this->title[2]] = '';

                if(count($this->title) == 1){

                    $this->output .= "'{$row[$this->title[0]]}',\n";

                }else if(count($this->title) == 2){

                    if(isset($this->title[1])){
                        $this->output .= " '{$row[$this->title[0]]}' => '{$row[$this->title[1]]}',<br/>";
                    }else{
                        $this->output .= " '{$row[$this->title[0]]}',//{$row[$this->title[2]]}<br/>";
                    }

                }else{
                    $this->output .= " '{$row[$this->title[0]]}' => '{$row[$this->title[1]]}', //{$row[$this->title[2]]}<br/>";
                }

            }

            $this->output .= ');';

        }


    }





    /**
     * 字符行转数组
     * -d 分割符
     * -k 设置为索引的列
     * -v 设置为值的列
     * -l 分割成几个元素
     * -rm 过滤字符
     * -w 几维数组
     */
    public function to_array(){

        $this->command += array('-d'=> ' ', '-k' => null, '-l' => 100, '-v' => null, '-rm'=>'', '-w' => 2);

        $this->data = str_replace(str_split($this->command['-rm']), '', $this->data);//过滤一些字符
        $this->data = explode("\r\n", trim($this->data));//按换行转数组
        $this->data = array_filter($this->data);//过滤空行

        $this->output = array();
        foreach($this->data as $key => $value){

            $row = explode($this->command['-d'], preg_replace('/\s+/', ' ', trim($value)), $this->command['-l']);//行数据分割成数组

            $value = ($this->command['-v'] === null)? $row : (isset($row[$this->command['-v']])? $row[$this->command['-v']] : $this->command['-v']);//如果指定了设置为值的列

            //如果设置了索引列
            if($this->command['-k'] === null){
                $this->output[] = $value;
            }else{
                $this->output[$row[$this->command['-k']]] = $value;
            }

        }

        //转一维数组
        if($this->command['-w'] == 1){

            $output = array();
            foreach($this->output as $key => $value){
                if(is_array($value)) $output = array_merge($output, $value);
                else $output[] = $value;
            }
            $this->output = $output;
        }

    }

    /**
     * 转接口数据格式
     * -d 分割符
     * -k 设置为索引的列
     * -v 设置为值的列
     * -l 二维分割成几个元素
     * -rm 过滤字符
     *
     */
    public function api_array(){

        $this->command('to_array', $this->command);

        $data = '';
        foreach($this->output as $key => $row){
            $data .= "  '$key' => null, //" .implode(' ',$row). '<br/>';
        }

        $this->output = 'array(<br/>' . $data .');';

    }


    /**
     * where 条件
     * -d 分割符
     * -k 设置为索引的列
     * -v 设置为值的列
     * -l 二维分割成几个元素
     * -rm 过滤字符
     * -w 几维数组
     *
     * -link
     * -ad
     */
    public function sql_where(){

        $this->command += array('-link'=>'IN',  '-ad'=>'', '-w'=>1);
        $this->command('to_array', $this->command);

        $link = strtoupper($this->command['-link']);
        $this->output =  " {$link}('" . implode("{$this->command['-ad']}','",  $this->output). "{$this->command['-ad']}');\n";

    }


    /**
     * 数据表备份
     * 0 源表名
     * 2 备份表名
     */
    public function sql_backup(){

        $this->command += array( 0=>'test', 1=>'');

        if(empty($this->command[1])) $this->command[1] = $this->command[0] . '_' . date('Ymd');

        $this->output = "ALTER TABLE `{$this->command[0]}` RENAME `{$this->command[1]}`;<br/>CREATE TABLE `{$this->command[0]}` LIKE `{$this->command[1]}`;";

    }






    /**
     *
     * -glue
     */
    public function to_string(){

        $this->command += array('-glue'=>',', '-w'=>1);
        $this->command('to_array', $this->command);

        $this->output = implode($this->command['-glue'], $this->output);

        if($this->command['-glue'] == "','") $this->output = "'" . $this->output . "'";
        if($this->command['-glue'] == '","') $this->output = '"' . $this->output . '"';

    }


    public function http_build_query(){

        if(substr($this->data, 0, 5) === 'array'){
            eval('$data='.$this->data.';');
            $this->output = http_build_query($data);
        }else{
            parse_str($this->data, $this->output);
        }

    }


    public function select_array(){

        $this->data = trim($this->data);
        $this->data = preg_replace(array('/^\$\w+\s+=\s+\'*/', '/\';$/'), array('', ''), $this->data);

        if(substr($this->data, 0, 5) === 'array'){

            eval('$data='.$this->data.';');
            $html = '';
            foreach($data as $key => $value){
                $html .= '<option value="'.$key.'">'.$value.'</option>' . "\n";
            }

            $this->output = '<select class="form-control" name="">' ."\n" . $html .'</select>';
            $this->output = htmlentities($this->output, ENT_QUOTES, "UTF-8");
        }else{
            $regex = '/<option\s+value="(.*)?".*?>(.*)?<\/option>/';
            preg_match_all($regex, $this->data, $matches);
            $this->output = array_combine($matches[1], $matches[2]);

        }

    }





    /**
     *
     */
    public function file_copy(){

        $newfile = $this->data . '/' . basename($this->title);
        if(!is_dir($this->data)) mkdir($this->data, '0777', true);
        copy($this->title, $newfile);
        $this->output = realpath($newfile);

    }


    /**
     *
     */
    public function sql_insert(){

        $this->command('to_array');

        $fields = array_slice($this->command, 2);
        foreach($fields as &$value){
            //array(0=>field, 1=>value);
            if(stripos($value, ':=')){

                $value = explode(':=', $value, 2);
                $temp = explode('|', $value[1], 2);

                //array(0=>field, 1=>value, 'value'=>'定值');
                $value = array($value[0], $temp[0], 'value' => $temp[1]);

            }else if(stripos($value, ':')){

                $value = explode(':', $value, 2);
            }else if(stripos($value, '=')){
                $value = explode('=', $value, 2);
                $value = array($value[0], 'null', 'value' => $value[1]);
            }
        }
        unset($value);

        $this->command['fields'] = '`' .implode('`,`',array_column($fields, 0)) . '`';
        $output = '';
        foreach($this->output as $key => $row){

            $values = array();
            foreach($fields as $field){

                $values[] = isset($row[$field[1]])? $row[$field[1]] : (isset($field['value'])? $field['value'] : '');
            }
            $values = "'" .implode("','", $values) . "'";

            $output .= "INSERT INTO `{$this->command[0]}` ({$this->command['fields']}) VALUES ($values);<br/>";
        }

        $this->output = $output;

    }





    public function json_array(){

        $this->data = trim($this->data);

        $this->data = preg_replace(array('/^\$\w+\s+=\s+\'*/', '/\';$/'), array('', ''), $this->data);

        if(substr($this->data, 0, 5) === 'Array'){
            $pattern = array('/Array\s+\(/', '/\[(\w+)\]\s=>\s/', '/=>#~#(.*)/', '/[\r\n]\',/', '/\'NULL\'/i', "/'array\(',/", '/([\r\n]+\s+)\)/');
            $replacement = array('array(', "'$1'=>#~#", " => '$1',", "',", 'null', 'array(', "$1),");
            $this->data =  preg_replace($pattern, $replacement, $this->data);
            $this->output = $this->data . ';';
            return;
        }


        if(substr($this->data, 0, 5) === 'array'){

            eval('$data='.$this->data.';');
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        }else if(substr($this->data, 0, 1) === '{' || substr($this->data, 0, 1) === '['){

            $data = json_decode($this->data, true);
            for($i = 1; $i < 3; $i++){
                if(!empty($data)) break;

                $this->data = stripslashes($this->data);
                $data = json_decode($this->data, true);
            }

        }else{
            parse_str($this->data, $this->output);
            return;
        }

        $this->output = var_export($data, true) . ';';

    }


    public function maketplfile(){

        $file = empty($this->title)? './new.php' : $this->title;
        $class = basename($file, ".php");
        $content = str_replace(array('{CLASS}'), array($class), $this->data);
        file_put_contents($file, $content);

        $this->output = $file.' 下载完成';

    }


    public function toArray(){

        $this->data = explode("\n", str_replace("\r", '', $this->data));
    }

    public function download(){

        $this->toArray();

        foreach($this->data as $row){

            $name = basename($row);
            file_put_contents('/home/zbseoag/Downloads/'.$name, file_get_contents($row));
            $this->output[] =  $name;
        }

        $this->output[] = ' 下载完成';

    }


//已经重写的方法
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    public function strip_tags(){
        $this->output = strip_tags($this->data, '<br><h1><h2><h3><h4><div><pre><p>');
    }


    public function htmlFormat(){

        $pattern = '/\<\/\w+?\>/';
        $replacement = '$1<br/>';
        $this->output =  preg_replace($pattern, $replacement, $this->data);

    }




    public function ascii(){

        $string = $this->data;
        if(preg_match_all('/^\d+,?/', $string)){
            $return = '';
            $string = explode(',', $string);
            foreach($string  as $key => $value){
                $return .= chr($value);
            }
        }else{
            $length = strlen($string);
            for($i = 0; $i < $length; $i++){
                $return[] = ord($string[$i]);
            }
            $return = implode(',', $return);
        }

        $this->output =  $return;
    }




    public function notExist(){

        $this->data = preg_replace('/\s{3,}/', "#@@@#", trim($this->data));
        $this->data = explode('#@@@#', trim($this->data));

        $soruce = explode("\r\n", trim($this->data[0]));
        $this->data = explode("\r\n", trim($this->data[1]));
        foreach($soruce as $value){
            if(in_array($value, $this->data))  $this->output['in'][] = $value;
            else   $this->output['not'][] = $value;
        }

        self::write($this->output);

    }


    //rsa转64位一行
    public function rsaLength64(){

        $data = array_merge(array_filter(explode("\r\n", $this->data)));

        $this->output = '-----BEGIN RSA PRIVATE KEY-----' . "\r\n";
        $this->output .=  implode("\r\n",str_split(trim($data[0]), 64)) . "\r\n";
        $this->output .= '-----END RSA PRIVATE KEY-----';
        $this->output .= '<br/><br/><br/>';
        $this->output .= '-----BEGIN PUBLIC KEY-----' . "\r\n";
        $this->output .=  implode("\r\n",str_split(trim($data[1]), 64)) . "\r\n";
        $this->output .= '-----END PUBLIC KEY-----';

    }


    //命名风格
    public function nameStyle(){

        $this->output = '';
        $this->data = explode("\n", trim($this->data));
        foreach($this->data as $line){
            $this->output .= str_replace(' ', '', ucwords(str_replace('_', ' ', trim($line)))) .'<br/>';
        }

    }



    //下载github上的文件到桌面
    public function gitDownloadFile(){

        $url = parse_url($this->title);
        $path = explode('/', trim($url['path'], '/'));
        unset($path[2]);
        array_unshift($path, $url['scheme'] . ':', '/raw.githubusercontent.com');
        $url = implode('/', $path);
        $file = end($path);
        file_put_contents($_ENV['desktop'] . '/'. $file , file_get_contents($url));

        $this->output = $file.' 下载完成';

    }




    public function createSetAndGetmethod(){

        if(strpos($this->data, '<table') === 0){
            $methods = $this->tableToArray(true)['td'];
        }else{
            eval('$methods='.$this->data.';');
        }


        $html = '';

        foreach($methods as $row){

            $html .= 'protected $'.$row[0].'; //'.$row[1].'<br/>';
        }
        $html .= '<br/><br/>';


        foreach($methods as $row){

            $name = str_replace(' ', '', ucwords(str_replace('_', ' ', trim($row[0]))));
            $html .= '/**<br/>*'.$row[1].'<br/>*/<br/>public function set' . $name . '($value){</br><br/>$this->'.$row[0]. ' = $value;<br/>return $this;<br/><br/>}<br/></br>';
        }

        foreach($methods as $row){

            $name = str_replace(' ', '', ucwords(str_replace('_', ' ', trim($row[0]))));

            $html .= '/**<br/>*'.$row[1].'<br/>*/<br/>public function get'. $name  .'(){<br/><br/>return $this->'.$row[0].';<br/><br/>}<br/></br>';
        }

        $this->output = $html;

    }



    public function templateTable(){

        eval('$data='.$this->data.';');

        $th = $td = '';
        foreach($this->data as $filed => $text){

            $th .= htmlentities('<th>{$thead.'. $filed .'}</th>') . '<br/>';
            $td .= htmlentities('<td>{$row.'. $filed .'}</td>'). '<br/>';
        }

        $this->output = $th . '<br/><hr/>' . $td;

    }


    public function tabToTable(){

        $this->data = str_replace("\r", '', $this->data);
        $this->data = explode("\n", $this->data);

        $table = "<br/><table>\n";

        foreach($this->data as $key => $row){

            //表头
            $td = ($key == 0)? 'th' :'td';

            $table .= '<tr><'.$td.'>' . str_replace("\t", '</'.$td.'><'.$td.'>', $row) . '</'.$td.'></tr>' . "\n";

            $this->output .= '| ' . str_replace("\t", ' | ', $row) . " |\n";

            //表头
            if($key == 0) $this->output .= str_repeat('| --- ', substr_count($row, "\t") + 1) . " |\n";

        }

        $table .= "\n</table><br/>";

        echo $table;

    }


}








class Tool3333{
    



    public function download(){

        $this->data = explode("\n", str_replace("\r", '', $this->data));
        if(empty($this->date)) return $this->output = '没有下载地址';
        foreach($this->data as $row){
            $name = basename($row);
            file_put_contents('/home/zbseoag/Downloads/'.$name, file_get_contents($row));
            $this->output[] =  $name;
        }

        $this->output[] = ' 下载完成';

    }



}


?>







