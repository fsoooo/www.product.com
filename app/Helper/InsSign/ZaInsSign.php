<?php

/**
 * Created by PhpStorm.
 * User: xyn
 * Date: 2018/3/1
 * Time: 9:57
 */

namespace App\Helper\InsSign;

class ZaInsSign
{
    protected $_public_key;
    protected $_private_key;

    public function __construct()
    {
        if(env('INS_API_TEST', 1)){
            //接口公私钥文件
            $this->_public_key = file_get_contents('../config/za_jiekuan_test_public_key.pem');
            $this->_private_key = file_get_contents('../config/za_jiekuan_test_private_key.pem');
            $this->_pay_app_key = 'eNw4RpAuPVTq67IzGKzr';   //支付密钥
            $this->_pay_url = 'http://cashier.itest.zhongan.com/za-cashier-web/gateway.do';
            $this->push_key = 'tianyan@20180226';   //T-1日推送密钥
        } else {
            //todo

        }
    }
    
    /**
     * 对参数进行加密
     * @param $params 待加密参数
     * @param $publicKey 对端的公钥
     * @return string
     */
    public function encrypt($params) {
        $_rawData = json_encode ($this->filterParam ( $params ) );

        $_encryptedList = array ();
        $_step = 117;

        for($_i = 0, $_len = strlen ( $_rawData ); $_i < $_len; $_i += $_step) {
            $_data = substr ( $_rawData, $_i, $_step );
            $_encrypted = '';

            openssl_public_encrypt ( $_data, $_encrypted, $this->_public_key );
            $_encryptedList [] = ($_encrypted);
        }
        $_data = base64_encode ( join ( '', $_encryptedList ) );
        return $_data;
    }

    /**
     * 对参数进行加签
     * @param $params 待加签参数
     * @return mixed
     */
    public function sign($params) {
        ksort ( $params );
        $_signStr = json_encode ( $params );
        $_signStr = stripslashes ( $_signStr );
        $_privateKeyId = openssl_get_privatekey ( $this->_private_key );
        openssl_sign ( $_signStr, $_data, $_privateKeyId );
        openssl_free_key ( $_privateKeyId );
        $_data = base64_encode ( $_data );
        return $_data;
    }

    /**
     * @param $params
     * @param $sign
     * @param $publicKey
     * @return int
     */
    public function checkSign($params, $sign) {
//        dd($params);
        $_params = $this->filterParam ( $params );
        ksort ($_params);
        $_publicKeyId = openssl_get_publickey ( $this->_public_key );
        $_data = json_encode ( $_params, JSON_UNESCAPED_UNICODE );
        $_data = stripslashes ( $_data );
        $_result = openssl_verify ( $_data, base64_decode ( $sign ), $_publicKeyId, "sha1WithRSAEncryption" );
        openssl_free_key ( $_publicKeyId );
        return $_result;
    }

    /**
     * 对参数进行解密
     * @param $encryptedData 待解密参数
     * @param $privateKey 自己的私钥
     * @return string
     *
     */
    public function decrypt($encryptedData) {
        $_encryptedData = base64_decode ( $encryptedData );

        $_decryptedList = array ();
        $_step = 128;
        if (strlen ( $this->_private_key ) > 1000) {
            $_step = 256;
        }
        for($_i = 0, $_len = strlen ( $_encryptedData ); $_i < $_len; $_i += $_step) {
            $_data = substr ( $_encryptedData, $_i, $_step );
            $_decrypted = '';
            openssl_private_decrypt ( $_data, $_decrypted, $this->_private_key );
            $_decryptedList [] = $_decrypted;
        }

        return join ( '', $_decryptedList );
    }

    /**
     * 保证只传有值的参数
     * @param $params
     * @return array
     */
    public function filterParam($params) {
        $_result = array ();
        foreach ( $params as $_key => $_value ) {
            // 没有值的
            if (empty ( $_value ) && $_value != 0) {
                continue;
            }

            if (is_array ( $_value )) {
                $_result [$_key] = json_encode ( $_value );
            } else {
                $_result [$_key] = $_value ? $_value : '';
            }
        }
        return $_result;
    }



    //============================收银台===================================

    /**
     * 获取支付url（支付前使用）
     * @param $params
     * @return string
     */
    public function createPayUrl($params){
//        if(!$this->_validationConfig()){
//            return false;
//        }
        $params['sign']=$this->_paramsToSign($params);

        $queryStr=$this->_myHttpBuildQuery($params);

        return $this->_pay_url.'?'.$queryStr;
    }


    /**
     * 验证签名（支付回调使用）
     * @param $params
     * @return bool
     */
    public function validationSign($params){
//        if(!$this->_validationConfig()){
//            return false;
//        }
        $requestSign=$params['sign'];

        $mySign=$this->_paramsToSign($params);
        if($requestSign!==$mySign){
//            $this->$_error='签名错误';
            return false;
        }
        return true;
    }


//    /**
//     * 获取错误信息
//     * @return mixed
//     */
//    public function getError(){
//        return $this->$_error;
//    }


    /**
     * 合成queryString
     * @param $params
     * @return string
     */
    private function _myHttpBuildQuery($params){
        $queryStr='';
        foreach($params as $paramsK=>$paramsV){
            $queryStr.=$paramsK.'='.$paramsV.'&';
        }
        $queryStr=trim($queryStr,'&');

        return $queryStr;
    }

//    /**
//     * 验证基础配置（这里处理比较粗暴，可以按照自己的方式来灵活处理）
//     * @return bool
//     */
//    private function _validationConfig(){
//        if(!$this->$_pay_url || !$this->$_mechantCode || !$this->$_appKey){
//            $this->$_error='配置有误';
//            return false;
//        }
//        return true;
//    }


    /**
     * 合成sign
     * @param $params   参数数组
     * @return string
     */
    private function _paramsToSign($params){
        if(isset($params['sign'])){
            unset($params['sign']);
        }
        if(isset($params['sign_type'])){
            unset($params['sign_type']);
        }
        ksort($params);
        $queryStr=$this->_myHttpBuildQuery($params);
        $queryStr=$queryStr . $this->_pay_app_key;
        $queryStr=md5($queryStr);
        return $queryStr;
    }

    //==========================保单推送======================================

    public function pushEncrypt($input) {
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $input = $this->pkcs5_pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $this->push_key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        return $data;
    }

    private function pkcs5_pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }


    public function pushDecrypt($sStr) {
        $decrypted= mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->push_key, base64_decode($sStr), MCRYPT_MODE_ECB);
        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s-1]);
        $decrypted = substr($decrypted, 0, -$padding);
        return $decrypted;
    }
}