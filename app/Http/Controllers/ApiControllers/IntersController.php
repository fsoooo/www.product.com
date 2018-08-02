<?php

namespace App\Http\Controllers\ApiControllers;

use App\Models\EmailInfo;
use App\Models\Insurance;
use App\Models\SmsInfo;
use App\Models\SmsModel;
use Illuminate\Http\Request;
use App\Helper\RsaSignHelp;
use Ixudra\Curl\Facades\Curl;
use Validator, DB, Image, Schema;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Models\Inter;
use App\Models\OnlineService;
use App\Models\ApiOption;
use App\Helper\Ucpaas;
use App\Helper\Uploader;
use App\Helper\Email;
use Mail;
use App\Mail\BaiduWorkInsureList;
use App\Mail\IdentifyingCode;

class IntersController extends BaseController{
    protected $sign_help;
    protected $private_key;
    protected $public_key;
    protected $wj_public_key;
    //初始化
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->sign_help = new RsaSignHelp();
        $this->private_key = file_get_contents('../config/rsa_private_key.pem');
        $this->public_key = file_get_contents('../config/rsa_public_key.pem');
        $this->wj_public_key = file_get_contents('../config/wj_rsa_public_key.pem');
    }
    public function passService(){

        $biz_content = $this->request->get('biz_content');
        $biz_content  = json_decode($this->sign_help->base64url_decode(strrev($biz_content)), true);
        $company = $biz_content['company'];
        $time = time();
        $num = "1234567890abcdefghigklmnopqrstuvwxyz";
        $str =  md5(str_shuffle($company.$time.$num));
        $token = substr($str,0,30);//30位的token
        $check_company = Inter::where('compony_id',$company)->first();
        if(is_null($check_company)){
            //生成token存入数据库
            $res = Inter::insert([
                'compony_id'=>$company,
                'token'=>$token,
                'inter_status'=>'1',
                'is_pay'=>'0'
            ]);
            if($res){
                //开通接口后，token，回调地址返回给客户
                return (['status' => 'true', 'message' => '开通成功！','token'=>$token]);
            }else{
                return (['status' => 'false', 'message' => '开通失败！']);
            }
        }else{
            return (['status' => 'false', 'message' => 'error']);
        }
    }
    public  function doSms(){
        $b = $this->request->get('biz_content');
        $biz_content = json_decode($this->sign_help->base64url_decode(strrev($b)), true);
        $token = $biz_content['token'];
        $phone = $biz_content['phone'];
        $sms_content = $biz_content['sms_content'];
        $model = $biz_content['model'];
        $time = $biz_content['time'];
        $company = $biz_content['company'];
//        $res = Inter::where('compony_id',$company)->first();
//        $tokens = $res->token;
//        $price = $res->price;
//        $money = $res->money;
//        $nums = $res->inter_nums;
//        if($money<5){
//            return (['status' => 2, 'message' => '您已欠费，为了不影响使用，请尽快缴费']);
//        }else{
            $name = isset($sms_content['name'])?$sms_content['name']:'';
            //模板替换内容
            if($model=='52339'||$model=='62523'||$model=='62524'||$model='107973'||$model='134321'){
                //随机验证码
                $rand = $biz_content['rand'];
                $model = $biz_content['model'];
            }elseif($model=='62526'||$model=='62529'){
                //申请通知
                $rand = array($name,' ');
                $model = $biz_content['model'];
            }elseif($model=='62508'){
                //受理通知
                $rand = array($name,$time);
                $model = $biz_content['model'];
            }elseif($model=='62509'){
                //账户失效
                $rand = array($name);
                $model = $biz_content['model'];
            }
            $rand = json_encode($rand);
            $model = $biz_content['model'];
            //初始化 $options必填
            $options['accountsid']='1eab778740ae84274f41dc044b6c1902';
            $options['token']='5a4740146ef9e60089010b33f5d95425';
            $ucpass = new Ucpaas($options);
            $appId = "316ecd170e12498a9495267c45bb3a00";
            $to = $phone;
            $templateId = $model;//模板类型
            $param=$rand;
             $success=$ucpass->templateSMS($appId,$to,$templateId,$param);
             $object = json_decode($success);
             //对象转数组
             if (is_object($object)) {
                 foreach ($object as $key => $value) {
                     $array[$key] = $value;
                 }
             }
             else {
                 $array = $object;
             }
             // var_dump($array['resp']);
             $objects = $array['resp'];
             if (is_object($objects)) {
                 foreach ($objects as $key => $value) {
                     $array[$key] = $value;
                 }
             }
             else {
                 $array = $objects;
             }
             $respCode = $array['respCode'];
//            $respCode = '000000';
//             var_dump($respCode);
            if($respCode=='000000'){
//                $num = 0;//计数，计价（每发成功一条）
//                $num = $num + 1;//本次计数
//                $nums = $nums+1;//总计数
//                $money = $money - $price*$num;//余额
                $model  = Smsmodel::where('model_id',$model)->get();
                $replace = json_decode($rand,true);
                foreach($model as $value){
                    $model = $value->content;
                }
                $rand = json_decode($rand,true);
                // var_dump($rand);
                if(is_array($rand)){
                    foreach ($rand as $value){
                    $content=  preg_replace('/(?:\{)(.*)(?:\})/i',$value,$model);
                }
                $res = SmsInfo::insert([
                    'company_id'=>$company,
                    'content'=>$content,
                    'send_phone'=>$phone,
                    'created_at'=>date('Y-m-d H:i:s',time()),
                    'updated_at'=>date('Y-m-d H:i:s',time())
                ]);
            }else{
                    $content=  preg_replace('/(?:\{)(.*)(?:\})/i',$rand,$model,1);
                 $res = SmsInfo::insert([
                    'company_id'=>$company,
                    'content'=>$content,
                    'send_phone'=>$phone,
                    'created_at'=>date('Y-m-d H:i:s',time()),
                    'updated_at'=>date('Y-m-d H:i:s',time())
                ]);
            }
                // if($money < 5){//欠费
                //     DB::table('inters')->where('token',$token)->update(['money' =>$money,'inter_nums'=>$nums,'status'=>'2' ]);
                //     return (['status' => 2, 'message' => '您已欠费，为了不影响使用，请尽快缴费']);
                // }else{
                //     DB::table('inters')->where('token',$token)->update(['money' =>$money,'inter_nums'=>$nums ]);
                     return (['status' => 0, 'message' => '短信发送成功']);
                // }
            }elseif($respCode=='100015'){
                return (['status' => 1, 'message' => '号码不合法']);
            }
//        }
    }
    /**
     * 邮件发送
     *
     * @param $to_email|string    邮件接收地址
     * @param $email_data|string  邮件内容
     * @param $email_type|string  邮件类型
     *
     * @return $return_data|json
     *
     */    public function doEmail($to_email,$email_data,$email_type=null){
        //发送邮件
        $to_email = json_decode($to_email,true);
        if(empty($to_email)){
            $rerurn_data = json_encode(['status' => '500', 'message' => '邮件接收地址格式错误！'],JSON_UNESCAPED_UNICODE);
            return $rerurn_data;
        }
        if(is_array($email_data)){
            Mail::to($to_email)->send(new BaiduWorkInsureList($email_data));
        }elseif(is_string($email_data)&&is_numeric($email_data)){
            Mail::to($to_email)->send(new IdentifyingCode($email_data));
        }else{
            Mail::to($to_email)->send(new IdentifyingCode($email_data));
        }
        $rerurn_data = json_encode(['status' => '200', 'message' => '邮件发送成功！'],JSON_UNESCAPED_UNICODE);
        return $rerurn_data;
    }

