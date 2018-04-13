<?php

//use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
//,
Route::any('/','ApiControllers\IndexController@index');
Route::any('/test_sign','ApiControllers\IndexController@testSign');//todo delete
Route::group(['namespace'=>'ApiControllers', 'middleware'=>'check.api.sign'],function(){

    //产品
    Route::any('getdata','IndexController@getData');
    Route::any('getproducts','IndexController@getProducts');
    Route::any('getproductinfo','IndexController@getProductInfo');

    //信息
    Route::any('passservice','IntersController@passService');
    Route::any('paysms','IntersController@paySms');
    Route::any('dosms','IntersController@doSms');

    //责任接口
    Route::post('duty/{id?}','InsDutyController@getDutyData')->where('id','[0-9]+');
    //参数接口
    Route::post('parameter_analysis','ParameterAnalysisController@getParameter');

    Route::any('sendemails','IntersController@sendEmails');

    Route::any('sendemailfiles','IntersController@sendEmailFiles');
    Route::any('saveemails','IntersController@saveEmails');
    Route::any('sendemailfiles','IntersController@sendEmailFiles');


    Route::post('/', 'IndexController@index');
    Route::any('insurances', 'InsuranceController@index');
    Route::post('check_ins', 'InsCurlController@checkIns');
    //工单
    Route::any('doaddspecial','SpecialsController@doAddSpecial');

    //在线客服
    Route::post('getonlines','IntersController@getOnlines');

    //发起理赔
    Route::any('saveclaim','ClaimController@saveClaim');
    Route::any('doupdateclaimstatus','ClaimController@doUpdateClaimStatus');//改变理赔状态

    //发起保全
    Route::any('saveformchange','PreservationController@saveFormChange');
    Route::any('doupdatemaintenance','PreservationController@doUpdateMaintenance');
    //获取渠道信息（韵达）
    Route::any('getfinfo','ChannelController@getChannelInfo');
    Route::any('getchannelinfo','ChannelController@getChannelInfo');



    Route::group(['prefix' => 'ins_curl'], function() {
        Route::post('quote', 'InsCurlController@quote');//算费
        Route::post('buy_ins', 'InsCurlController@buyIns');
        Route::post('check_ins', 'InsCurlController@checkIns');//核保
        Route::post('get_pay_way_info', 'InsCurlController@getPayWayInfo');//获取支付方式
        Route::post('pay_ins', 'InsCurlController@payIns');//支付
        Route::any('issue', 'InsCurlController@issue');//出单
        Route::any('getapioption','InsCurlController@getApiOption');//获取产品详情
        Route::any('contract_ins','InsCurlController@contractIns');//签约接口
        Route::any('wechat_pay_ins','InsCurlController@insWithhold');//微信代扣接口
        Route::any('cacel','InsCurlController@insureCacel');//退保接口

        Route::post('get_health_statement', 'InsCurlController@getHealthStatement');//获取健康告知
        Route::post('sub_health_statement', 'InsCurlController@subHealthStatement');//提交健康告知
    });

    // 理赔
    Route::group(['prefix' => 'claim'], function() {
        // 用户信息查询接口
        Route::any('get_member_info', 'InsCurlController@claimGetMemberInfo');
        // 地区信息
        Route::any('get_area', 'InsCurlController@claimGetArea');
        // 获取被保人信息
        Route::any('get_insurant_info', 'InsCurlController@claimGetInsurantInfo');
        // 获取验证码
        Route::any('get_verify_code', 'InsCurlController@claimGetVerifyCode');
        // 报案信息提交
        Route::any('save_case_info', 'InsCurlController@claimSaveCaseInfo');
        // 人伤险理赔资料上传类型
        Route::any('get_tkc_doc_type', 'InsCurlController@claimGetTKCDocType');
        // 财产险上传资料描述
        Route::any('get_tka_upload_desc', 'InsCurlController@claimGetTKAUploadDesc');
        // 理赔资料上传、删除、回显
        Route::any('handle_docs', 'InsCurlController@claimHandleDocs');
        // 理赔进度查询
        Route::any('get_progress', 'InsCurlController@claimGetProgress');
        // 理赔详情
        Route::any('get_detail', 'InsCurlController@claimGetDetail');
        // 申请提交接口 - 最终提交
        Route::any('submit', 'InsCurlController@claimSubmit');
        // 补充资料提交
        Route::any('submit_append', 'InsCurlController@claimSubmitAppend');
    });
//TODO  测试Email
    Route::any('do_email','IntersController@doEmail');
});

