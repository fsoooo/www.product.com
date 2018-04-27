<?php

namespace App\Http\Controllers\ApiControllers;

use App\Helper\LogHelper;
use App\Models\ApiFrom;
use App\Models\InsuranceApiInfo;
use App\Repositories\InsuranceApiFromRepository;
use Illuminate\Http\Request;
use App\Helper\RsaSignHelp;

class InsCurlController
{
    /**
     * API来源
     *
     * @var $api
     */
    protected $api;
    protected $template_url;
    protected $insurance_api_from;
    public function __construct(Request $request, RsaSignHelp $signHelp, InsuranceApiFromRepository $repository)
    {
        $data = $request->all();
        $original_data = $signHelp->tyDecodeOriginData($data['biz_content']);
        $insurance_id = isset($original_data['ty_product_id']) ? $original_data['ty_product_id'] : 0;    //天眼产品ID
        $private_p_code = isset($original_data['private_p_code']) ? $original_data['private_p_code'] : 0;   //内部产品唯一码
        if ($insurance_id) {
            $this->insurance_api_from = $insurance_api_from = $repository->getApiStatusOn($insurance_id);
        } else {
            $this->insurance_api_from = $insurance_api_from = $repository->getApiByPrivatePCode($private_p_code);
        }
        if(empty($insurance_api_from))
            return ['data'=> 'product not exist', 'code'=> 400];
        //团险模板
        $this->template_url = $insurance_api_from->template_url;
        $api_from = ApiFrom::where('id', $insurance_api_from->api_from_id)->first();
        $class_name = 'App\Http\Controllers\ApiControllers\Curls\\' . $api_from->uuid . 'InsCurlController';
        $this->api = new $class_name($request, $insurance_api_from);
    }

    //产品列表
    public function ins()
    {
        $res = $this->api->ins();
        return response($res['data'], $res['code']);
    }

    //产品详情
    public function insInfo()
    {
    	dd(132);die;
        $res = $this->api->insInfo();
        return response(json_encode($res['data']), $res['code']);
    }

    //投保属性
    public function insAttr()
    {
        $res = $this->api->insAttr();
        return response($res['data'], $res['code']);
    }


    //算费
    public function quote()
    {
        $res = $this->api->quote();
        return response($res['data'], $res['code']);
    }

    //todo delete
    public function buy()
    {
        $res = $this->api->buy();
        return response($res['data'], $res['code']);
    }

    //投保
    public function buyIns()
    {
        $res = $this->api->buyIns();
        return response($res['data'], $res['code']);
    }

    //核保
    public function checkIns()
    {
        $res = $this->api->checkIns();
        return response($res['data'], $res['code']);
    }

    //支付
    public function payIns()
    {
	    $res = $this->api->payIns();
        return response($res['data'], $res['code']);
    }

    //出单
    public function issue()
    {
        $res = $this->api->issue();
        return response($res['data'], $res['code']);
    }

    //状态
    public function orderStatus()
    {
        $res = $this->api->orderStatus();
        return response($res['data'], $res['code']);
    }

    //接口信息
    public function getApiOption()
    {
        $res = $this->api->getApiOption();
        //团险模板地址
        if(!is_array($res['data']) && is_null(json_decode($res['data']))){
            $res['data'] = json_decode($res['data'], true);
            $res['data']['template_url'] = $this->insurance_api_from->template_url;
            $res['data']['first_date'] = $this->insurance_api_from->insurance->first_date;
            $res['data']['latest_date'] = $this->insurance_api_from->insurance->latest_date;
            $res['data']['observation_period'] = $this->insurance_api_from->insurance->observation_period;
            $res['data']['period_hesitation'] = $this->insurance_api_from->insurance->period_hesitation;
        }

        return response($res['data'], $res['code']);
    }

    //获取健康告知
    public function getHealthStatement()
    {
        $res = $this->api->getHealthStatement();
        return response($res['data'], $res['code']);
    }

    //提交健康告知
    public function subHealthStatement()
    {
        $res = $this->api->subHealthStatement();
        return response($res['data'], $res['code']);
    }

    //支付方式
    public function getPayWayInfo()
    {
        $res = $this->api->getPayWayInfo();
        return response($res['data'], $res['code']);
    }
    //微信签约
    public function contractIns()
    {
        $res = $this->api->contractIns();
        return response($res['data'], $res['code']);
    }
    //微信代扣
    public function insWithhold()
    {
        $res = $this->api->insWithhold();
        return response($res['data'], $res['code']);
    }
    /****************************************************** 保全 ***************************************************/
    //退保
    public function insureCacel()
    {
        $res = $this->api->insureCacel();
        return response($res['data'], $res['code']);
    }
    /****************************************************** 理赔 ***************************************************/

    // 理赔 - 查询用户信息
    public function claimGetMemberInfo()
    {
        $res = $this->api->claimGetMemberInfo();
        return response($res['data'], $res['code']);
    }

    // 理赔 - 获取地区信息
    public function claimGetArea()
    {
        $res = $this->api->claimGetArea();
        return response($res['data'], $res['code']);
    }

    // 理赔 - 获取被保人信息
    public function claimGetInsurantInfo()
    {
        $res = $this->api->claimGetInsurantInfo();
        return response($res['data'], $res['code']);
    }

    // 获取验证码
    public function claimGetVerifyCode()
    {
        $res = $this->api->claimGetVerifyCode();
        return response($res['data'], $res['code']);
    }

    // 保存报案信息
    public function claimSaveCaseInfo()
    {
        $res = $this->api->claimSaveCaseInfo();
        return response($res['data'], $res['code']);
    }

    // 人伤理赔资料上传类型
    public function claimGetTKCDocType()
    {
        $res = $this->api->claimGetTKCDocType();
        return response($res['data'], $res['code']);
    }

    // 财产险上传资料描述
    public function claimGetTKAUploadDesc()
    {
        $res = $this->api->claimGetTKAUploadDesc();
        return response($res['data'], $res['code']);
    }

    // 进度
    public function claimGetProgress()
    {
        $res = $this->api->claimGetProgress();
        return response($res['data'], $res['code']);
    }

    // 资料操作
    public function claimHandleDocs()
    {
        $res = $this->api->claimHandleDocs();
        return response($res['data'], $res['code']);
    }

    // 获取详情
    public function claimGetDetail()
    {
        $res = $this->api->claimGetDetail();
        return response($res['data'], $res['code']);
    }

    // 申请提交
    public function claimSubmit()
    {
        $res = $this->api->claimSubmit();
        return response($res['data'], $res['code']);
    }

    // 补充资料
    public function claimSubmitAppend()
    {
        $res = $this->api->claimSubmitAppend();
        return response($res['data'], $res['code']);
    }

    /****************************************************** 理赔 end ***************************************************/
}