//            返回码说明
//            000000    ok
//            100000    金额不为整数
//            100001    余额不足
//            100002    数字非法
//            100003    不允许有空值
//            100004    枚举类型取值错误
//            100005    访问IP不合法
//            100006    手机号不合法
//            100015    号码不合法
//            100500    HTTP状态码不等于200
//            100007    查无数据
//            100008    手机号码为空
//            100009    手机号为受保护的号码
//            100010    登录邮箱或手机号为空
//            100011    邮箱不合法
//            100012    密码不能为空
//            100013    没有测试子账号
//            100014    金额过大,不要超过12位数字
//            100016    余额被冻结
//            100017    余额已注销
//            100018    通话时长需大于60秒
//            100699    系统内部错误
//            100019    应用餘額不足
//            100020    字符长度太长
//            100104    callId不能为空
//            100105    日期格式错误
//            100108    取消回拨失败
    //短信订单
    public  function paySms(){
        $biz_content = $this->request->get('biz_content');
        $biz_content  = json_decode($this->sign_help->base64url_decode(strrev($biz_content)), true);
        $company = $biz_content['company'];
        $name =  $biz_content['name'];
        $order_num =  $biz_content['order_num'];
        $pay_type =  $biz_content['pay_type'];
        $money =  $biz_content['money'];
        $time =  $biz_content['create_time'];
        switch ($pay_type) {
            case "alipay":
                return  (['支付宝支付！']);
                break;
            case "bank":
                return  (['银行卡！']);
                break;
            case "wechat":
                return  (['微信！']);
                break;
        }
    }
   public function sendEmails()
        {
            $biz_content = $this->request->get('biz_content');
            $biz_content  = json_decode($this->sign_help->base64url_decode(strrev($biz_content)), true);
//            var_dump($biz_content);
            //获取数据
            $email = $biz_content['email'];
//            $email  =  rtrim($email,"；");
            $emails = explode("；", $email);
            $title = $biz_content['title'];
            $content = $biz_content['content'];
            $send = $biz_content['send'];
//            $mail = new Email();
//            $mail->setServer("smtp.exmail.qq.com", "ghj@douweixiao.cn", "1234Aa"); //设置smtp服务器
//            $mail->setFrom("ghj@douweixiao.cn"); //设置发件人
//            //设置收件人，多个收件人，调用多次
//
//            $file = "uploads/mHwnSN9rWz.md";
//            $mail->setReceiver($emails);
//            $mail->setMailInfo($title, $content,$file);// 设置邮件主题、内容
//            $success = $mail->sendMail(); //发送
            $success = $this->doEmail($email,$content);
            if($success){
                return (['status' => 0, 'message' => '邮件发送成功！']);
            }
            if ($success) {
                $res = EmailInfo::insert([
                    'receive'=>$email,
                    'send'=>$send,
                    'title'=>$title,
                    'content'=>$content,
                    'created_at'=>date('Y-m-d H:i:s',time()),
                    'updated_at'=>date('Y-m-d H:i:s',time())
                ]);
                $res = Email_info::all();
//                var_dump($res);
                if($res){
                    return (['status' => 0, 'message' => '邮件发送成功！']);
                }
            } else {
                return (['status' => 1, 'message' => '邮件发送失败！']);
            }
        }

    public function sendEmailFiles(){
        $biz_content = $this->request->get('biz_content');
        $biz_content  = json_decode($this->sign_help->base64url_decode(strrev($biz_content)), true);
        // var_dump($biz_content);
        $uploader = new Uploader();
        $data = $uploader->upload($biz_content['file'], array(
            'limit' => 10, //Maximum Limit of files. {null, Number}
            'maxSize' => 10, //Maximum Size of files {null, Number(in MB's)}
            'extensions' => null, //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
            'required' => false, //Minimum one file is required for upload {Boolean}
            'uploadDir' => 'uploads/', //Upload directory {String}
            'title' => array('auto', 10), //New file name {null, String, Array} *please read documentation in README.md
            'removeFiles' => true, //Enable file exclusion {Boolean(extra for jQuery.filer), String($_POST field name containing json data with file names)}
            'replace' => false, //Replace the file if it already exists  {Boolean}
            'perms' => null, //Uploaded file permisions {null, Number}
            'onCheck' => null, //A callback function name to be called by checking a file for errors (must return an array) | ($file) | Callback
            'onError' => null, //A callback function name to be called if an error occured (must return an array) | ($errors, $file) | Callback
            'onSuccess' => null, //A callback function name to be called if all files were successfully uploaded | ($files, $metas) | Callback
            'onUpload' => null, //A callback function name to be called if all files were successfully uploaded (must return an array) | ($file) | Callback
            'onComplete' => null, //A callback function name to be called when upload is complete | ($file) | Callback
            'onRemove' => 'onFilesRemoveCallback' //A callback function name to be called by removing files (must return an array) | ($removed_files) | Callback
        ));
        if($data['isComplete']){
            $files = $data['data'];
            echo '1';
            return(['status' => true, 'message' => $files['files']]);
        }
        echo '3';
        if($data['hasErrors']){
            $errors = $data['errors'];
            return $errors;
        }
        function onFilesRemoveCallback($removed_files){
            foreach($removed_files as $key=>$value){
                $file = '../uploads/' . $value;
                if(file_exists($file)){
                    unlink($file);
                }
            }
            return $removed_files;
        }
    }



    public function saveEmails()
    {
        $biz_content = $this->request->get('biz_content');
        $biz_content  = json_decode($this->sign_help->base64url_decode(strrev($biz_content)), true);
        //获取数据
        $email = $biz_content['email'];
        $title = $biz_content['title'];
        $content = $biz_content['content'];
        $send = $biz_content['send'];
        $file = $biz_content['file'];

            $res = EmailInfo::insert([
                'receive'=>$email,
                'send'=>$send,
                'title'=>$title,
                'content'=>$content,
                'file'=>$file,
                'created_at'=>date('Y-m-d H:i:s',time()),
                'updated_at'=>date('Y-m-d H:i:s',time())
            ]);
            if($res){
                return (['status' => 0, 'message' => '邮件保存成功！']);
            } else {
                return (['status' => 1, 'message' => '邮件保存失败！']);
            }
    }


    public  function  getOnlines(){
        $biz_content = $this->request->get('biz_content');
        $biz_content  = json_decode($this->sign_help->base64url_decode(strrev($biz_content)), true);
        //获取数据
        $onlines = OnlineService::where('status','0')->get();
        $onlines_id = [];
        foreach ($onlines as $online){
            $onlines_id[] = $online['id'];
        }
        if(!empty($onlines_id)){
            $data = $onlines->toArray();
            return response()->json(['status'=>'0','data' => $data]);
        }else{
            return response()->json(['status'=>'1','data' => '']);
        }
    }

}