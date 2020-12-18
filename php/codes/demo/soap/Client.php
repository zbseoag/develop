<?php

class SoapClientNew extends SoapClient {  

    public function __doRequest($request, $location, $action, $version, $one_way = 0){  
    
    
        $request = parent::__doRequest($request, $location, $action, $version, $one_way);  
        print_r($request);
        
        $start = strpos($request, '<soap');//根据实际情况做处理。如果是<?xml开头，改成<?xml
        $end = strrpos($request, '>');  
        return substr($request, $start, $end-$start+1);  
    }  
}



//关闭wsdl缓存 
ini_set('soap.wsdl_cache_enabled', "0"); 
   
$soap = new SoapClient('http://localhost/soap/Service.php?wsdl');

echo $soap->tool(12, 13);

