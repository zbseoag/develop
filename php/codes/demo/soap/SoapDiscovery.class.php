<?php
/**
by zbseoag 
*/

class SoapDiscovery {

    private $class_name = '';
    private $service_name = '';


    public function __construct($class_name = '', $service_name = ''){
        
        if (empty($class_name)) {
            throw new Exception('No service name');
        }
        if(empty($service_name)){
            throw new Exception('No class name');
        }
        
        $this->class_name = $class_name;
        $this->service_name = $service_name;
    }


    /**
     * 返回WSDL字符串
     * @throws Exception
     * @return string
     */
    public function getWSDL(){
        
        $headerWSDL = "<?xml version=\"1.0\" ?>\n";
        $headerWSDL.= "<definitions name=\"$this->service_name\" targetNamespace=\"urn:$this->service_name\" xmlns:wsdl=\"http://schemas.xmlsoap.org/wsdl/\" xmlns:soap=\"http://schemas.xmlsoap.org/wsdl/soap/\" xmlns:tns=\"urn:$this->service_name\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:SOAP-ENC=\"http://schemas.xmlsoap.org/soap/encoding/\" xmlns=\"http://schemas.xmlsoap.org/wsdl/\">\n";
        $headerWSDL.= "<types xmlns=\"http://schemas.xmlsoap.org/wsdl/\" />\n";

        $class = new ReflectionClass($this->class_name);

        if (!$class->isInstantiable()) {
            throw new Exception('Class is not instantiable.');
        }
        $methods = $class->getMethods();
        
        $portTypeWSDL = '<portType name="' . $this->service_name . 'Port">';
        $bindingWSDL = '<binding name="' . $this->service_name . 'Binding" type="tns:' . $this->service_name . "Port\">\n<soap:binding style=\"rpc\" transport=\"http://schemas.xmlsoap.org/soap/http\" />\n";
        $serviceWSDL = '<service name="' . $this->service_name . "\">\n<documentation />\n<port name=\"" . $this->service_name . 'Port" binding="tns:' . $this->service_name . "Binding\"><soap:address location=\"http://" . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['PHP_SELF'] . "\" />\n</port>\n</service>\n";
        $messageWSDL = '';
        
        foreach ($methods as $method) {
        
            if ($method->isPublic() && !$method->isConstructor()){
            
                $portTypeWSDL.= '<operation name="' . $method->getName() . "\">\n" . '<input message="tns:' . $method->getName() . "Request\" />\n<output message=\"tns:" . $method->getName() . "Response\" />\n</operation>\n";
                $bindingWSDL.= '<operation name="' . $method->getName() . "\">\n" . '<soap:operation soapAction="urn:' . $this->service_name . '#' . $this->class_name . '#' . $method->getName() . "\" />\n<input><soap:body use=\"encoded\" namespace=\"urn:$this->service_name\" encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\" />\n</input>\n<output>\n<soap:body use=\"encoded\" namespace=\"urn:$this->service_name\" encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\" />\n</output>\n</operation>\n";
                $messageWSDL.= '<message name="' . $method->getName() . "Request\">\n";
                
                $methodInfo = $this->_getType($method);
              
                $parameters = $method->getParameters();
                foreach ($parameters as $parameter) {
                    
                    $messageWSDL.= '<part name="' . $parameter->getName() . '" type="xsd:'.$methodInfo['param']['$'.$parameter->getName()] .'"/>'."\n";
                }
                
                $messageWSDL.= "</message>\n";
                $messageWSDL.= '<message name="' . $method->getName() . "Response\">\n";
                $messageWSDL.= '<part name="' . $method->getName() . '" type="xsd:'.$methodInfo['return'].'"/>'."\n";
                $messageWSDL.= "</message>\n";
            }
        }
        $portTypeWSDL.= "</portType>\n";
        $bindingWSDL.= "</binding>\n";
        
        return sprintf('%s%s%s%s%s%s', $headerWSDL, $portTypeWSDL, $bindingWSDL, $serviceWSDL, $messageWSDL, '</definitions>');

    }
    
   /**
    * 生成WSDL文件
    */
    public function buildWSDL($file = ''){
        
        if(empty($file)) $file = $this->class_name . '.wsdl';
        $content = $this->getWSDL();
        file_put_contents($file, $content);
    }

    
    public function getDiscovery() {
        return "<?xml version=\"1.0\" ?>\n<disco:discovery xmlns:disco=\"http://schemas.xmlsoap.org/disco/\" xmlns:scl=\"http://schemas.xmlsoap.org/disco/scl/\">\n<scl:contractRef ref=\"http://" . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['PHP_SELF'] . "?wsdl\" />\n</disco:discovery>";
    }

    /**
     * 取得方法参数的类型
     * @param unknown $method
     * @return unknown|mixed
     */
    protected function _getType($method){
        
        $comment = $method->getDocComment();
        $comment = trim(preg_replace('/\/\*+|^\s*\**\s*\/*/m', '', $comment));
        $comment = preg_split('/\r*\n/', $comment);
        
        $myarray = array();
        foreach($comment as $line){
            $row = explode(' ', $line, 4);
            if($row[0] == '@param'){
                //array(0=>'@param', 1=>'int', 2=>'$name', 3=>'other word',
                $return['param'][$row[2]] = $row[1];
    
            }elseif($row[0] == '@return'){
                $return['return'] = $row[1];
            }
            
        }
        return $return;
        
    }

}