//惠泽回调
Route::any('ins/qx/call_back', 'ApiControllers\Curls\QxInsCurlController@callBack');
//悟空回调
Route::any('ins/wk/call_back/check', 'ApiControllers\Curls\WkInsCurlController@checkCallBack');
Route::any('ins/wk/call_back/pay', 'ApiControllers\Curls\WkInsCurlController@payCallBack');
//泰康签约回调  todo  contractCallBack   微信签约回调
Route::any('ins/tk/call_back', 'ApiControllers\Curls\TkInsCurlController@contractCallBack');
//泰康支付回调  todo insWithholdCallBack  微信代扣回调
Route::any('ins/tk/pay_call_back', 'ApiControllers\Curls\TkInsCurlController@insWithholdCallBack');
//安心财支付回掉
Route::any('ins/axc/call_back', 'ApiControllers\Curls\AxcInsCurlController@payCallBack');
//易安支付回调测试
Route::any('ins/ya/call_back', 'ApiControllers\Curls\YaInsCurlController@payCallBack');
//众安回调
Route::any('ins/za/call_back', 'ApiControllers\Curls\ZaInsCurlController@payCallBack');
Route::any('ins/za/issue_push/call_back', 'ApiControllers\Curls\ZaInsCurlController@issuePushCallBack');    //众安推送T-1日出单和退保的数据
//众安退保测试 todo delete
Route::any('ins/za/test/reject_ins', 'ApiControllers\Curls\ZaInsCurlController@rejectIns');


// 2、	出险地区初始化接口(ok)
Route::any('ins/tk/claim/get_povince_city', 'ApiControllers\Curls\TkInsCurlController@claimGetPovinceCity');
// 4、	通过财产险保单号获取被保险人信息接口(ok)
Route::any('ins/tk/claim/get_insurant_msg', 'ApiControllers\Curls\TkInsCurlController@claimGetInsurantMsg');
// 5、	获取验证码接口（ok）
Route::any('ins/tk/claim/get_verify_code', 'ApiControllers\Curls\TkInsCurlController@claimGetVerifyCode');
// 8、	人伤理赔资料上传类型查询接口(ok)
Route::any('ins/tk/claim/get_doc_types', 'ApiControllers\Curls\TkInsCurlController@getDocTypes');
// 9、	通过财产险保单号查询上传资料描述接口(ok)
Route::any('ins/tk/claim/get_claim_img_msg', 'ApiControllers\Curls\TkInsCurlController@getClaimImgMsg');
// 15、	理赔详情查询接口（参数有问题）
Route::any('ins/tk/claim/get_apply_speed_query', 'ApiControllers\Curls\TkInsCurlController@claimDetail');
// 1、	会员绑定信息查询接口
Route::any('ins/tk/claim/is_member', 'ApiControllers\Curls\TkInsCurlController@claimIsMember');
// 报案(ok)
Route::any('ins/tk/claim/report_case_post', 'ApiControllers\Curls\TkInsCurlController@reportCasePost');
//Route::any('ins/tk/claim/tkc_info_save', 'ApiControllers\Curls\TkInsCurlController@claimTKCInfoSave');
//Route::any('ins/tk/claim/tka_info_save', 'ApiControllers\Curls\TkInsCurlController@claimTKAInfoSave');
/*
 * 10、	理赔资料上传接口
 * 11、	理赔资料删除接口
 * 12、	理赔资料回显接口
 */
Route::any('ins/tk/claim/tak_info_image', 'ApiControllers\Curls\TkInsCurlController@claimTKAInfoImage');
//Route::any('ins/tk/claim/delete_tak_info_image', 'ApiControllers\Curls\TkInsCurlController@claimTKAInfoImage');
// 13、	申请提交接口
Route::any('ins/tk/claim/submit', 'ApiControllers\Curls\TkInsCurlController@claimTKAInfoSubmit');
//Route::any('ins/tk/claim/submit', 'ApiControllers\Curls\TkInsCurlController@claimTKAInfoSubmit');

