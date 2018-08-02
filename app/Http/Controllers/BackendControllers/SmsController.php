<?php

namespace App\Http\Controllers\BackendControllers;

use App\Models\User;
use App\Models\EmailInfo;
use App\Models\EmailModel;
use App\Models\OnlineService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Sms;
use App\Models\SmsModel;
use App\Models\SmsInfo;
use App\Helper\Ucpaas;
use App\Helper\Email;
use App\Helper\Uploader;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
class SmsController extends Controller
{
    //短信首页
                public function sms(Request $request){
                    $infos = SmsInfo::where('company_id','<>','')->get();
                    $company = [];
                    foreach ($infos as $info){

                           $companys = User::where('account_id',$info->company_id)->first();
                           if(!is_null($companys)){
                               $company[$info->company_id] = $companys['name'];
                           }
                    }
                  $company = array_unique($company);
                    foreach ($infos as $v){
                        foreach ($company as $k=>$vs){
                           if($v['company_id'] == $k){
                               $v['company_id'] = [$k=>$vs];
                           }
                        }
                    }
                    $infos = $infos->groupby('company_id');
//                    $company_ids = array_unique($company_ids);
//                    $companys = wherein('account_id',$company_ids)->get();
//                    $company_names = [];
//                    foreach ($companys as $company){
//                        $company_names = $company['name'];
//                    }
//
//                    dump($company_names);
////                    dump($infos);
//                     foreach ($value as $num){
//                         $nums = $num['num'];
//                         $sends =  $num['send_num'];
//                         $notice =  $num['notice_money'];
//                         $money =  $num['money'];
//
//                         $request->session()->put('nums', $nums);
//                         $request->session()->put('sends', $sends);
//                         $request->session()->put('moneys', $money);//余额
//                     }
//                            $nums = $request->session()->get('nums');
//                            $sends = $request->session()->get('sends');
//                            $moneys = $request->session()->get('moneys');
                            $models = SmsModel::get();
                            $info = SmsInfo::where('company_id','')->get();
                    $limit = config('list_num.backend.roles');//每页显示几条
                    $params = "/backend/sms/sms";
                    if (isset($_GET['page'])) {
                        $page = $_GET['page'];
                        $res = SmsInfo::where('company_id','')->paginate($limit);
                        $pages = $res->lastPage();//总页数
                        $totals = $res->total();//总数
                        $currentPage = $res->currentPage();//当前页码
                        $result = SmsInfo::where('company_id','')
                            ->skip($page)
                            ->offset(($page - 1) * $limit)
                            ->limit($limit)
                            ->get();
                        return view('backend.sms.smsindex')
                            ->with('models',$models)
                            ->with('info',$result)
                            ->with('infos',$infos)
                            ->with('params', $params)
                            ->with('totals', $totals)
                            ->with('currentPage', $currentPage)
                            ->with('pages', $pages);
                    } else {
                        $page = '1';
                        $res = SmsInfo::where('company_id','')->paginate($limit);
                        $pages = $res->lastPage();//总页数
                        $totals = $res->total();//总数
                        $currentPage = $res->currentPage();//当前页码
                        $result = SmsInfo::where('company_id','')
                            ->skip($page)
                            ->offset(($page - 1) * $limit)
                            ->limit($limit)
                            ->get();
                        return view('backend.sms.smsindex')
                            ->with('models',$models)
                            ->with('info',$result)
                            ->with('infos',$infos)
                            ->with('params', $params)
                            ->with('totals', $totals)
                            ->with('currentPage', $currentPage)
                            ->with('pages', $pages);
                    }

                }
                //发短信
                public  function doSms(){
                    $sms_content = !empty($_GET['sms_content']) ? $_GET['sms_content'] : null;
                    $time =  !empty($_GET['time']) ? $_GET['time'] : date("Y-m-d H:i:s");
                    $phone = $_GET['phone'];
                    $model = $_GET['model'];
                    if(empty($phone)||empty($model)){
                        return (['status'=>'1','message'=>'请输入！！']);
                    }
                        //模板替换内容
                    if($model=='52339'||$model=='62523'||$model=='62524'||$model='107973'){
                        //随机验证码

                        $rand = $biz_content['rand'];
                    }elseif($model=='62526'||$model=='62529'){
                        //申请通知
                        $rand = array($name,' ');

                    }elseif($model=='62508'){
                        //受理通知
                        $rand = array($name,$time);

                    }elseif($model=='62509'){
                        //账户失效
                        $rand = array($name);
                    }
                        $rand = json_encode($rand);
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

                        if($respCode=='000000'){
                            $model  = SmsModel::where('model_id',$model)->get();
                            $replace = json_decode($rand,true);
                            foreach($model as $value){
                                $model = $value->content;
                            }
                            $rand = json_decode($rand,true);
                            if(is_array($rand)){
                                foreach ($rand as $key=>$value){
                                    $content=  preg_replace('/(?:\{)(.*)(?:\})/i',$value,$model);
                                }
                                $res = SmsInfo::insert([
                                    'company_id'=>'',
                                    'content'=>$content,
                                    'send_phone'=>$phone,
                                    'created_at'=>date('Y-m-d H:i:s',time()),
                                    'updated_at'=>date('Y-m-d H:i:s',time())
                                ]);
                            }else{
                                $content=  preg_replace('/(?:\{)(.*)(?:\})/i',$rand,$model,1);
                                $res = SmsInfo::insert([
                                    'company_id'=>'',
                                    'content'=>$content,
                                    'send_phone'=>$phone,
                                    'created_at'=>date('Y-m-d H:i:s',time()),
                                    'updated_at'=>date('Y-m-d H:i:s',time())
                                ]);
                            }
                            return (['status' => 0, 'message' => '短信发送成功！']);
                        }elseif($respCode=='100015'){
                            return (['status' => 1, 'message' => '号码不合法']);
                        }
                    }
                    public function emailInfos(){
                        $infos = EmailInfo::where('send','<>',' ')->get();
                        $company = [];
                        foreach ($infos as $info){
                            $companys = User::where('account_id',$info->send)->first();
                            if(!is_null($companys)){
                                $company[$info->send] = $companys['name'];
                            }
                        }
                        $company = array_unique($company);
                        foreach ($infos as $key=>$v){
                            foreach ($company as $k=>$vs){
                                if($v['send'] == $k){
                                    $v['send'] =[$k=>$vs];
                                }
                            }
                        }
                        $infos = $infos->groupby('send');
                        return view('backend.sms.emailinfos')->with('res',$infos);
                    }
                    public function emailListInfos(){
                        $data = $_GET['data'];
                        $user = User::where('name',$data)->first();
                        $id = $user->account_id;
                        $limit = config('list_num.backend.roles');//每页显示几条
                        $params = "/backend/sms/emaillistinfo?data=".$data."&";
                        if (isset($_GET['page'])) {
                            $page = $_GET['page'];
                            $res = EmailInfo::where('send',$id)->paginate($limit);
                            $pages = $res->lastPage();//总页数
                            $totals = $res->total();//总数
                            $currentPage = $res->currentPage();//当前页码
                            $result = EmailInfo::orderBy('created_at', 'asc')
                                ->where('send',$id)
                                ->skip($page)
                                ->offset(($page - 1) * $limit)
                                ->limit($limit)
                                ->get();
                            return view('backend.sms.hassendemail')
                                ->with('res', $result)
                                ->with('params', $params)
                                ->with('totals', $totals)
                                ->with('currentPage', $currentPage)
                                ->with('pages', $pages);
                        } else {
                            $page = '1';
                            $res = EmailInfo::where('send',$id)->paginate($limit);
                            $pages = $res->lastPage();//总页数
                            $totals = $res->total();//总数
                            $currentPage = $res->currentPage();//当前页码
                            $result = EmailInfo::orderBy('created_at', 'asc')
                                ->where('send',$id)
                                ->skip($page)
                                ->offset(($page - 1) * $limit)
                                ->limit($limit)
                                ->get();
                            return view('backend.sms.hassendemail')
                                ->with('res', $result)
                                ->with('params', $params)
                                ->with('totals', $totals)
                                ->with('currentPage', $currentPage)
                                ->with('pages', $pages);
                        }
                    }
                    public function emailDetail(){
                        $email_id = $_GET['email_id'];
                        $res = EmailInfo::where('id',$email_id)->get();
//                        dump($res);
                        return view('backend.sms.emaildetail')->with('res',$res);

                    }
                    //邮件编辑
                    public function  emailEdit(){
                        return view('backend.sms.emailedit');
                    }
                    public function  emailFileSend(){
                        $uploader = new Uploader();
                        $data = $uploader->upload($_FILES['file'], array(
                            'limit' => 10, //Maximum Limit of files. {null, Number}
                            'maxSize' => 2, //Maximum Size of files {null, Number(in MB's)}
                            'extensions' =>null, //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
                            'required' => false, //Minimum one file is required for upload {Boolean}
                            'uploadDir' => 'upload/', //Upload directory {String}
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
//                            var_dump($files);
                            return (['status' => true, 'message' => $files['files']]);
                        }
                        if($data['hasErrors']){
                            $errors = $data['errors'];
                            return $errors;
                        }
                        function onFilesRemoveCallback($removed_files){
                            foreach($removed_files as $key=>$value){
                                $file = '../upload/' . $value;
                                if(file_exists($file)){
                                    unlink($file);
                                }
                            }
                            return $removed_files;
                        }
                    }
                    //邮箱发送
                    public function emailSend(){

                            $email = $_GET['email'];
                            $emails = explode("；", $email);
                            $title = $_GET['title'];
                            $content = $_GET['content'];
                            if(empty($email)||empty($title)||empty($content)){
                                return (['status'=>'1','message'=>'请输入邮件内容！！ ']);
                            }
                            $file = config('curl.email_file_url').$_GET['file'];
                            $mail = new Email();
                            $send = ' ';
                            $mail->setServer("smtp.exmail.qq.com", "galaxy@mengtiancai.com", "Shiqu521"); //设置smtp服务器
                            $mail->setFrom("galaxy@mengtiancai.com"); //设置发件人
                            $mail->setReceiver($emails);

                            if(empty($file)){
                                $mail->setMailInfo($title,$content);// 设置邮件主题、内容
                            }else{
                                $mail->setMailInfo($title,$content,$file);// 设置邮件主题、内容、附件
                            }
                            $success =  $mail->sendMail(); //发送
                            if($success){
                                EmailInfo::insert([
                                    'send'=>$send,
                                    'receive'=>$email,
                                    'title'=>$title,
                                    'content'=>$content,
                                    'file'=>$file,
                                    'created_at'=>date('YmdHis',time()),
                                    'updated_at'=>date('YmdHis',time())
                                ]);
                                return (['status' => 0, 'message' => '邮件发送成功！']);
                            }else{
                                return (['status' => 1, 'message' => '邮件发送失败！']);
                            }
                        
                        }
                        public function Emails(){
                            $models = EmailModel::all();
                            return view('backend.sms.email')->with('models',$models);
                        }

                        public function emailLists(){
                            $limit = config('list_num.backend.roles');//每页显示几条
                            $params = "/backend/sms/emaillists";
                            if (isset($_GET['page'])) {
                                $page = $_GET['page'];
                                $res = EmailInfo::where('send',' ')->paginate($limit);
                                $pages = $res->lastPage();//总页数
                                $totals = $res->total();//总数
                                $currentPage = $res->currentPage();//当前页码
                                $result = EmailInfo::orderBy('created_at', 'asc')
                                    ->where('send',' ')
                                    ->skip($page)
                                    ->offset(($page - 1) * $limit)
                                    ->limit($limit)
                                    ->get();
                                return view('backend.sms.hassendemail')
                                    ->with('res', $result)
                                    ->with('params', $params)
                                    ->with('totals', $totals)
                                    ->with('currentPage', $currentPage)
                                    ->with('pages', $pages);
                            } else {
                                $page = '1';
                                $res = EmailInfo::where('send',' ')->paginate($limit);
                                $pages = $res->lastPage();//总页数
                                $totals = $res->total();//总数
                                $currentPage = $res->currentPage();//当前页码
                                $result = EmailInfo::orderBy('created_at', 'asc')
                                    ->where('send',' ')
                                    ->skip($page)
                                    ->offset(($page - 1) * $limit)
                                    ->limit($limit)
                                    ->get();
                                return view('backend.sms.hassendemail')
                                    ->with('res', $result)
                                    ->with('params', $params)
                                    ->with('totals', $totals)
                                    ->with('currentPage', $currentPage)
                                    ->with('pages', $pages);
                            }

                        }
                        public  function  emailModels(){

                            $limit = config('list_num.backend.roles');//每页显示几条
                            $params = "/backend/sms/emailmodels";
                            if (isset($_GET['page'])) {
                                $page = $_GET['page'];
                                $res = EmailModel::paginate($limit);
                                $pages = $res->lastPage();//总页数
                                $totals = $res->total();//总数
                                $currentPage = $res->currentPage();//当前页码
                                $result = EmailModel::orderBy('created_at', 'asc')
                                    ->skip($page)
                                    ->offset(($page - 1) * $limit)
                                    ->limit($limit)
                                    ->get();
                                return view('backend.sms.emailmodels')
                                    ->with('res', $result)
                                    ->with('params', $params)
                                    ->with('totals', $totals)
                                    ->with('currentPage', $currentPage)
                                    ->with('pages', $pages);
                            } else {
                                $page = '1';
                                $res = EmailModel::paginate($limit);
                                $pages = $res->lastPage();//总页数
                                $totals = $res->total();//总数
                                $currentPage = $res->currentPage();//当前页码
                                $result = EmailModel::orderBy('created_at', 'asc')
                                    ->skip($page)
                                    ->offset(($page - 1) * $limit)
                                    ->limit($limit)
                                    ->get();
                                return view('backend.sms.emailmodels')
                                    ->with('res', $result)
                                    ->with('params', $params)
                                    ->with('totals', $totals)
                                    ->with('currentPage', $currentPage)
                                    ->with('pages', $pages);
                            }
                        }
                        public  function addEmailModels(){
                            return view('backend.sms.addmodels');
                        }
                        public  function doAddEmailModels(){

                            if(empty($_GET['name'])||empty($_GET['descrition'])){
                                return (['status' => 1, 'message' => '请输入内容！']);
                            }
                            $res = EmailModel::insert([
                                'model_id'=>$_GET['model_id'],
                                'model_name'=>$_GET['name'],
                                'content'=>$_GET['descrition'],
                                'created_at'=>date('YmdHis',time()),
                                'updated_at'=>date('YmdHis',time()),
                            ]);
                            if($res){
                                return (['status' => 0, 'message' => '模板添加成功']);
                            }else{
                                return (['status' => 1, 'message' => '模板添加失败']);
                            }
                        }
                        public function getEmailModels(){
                            $res = EmailModel::where('model_id',$_POST['model_id'])->get();
                            $res = $res[0]['content'];
                            if(is_null($res)){
                                return (['status' => 1, 'message' => '模板获取失败','model'=>' ']);
                            }else{
                                return (['status' => 0, 'message' => '模板获取成功','model'=>$res]);
                            }

                        }
                        public function emailModelsInfo(){
                            $res  = EmailModel::where('id',$_GET['id'])->get();
                            return view('backend.sms.emailmodelsinfo')->with('res',$res);
                        }

                        public function hasEmailInfo(){
                            $res  = EmailInfo::where('id',$_GET['id'])->get();
                            return view('backend.sms.hasemailinfo')->with('res',$res);
                        }

                        public function addModels(){
                                $model = $_GET['model'];
                                $content = $_GET['content'];
                                $model_name = $_GET['model_name'];
                                if(empty($model)||empty($content||empty($model_name))){
                                    return (['status' => 1, 'message' => '请输入！！']);
                                }
                                $models = SmsModel::where('model_id',$model)->first();
                                if(is_null($models)){
                                    $res = SmsModel::insert(['model_id'=>$model,'content'=>$content,'model_name'=>$model_name]);
                                    if($res){
                                        return (['status' => 0, 'message' => '操作成功！']);
                                    }else{
                                        return (['status' => 1, 'message' => '操作失败 ！']);
                                    }
                                }else{
                                    return (['status' => 2, 'message' => '操作失败！']);
                                }

                        }
                        //短信正则替换测试
                        public function  smsInfo(){
                            $model = SmsModel::where('model_id',$_GET['model'])->get();
                            $rand = array('0'=>'3');
                            $rand[] = '2';
                            foreach($model as $value){
                                $model = $value->content;
                            }
                            foreach ($rand as $key=>$value){
                                $content=  preg_replace_array('/(?:\{)(.*)(?:\})/i',$rand,$model);
                            }
                        }
                        public function  smsInfoList(){

                            $company_name = $_GET['company_id'];
                            $company = User::where('name',$company_name)->first();
                            if(!is_null($company)){
                                $company_id = $company->account_id;
                                $limit = config('list_num.backend.roles');//每页显示几条
                                $params_pages = '?company_id='.$_GET['company_id'];
                                $params = '/backend/sms/smsinfolist'.$params_pages."&";
                                if (isset($_GET['page'])) {
                                    $page = $_GET['page'];
                                    $res = SmsInfo::where('company_id',$company_id)->paginate($limit);
                                    $pages = $res->lastPage();//总页数
                                    $totals = $res->total();//总数
                                    $currentPage = $res->currentPage();//当前页码
                                    $result = SmsInfo::orderBy('created_at', 'asc')
                                        ->where('company_id',$company_id)
                                        ->skip($page)
                                        ->offset(($page - 1) * $limit)
                                        ->limit($limit)
                                        ->get();
                                    return view('backend.sms.smslist')
                                        ->with('infos',$result)
                                        ->with('params', $params)
                                        ->with('totals', $totals)
                                        ->with('currentPage', $currentPage)
                                        ->with('pages', $pages);
                                } else {
                                    $page = '1';
                                    $res = SmsInfo::where('company_id',$company_id)->paginate($limit);
                                    $pages = $res->lastPage();//总页数
                                    $totals = $res->total();//总数
                                    $currentPage = $res->currentPage();//当前页码
                                    $result = SmsInfo::orderBy('created_at', 'asc')
                                        ->where('company_id',$company_id)
                                        ->skip($page)
                                        ->offset(($page - 1) * $limit)
                                        ->limit($limit)
                                        ->get();
                                    return view('backend.sms.smslist')
                                        ->with('infos',$result)
                                        ->with('params', $params)
                                        ->with('totals', $totals)
                                        ->with('currentPage', $currentPage)
                                        ->with('pages', $pages);
                                }


                            }else{
                                return "<script>location.href(history.back(-1));</script>";
                            }

                        }
                        public function smsListInfo(){
                            $sms_id = $_GET['sms_id'];
                            $company_id = SmsInfo::where('id',$sms_id)->first();
                            $company_id = $company_id['company_id'];
                            $company = User::where('account_id',$company_id)->first();
                            $company_name = $company['name'];
                            $info = SmsInfo::where('id',$sms_id)->get();
                            return view('backend.sms.smsinfo')->with('info',$info)->with('company_name',$company_name);
                        }

                        //在线客服管理
                        public function onlineService(){
                            $limit = config('list_num.backend.roles');//每页显示几条
                            $params = "/backend/sms/onlineservice";
                            if (isset($_GET['page'])) {
                                $page = $_GET['page'];
                                $res = OnlineService::where('status','0')->paginate($limit);
                                $pages = $res->lastPage();//总页数
                                $totals = $res->total();//总数
                                $currentPage = $res->currentPage();//当前页码
                                $result = OnlineService::where('status','0')
                                    ->skip($page)
                                    ->offset(($page - 1) * $limit)
                                    ->limit($limit)
                                    ->get();
                                return view('backend.sms.onlineservice')
                                    ->with('res', $result)
                                    ->with('params', $params)
                                    ->with('totals', $totals)
                                    ->with('currentPage', $currentPage)
                                    ->with('pages', $pages);
                            } else {
                                $page = '1';
                                $res = OnlineService::where('status','0')->paginate($limit);
                                $pages = $res->lastPage();//总页数
                                $totals = $res->total();//总数
                                $currentPage = $res->currentPage();//当前页码
                                $result = OnlineService::where('status','0')
                                    ->skip($page)
                                    ->offset(($page - 1) * $limit)
                                    ->limit($limit)
                                    ->get();
                                return view('backend.sms.onlineservice')
                                    ->with('res', $result)
                                    ->with('params', $params)
                                    ->with('totals', $totals)
                                    ->with('currentPage', $currentPage)
                                    ->with('pages', $pages);
                            }

                        }
                        //添加
                        public function addOnlines(){
                            return view('backend.sms.addonlines');
                        }
                        public function doAddOnlines(Request $request){
                            $res = $request->aLL();
                            if(empty($res['name'])||empty($res['number'])||empty($res['datas'])||empty($res['phone'])||empty($res['real_name'])||empty($res['card_id'])){
                                return (['status'=>'1','message'=>'请正确填写内容']);
                            }
                            $ids = OnlineService::where('number',$res['number'])->first();
                            if(!is_null($ids)){
                                return (['status'=>'1','message'=>'您已经添加过此号码！！']);
                            }else{
                                OnlineService::insert($res);
                                return (['status'=>'0','message'=>'添加成功']);
                            }
                        }
                        public function  onlinesInfo(){
                            $res = OnlineService::where('id',$_GET['id'])->first();
                            return view('backend.sms.onlinesinfo')->with('res',$res);
                        }



}
