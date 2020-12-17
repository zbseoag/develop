<?php

/**
 *  * author: zbseoag
 * QQ: 617937424

    $rsa = new Openssl();
    $rsa->setPrivateKey($private_key)->setPublicKey($public_key);
    echo $encrypt = $rsa->encrypt('我是张三');
    echo $rsa->decrypt($encrypt);
 *
 */

class Openssl {

    private $privateKey = null;
    private $publicKey = null;

    public function __construct($publicKey = '', $privateKey = ''){

        if($publicKey) $this->setPublicKey($publicKey);
        if($privateKey) $this->setPrivateKey($privateKey);

    }


    /**
     * 设置私钥
     * @param $value 密钥
     * @param string $passphrase 密钥的密钥
     * @return $this
     */
    public function setPrivateKey($value, $passphrase = ''){

        if(is_file($value)) $value = file_get_contents($value);
        $this->privateKey = openssl_pkey_get_private($value, $passphrase);
        return $this;
    }


    /**
     * 设置公钥
     * @param $value 密钥
     * @param string $passphrase 密钥的密钥
     * @return $this
     */
    public function setPublicKey($value){

        if(is_file($value)) $value = file_get_contents($value);

        $this->publicKey  = openssl_pkey_get_public($value);
        return $this;
    }


    /**
     * 生成签名
     * @param mixed $data 数据
     * @param int $signature_alg 签名算法
     * @return string 签名
     */
    public function signature($data, $signature_alg = OPENSSL_ALGO_SHA1){

        if(is_array($data)) $data = json_encode($data);

        openssl_sign($data, $signature, $this->privateKey, $signature_alg);
        return base64_encode($signature);

    }

    /**
     * 验证签名
     * @param mixed $data 数据
     * @param string $signature 签名
     * @param int $signature_alg 签名算法
     * @return int 1正确,0错误,-1异常
     */
    public function verify($data, $signature, $signature_alg = OPENSSL_ALGO_SHA1){

        if(is_array($data)) $data = json_encode($data);
        return openssl_verify($data, base64_decode($signature), $this->publicKey, $signature_alg);

    }


    /**
     * 数据加密
     * @param mixed $data 待加密数据
     * @param bool $isPublicKey 是否用公钥
     * @param int $padding 填充
     * @return string 数据密文
     */
    public function encrypt($data, $isPublicKey = true, $padding = OPENSSL_PKCS1_PADDING){

        $buffer = '';
        if(is_array($data)) $data = json_encode($data);

        $data = str_split($data, 117);

        foreach($data as $chunk){

            if($isPublicKey){
                openssl_public_encrypt($chunk, $encrypt,  $this->publicKey, $padding);
            }else{
                openssl_private_encrypt($chunk, $encrypt, $this->privateKey, $padding);
            }
            $buffer .= $encrypt;
        }

        return base64_encode($buffer);

    }


    /**
     * 数据解密
     * @param $content
     * @param bool $isPrivateKey 是否用私钥
     * @param int $padding 填充
     * @return string 数据明文
     */
    public function decrypt($content, $isPrivateKey = true, $padding = OPENSSL_PKCS1_PADDING){

        $buffer = '';
        $content = str_split(base64_decode($content), 128);
        foreach($content as $chunk){

            if($isPrivateKey){
                openssl_private_decrypt($chunk, $decrypt, $this->privateKey, $padding);
            }else{
                openssl_public_decrypt($chunk, $decrypt, $this->publicKey, $padding);
            }
            $buffer .= $decrypt;
        }

        return $buffer;
    }




}