//todo delete tariff excel input
//Route::any('test_excel_axc_gy', 'ApiControllers\Curls\AxcInsCurlController@testExcelGy');
//Route::any('test_excel_axc_ty', 'ApiControllers\Curls\AxcInsCurlController@testExcelTy');
//Route::post('test_rs', 'ApiControllers\Curls\RsCurlController@test');

//车险
Route::group(['middleware'=>'check.api.sign'],function(){
    Route::any('insurance_car/car_info', 'ApiControllers\Curls\CarInsCurlController@getCarInfo');   //车辆车型信息查询
    Route::any('insurance_car/quote', 'ApiControllers\Curls\CarInsCurlController@quote');   //价格查询
    Route::any('insurance_car/city', 'ApiControllers\Curls\CarInsCurlController@getProvinces');   //省查询
    Route::any('insurance_car/city2', 'ApiControllers\Curls\CarInsCurlController@getCities');   //市查询
    Route::any('insurance_car/buy_ins', 'ApiControllers\Curls\CarInsCurlController@buyIns');   //投保
    Route::any('/insurance_car/card_ins_post', 'ApiControllers\Curls\CarInsCurlController@cardInfoPost');   //北京地区证件信息采集
    Route::any('/insurance_car/get_code_again', 'ApiControllers\Curls\CarInsCurlController@getCodeAgain');   //重获验证码
    Route::any('/insurance_car/verify_code', 'ApiControllers\Curls\CarInsCurlController@verifyCode');   //重获验证码
});
//车险回调
Route::any('insurance_car/call_back', 'ApiControllers\Curls\CarInsCurlController@callBack');   //核保回调



//todo  华贵人寿--测试
Route::any('check_insure_hg', 'ApiControllers\Curls\HgInsCurlController@buyIns');//核保 ok
Route::any('pay_insure_hg', 'ApiControllers\Curls\HgInsCurlController@payIns');//支付 ok
Route::any('issue_insure_hg', 'ApiControllers\Curls\HgInsCurlController@issue');//出单 ok
Route::any('tariff_execl_hg', 'ApiControllers\Curls\HgInsCurlController@testExcel');//处理费率表（Execl）OK
Route::any('insure_issue_callback_hg', 'ApiControllers\Curls\HgInsCurlController@issueCallBack');//出单回访 OK
Route::any('insure_issue_querycall_hg', 'ApiControllers\Curls\HgInsCurlController@issueCallBack');//出单回访查询
Route::any('cont_query', 'ApiControllers\Curls\HgInsCurlController@contQuery');//保单查询-用户信息 ok
Route::any('query_by_cont', 'ApiControllers\Curls\HgInsCurlController@queryByCont');//保单查询-报单号 ok
Route::any('call_back_hg', 'ApiControllers\Curls\HgInsCurlController@doExeclMsg');//测试路由  OK
Route::any('new_acc_hg', 'ApiControllers\Curls\HgInsCurlController@newAcc');//新单对账
Route::any('cancle_quote_hg', 'ApiControllers\Curls\HgInsCurlController@edorTrial');//退保试算 OK
Route::any('cancle_hg', 'ApiControllers\Curls\HgInsCurlController@insureCacel');//退保 ok
Route::any('cancel_notice_hg', 'ApiControllers\Curls\HgInsCurlController@edorConf');//退保通知 ok
Route::any('delete_prt_hg', 'ApiControllers\Curls\HgInsCurlController@deletePrt');//解锁操作 ok
Route::any('test_hg', 'ApiControllers\Curls\HgInsCurlController@Test');

//todo  英大测试路由
Route::any('ydth_test', 'ApiControllers\Curls\YdthInsCurlController@Test');
Route::any('ydth_test_call', 'ApiControllers\Curls\YdthInsCurlController@TestCallBack');
Route::any('ydth_encrypt', 'ApiControllers\Curls\YdthInsCurlController@index');

//TODO  泰康测试出单
Route::any('issue_tk', 'ApiControllers\Curls\TkInsCurlController@issue');






