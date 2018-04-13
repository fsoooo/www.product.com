<?php
/**
 * Created by PhpStorm.
 * User: wangsl
 * Date: 2017/8/23
 * Time: 18:27
 */
namespace App\Http\Controllers\ApiControllers;

use App\Models\User;
use App\Models\Insurance;
use App\Models\Duty;
use App\Models\Tariff;
use App\Models\Clause;
use App\Models\Claim;
use App\Models\Preservation;
use Illuminate\Http\Request;
use App\Helper\RsaSignHelp;
use Ixudra\Curl\Facades\Curl;
use Validator, DB, Image, Schema;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class ChannelController{
    protected $sign_help;
    //初始化
    public function __construct(Request $request)
    {
        $this->sign_help = new RsaSignHelp();
        $this->request = $request;
    }
    public function getChannelInfo(){
        $data = $this->request->get('biz_content');
        $biz_content = json_decode($this->sign_help->base64url_decode(strrev($data)), true);
        $all = $biz_content['params'];//更改内容
        $channel_code_res = User::where('code',$all['channel_code'])->first();
        $channel_name_res = User::where('name',$all['channel_code'])->first();
        $channel_url_res = User::where('call_back_url',$all['channel_code'])->first();
        $channel_email_res = User::where('email',$all['channel_code'])->first();
        if(is_null($channel_code_res)&&is_null($channel_name_res)&&is_null($channel_url_res)&&is_null($channel_email_res)){
            return (['status'=>'205','content'=>'没有检测到渠道信息，请检查您的您的信息是否正确！']);
        }else{
            if(!is_null($channel_code_res)){
                return (['status'=>'200','content'=>['account_id'=>$channel_code_res->only_id,'sign_key'=>$channel_code_res->sign_key,'account'=>$channel_code_res->name],'channel_info'=>$channel_code_res]);
            }
            if(!is_null($channel_name_res)){
                return (['status'=>'200','content'=>['account_id'=>$channel_name_res->only_id,'sign_key'=>$channel_name_res->sign_key,'account'=>$channel_name_res->name],'channel_info'=>$channel_name_res]);
            }
            if(!is_null($channel_url_res)){
                return (['status'=>'200','content'=>['account_id'=>$channel_url_res->only_id,'sign_key'=>$channel_url_res->sign_key,'account'=>$channel_url_res->name],'channel_info'=>$channel_url_res]);
            }
            if(!is_null($channel_email_res)){
                return (['status'=>'200','content'=>['account_id'=>$channel_email_res->only_id,'sign_key'=>$channel_email_res->sign_key,'account'=>$channel_email_res->name],'channel_info'=>$channel_email_res]);
            }
        }
    }
}



