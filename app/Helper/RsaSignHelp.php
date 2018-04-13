<?php
namespace App\Helper;

class RsaSignHelp{
    /**
     * 分段加密
     * @param $data string
     * @param $key string
     * @param $key_type string
     * @return mixed
     */
    public function doEncrypt($data, $key, $key_type)
    {
        //密钥检测
        $key = $this->checkKey($key, $key_type);
        //分段加密
        $crypt_ed = array();
        $dataArray = str_split($data, 117);
        ////使用公钥 或 私钥 加密
        if($key_type == 'public'){
            foreach($dataArray as $v){
                $encryptData = null;
                if(openssl_public_encrypt($v,$encryptData, $key)){
                    $crypt_ed[] = $encryptData;
                } else {
                    die('加密失败');
                }
            }
        } else {
            foreach($dataArray as $v){
                $encryptData = null;
                if(openssl_private_encrypt($v,$encryptData, $key)){
                    $crypt_ed[] = $encryptData;
                } else {
                    die('加密失败');
                }
            }
        }
        $crypt_ed = implode('',$crypt_ed);
        $result = base64_encode($crypt_ed);
        return $result;
    }

    /**
     * 分段解密
     * @param $data string
     * @param $key string
     * @param $key_type string
     * @return mixed
     */
    public function doDecrypt($data, $key, $key_type)
    {
        //密钥检测
        $key = $this->checkKey($key, $key_type);
        //分段解密
        $encrypt_str = base64_decode($data);
        $decrypted = array();
        $dataArray = str_split($encrypt_str, 128);
        //使用公钥 或 私钥 解密
        if($key_type == 'public'){
            foreach($dataArray as $subData){
                $subDecrypted = null;
                openssl_public_decrypt($subData, $subDecrypted, $key);
                $decrypted[] = $subDecrypted;
            }
        } else {
            foreach($dataArray as $subData){
                $subDecrypted = null;
                openssl_private_decrypt($subData, $subDecrypted, $key);
                $decrypted[] = $subDecrypted;
            }
        }
        $decrypted = implode('',$decrypted);
        return json_decode($decrypted, true);
    }


    /**
     * * rsa 签名
     * 排序->json处理->sign->url安全
     * @param $data
     * @param $key
     * @param $key_type
     * @param int $sign_type
     * @param bool $safe_url
     * @return mixed|string
     */
    public function rsa_sign($data, $key, $key_type, $sign_type = OPENSSL_ALGO_MD5, $safe_url = true)
    {
        $this->checkKey($key, $key_type);
        //是否为数组
        if(is_array($data)){
            ksort($data);
            //拼接字符串
            $str = str_replace('\\', '', json_encode($data));
        } else {
            $str = $data;
        }

        //加签
        $out_sign = '';
        openssl_sign($str, $out_sign, $key, $sign_type);
        $out_sign = base64_encode($out_sign);
        if($safe_url){
            $out_sign = $this->base64url_encode($out_sign); //base64转安全URL
        }

        return $out_sign;
    }

    /**
     * rsa 验签
     * @param $data
     * @param $sign
     * @param $key
     * @param $key_type
     * @param int $sign_type
     * @return mixed
     */
    public function rea_sign_verify($data, $sign, $key, $key_type, $sign_type = OPENSSL_ALGO_MD5)
    {
        $this->checkKey($key, $key_type);
        ksort($data);
        //拼接字符串
        $str = str_replace('\\', '', json_encode($data, JSON_UNESCAPED_UNICODE));
	//echo $str;
	$sign = $this->base64url_decode($sign);
        $result = openssl_verify($str, $sign, $key, $sign_type);
        return $result;
    }


    /**
     * 检查密钥规范
     * @param $key
     * @param $key_type
     * @return bool|resource
     */
    public function checkKey($key, $key_type)
    {
        extension_loaded('openssl') or die('php需要openssl扩展支持');
        switch ($key_type){
            case 'public':
                $key =  openssl_pkey_get_public($key);//这个函数可用来判断公钥是否是可用的，可用返回资源id Resource id
                break;
            case 'private':
                $key =  openssl_pkey_get_private($key);//这个函数可用来判断私钥是否是可用的
                break;
        }
        ($key) or die('密钥不可用');
        return $key;
    }

    /**
     * data->base64->编译url安全参数
     * @param $data string
     * @return mixed
     */
    public function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * 反编安全参数->base64_decode
     * @param $data string
     * @return mixed
     */
    public function base64url_decode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    /**
     * ty内部签名
     * @param $biz_content，业务参数
     * @param array $other_ty_options 其他系统参数
     * @return array
     */
    public function tySign($biz_content, $other_ty_options = [])
    {
        $biz_content = strrev($this->base64url_encode(json_encode($biz_content, JSON_UNESCAPED_UNICODE)));

        $data['account_id'] = env('TY_API_ID', '123456789'); //id

        $data['timestamp'] = date('YmdHis');  //时间戳
        $data['biz_content'] = $biz_content;    //业务参数特殊字符串
        $data = array_merge($data, $other_ty_options);
        krsort($data);  //排序

        $data['sign'] = md5($this->base64url_encode(json_encode($data)) . env('TY_API_PASSWORD', 'testSignKey'));

        return $data;
    }

    /**
     * ty内部解析源数据
     * @param $ty_sign_data
     * @return mixed
     */
    public function tyDecodeOriginData($ty_sign_data)
    {
        //业务参数 解析出源数据json字符串
        $original_data_array = json_decode($this->base64url_decode(strrev($ty_sign_data)), true);
        return $original_data_array;
    }

}
