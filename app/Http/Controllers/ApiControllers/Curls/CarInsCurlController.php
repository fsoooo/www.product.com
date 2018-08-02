<?php
/**
 * Created by PhpStorm.
 * User: xyn
 * Date: 2017/11/3
 * Time: 10:51
 */

namespace App\Http\Controllers\ApiControllers\Curls;


use App\Helper\LogHelper;
use DB;
use App\Models\User;
use App\Models\Policy;
use App\Models\Insure;
use App\Models\InsOrder;
use App\Models\Insurance;
use App\Models\ApiFrom;
use App\Models\OrderFinance;
use App\Models\InsApiBrokerage;
use App\Helper\ExcelHelper;
use Illuminate\Http\Request;
use App\Helper\RsaSignHelp;
use Ixudra\Curl\Facades\Curl;

class CarInsCurlController
{
    protected $request;
    protected $application_id;

    public function __construct(Request $request)
    {
        $this->sign_help = new RsaSignHelp();
        $this->request = $request;
        $this->application_id = 11;
    }

    protected function decodeOriginData()
    {
        $input = $this->request->all();
        //业务参数 解析出源数据json字符串
        $original_data_array = $this->sign_help->tyDecodeOriginData($input['biz_content']);
        return $original_data_array;
    }

    /**
     * 查询车辆信息
     */
    public function getCarInfo()
    {
        $input = $this->decodeOriginData();
        if(!isset($input['type']) || !isset($input['car_number']) || !in_array($input['type'], ['license', 'frame'])){
            return response('参数异常', 400);
        }

        $car_info = array();
        switch($input['type']){
            case 'license':
                $car_info = $this->getCarInfoByLicenseNo($input['car_number']);
//                $car_info = $this->getCarInfoByLicenseNo(111);
                break;
            case 'frame':
                $car_info = $this->getCarInfoByFrameNo($input['car_number']);
//                $car_info = $this->getCarInfoByFrameNo(11);
                break;
        }
        return response($car_info['data'], $car_info['code']);
    }

    /**
     * 通过车牌号查询车辆车型信息
     * @param $license_no
     * @return int
     */
    protected function getCarInfoByLicenseNo($license_no)
    {
        $data = array();
        //业务参数
        $data['data']['licenseNo'] = $license_no;   //车牌号码
        $data['data']['applicationID'] = $this->application_id; //请求方标识
        //基础参数
        $data['sendTime'] = date("Y-m-d H:i:s");    //请求时间
        $data['sign'] = $this->carInsSign($data);   //签名

//        $response = Curl::to('http://api-mock.ztwltech.com/apply-mock/v2.0/auto/vehicleAndModel')
//            ->returnResponseObject()
//            ->withData($data)
//            ->asJson()
//            ->withTimeout(60)
//            ->post();

        $str = '{
            "state": "1",
            "msg": "成功",
            "msgCode": null,
            "sendTime": "2017-05-25 15:53:39",
            "data": {
                "responseNo": "8f250190-31b9-4cf9-bea7-99f586ce31f1",
                "engineNo": "E34****5901",
                "licenseNo": "豫 JCC522",
                "frameNo": "LS5A3****A112011",
                "firstRegisterDate": "2014-05-12",
                " vehicleList ": [
                    {
                        "vehicleFgwCode": "SC7149A4",
                        "brandCode": "16a65866-4fe2-49d3-b9f9-bd512c3274f9",
                        "brandName": "长安",
                        "engineDesc": "1.4L",
                        "familyName": "长安 CX20",
                        "gearboxType": "手动档",
                        "remark": "手动档 天窗导航版 国Ⅳ",
                        "newCarPrice": "51900",
                        "purchasePriceTax": "54118",
                        "importFlag": "0",
                        "purchasePrice": "51900",
                        "seat": "5",
                        "standardName": "长安 SC7149A4 轿车",
                        "vehicleFgwName": " 长 安 ",
                        "parentVehName": "2014 款 天窗导航版"
                    },
                    {
                        "vehicleFgwCode": "SC7149A4",
                        "brandCode": "16a65866-4fe2-49d3-b9f9-bd512c3274f9",
                        "brandName": "长安",
                        "engineDesc": "1.4L",
                        "familyName": " 长 安 CX20",
                        "gearboxType": " 手 动 档 ",
                        "remark": "手动档 运动版 国Ⅳ",
                        "newCarPrice": "47900",
                        "purchasePriceTax": "49947",
                        "importFlag": "0",
                        "purchasePrice": "47900",
                        "seat": "5",
                        "standardName": "长安 SC7149A4 轿车",
                        "vehicleFgwName": " 长 安 ",
                        "parentVehName": "2014 款 运动版"
                    }
                ]
            }
        }';
        $return = json_decode($str, true);
        return ['data'=>$return, 'code'=>200];
    }

    /**
     * 通过机架号查询车辆车型信息
     * @param $frame_no
     * @return int
     */
    protected function getCarInfoByFrameNo($frame_no)
    {
        $data = array();
        //业务参数
        $data['data']['frameNo'] = $frame_no;   //机架号码
        $data['data']['applicationID'] = $this->application_id; //请求方标识
        //基础参数
        $data['sendTime'] = date("Y-m-d H:i:s");    //请求时间
        $data['sign'] = $this->carInsSign($data);   //签名

//        通过机架号查询车辆信息
//        $response = Curl::to('http://api-mock.ztwltech.com/apply-mock/v2.0/auto/vehicleInfoByFrameNo')
//            ->returnResponseObject()
//            ->withData($data)
//            ->asJson()
//            ->withTimeout(60)
//            ->post();
        //解析成功的返回结果
//        $response = array();
//        $response['responseNo'] = "8f250190-31b9-4cf9-bea7-99f586ce31f1"; //响应码
//        $response['engineNo'] = 'E34B****01'; //发动机号
//        $response['licenseNo'] = "豫 JCC522";   //车牌号
//        $response['frameNo'] = "LS5A3****EA112011"; //车架号
//        $response['firstRegisterDate'] = "2014-05-12";   //初登日期
        $str = '{
                "state": "1",
                "msg": "成功",
                "msgCode": null,
                "sendTime": "2017-05-25 15:31:20",
                "sign": "dXF3HtPTsrYIGXL7VAyrWpRPZxjnDGQEHv7L4PJQBgx",
                "data": {
                    "responseNo": "8f250190-31b9-4cf9-bea7-99f586ce31f1",
                    "engineNo": "6454****65",
                    "licenseNo": "豫 JCC522",
                    "frameNo": "LHGGM26****018605",
                    "firstRegisterDate": "2014-05-12"
                }
            }';
        $response = json_decode($str, true);
        $return = array();
        $return['state'] = $response['state'];
        $return['msg'] = $response['msg'];
        $return['sendTime'] = $response['sendTime'];  //接口返回时间

        //车辆信息返回 封装
        $return['data']['responseNo'] = $response['data']['responseNo']; //响应码
        $return['data']['engineNo'] = $response['data']['engineNo'];   //发动机号
        $return['data']['licenseNo'] = $response['data']['licenseNo'];  //车牌号
        $return['data']['frameNo'] = $response['data']['frameNo'];    //车架号
        $return['data']['firstRegisterDate'] = $response['data']['firstRegisterDate'];    //初登日期


        //模糊获取车型信息
        $data = array();
        //业务参数
        $data['data']['brandName'] = "CSA7182AB";   //通过驾驶证上的品牌型号进行检索
        $data['data']['row'] = 10;   //每页显示行数
        $data['data']['page'] = 1;   //当前页
        $data['data']['applicationID'] = $this->application_id; //请求方标识
        //基础参数
        $data['sendTime'] = date("Y-m-d H:i:s");
        $data['sign'] = $this->carInsSign($data);   //签名
//        通过机架号查询车辆信息
//        $response = Curl::to('http://api-mock.ztwltech.com/apply-mock/v2.0/auto/modelMistiness')
//            ->returnResponseObject()
//            ->withData($data)
//            ->asJson()
//            ->withTimeout(60)
//            ->post();
        $str = '{
            "state": "1",
            "msg": "成功",
            "msgCode": null,
            "sendTime": "2017-05-25 10:10:52",
            "sign": "rShwlZX22HCjDOd9WluV555fqeBHavgZR9DSp2M35xqJ7ibqZQd4IcOZUFMrtHDz+DrUxV0/9a9G\r\nlzhlLIctnTxPhr uQQ3zdL3wL30QV71L0XmxoT4NUEnRYT/dhZjDzhblSXmzxliKHz4eFRor+Usg2\r\nUbKQP0a8eICHFP3Q0pU=\r\n",
            "data": [
                {
                    "vehicleFgwCode": "CSA7182AB",
                    "brandCode": "edef4623-a95e-46b9-8db6-316b13cf3b93",
                    "brandName": "荣威 CSA7182AB 轿车",
                    "engineDesc": "1.8L",
                    "familyName": "荣威 550",
                    "gearboxType": "手自一体",
                    "remark": "手自一体 启臻版 DWT 国ⅢOBD",
                    "newCarPrice": "140800",
                    "purchasePriceTax": "152800",
                    "importFlag": "0",
                    "purchasePrice": "140800",
                    "seat": "5",
                    "standardName": "荣威 CSA7182AB 轿车"
                },
                {
                    "vehicleFgwCode": "CSA7182AB",
                    "brandCode": "a02e9873-671c-47f3-aa76-6e0dba35ea62",
                    "brandName": "荣威 CSA7182AB 轿车",
                    "engineDesc": "1.8L",
                    "familyName": "荣威 550",
                    "gearboxType": "手自一体",
                    "remark": "手自一体 启智版 DWT 国ⅢOBD",
                    "newCarPrice": "129800",
                    "purchasePriceTax": "140900",
                    "importFlag": "0",
                    "purchasePrice": "129800",
                    "seat": "5",
                    "standardName": "荣威 CSA7182AB 轿车"
                }
            ]
        }';
        $response = json_decode($str, true);
        $return['data'][' vehicleList'] = $response['data'];
        return ['data'=>$return, 'code'=>200];
    }

    /**
     * 查询险别信息
     */
    public function getClauseInfo()
    {
        $input = $this->decodeOriginData();
        if(!isset($input['msg']) || ($input['msg'] != 'getClauseInfo')){
            return response('参数异常', 400);
        }
        $data = array();
        //业务参数
        $data['data']['applicationID'] = $this->application_id; //请求方标识
        //基础参数
        $data['sendTime'] = date("Y-m-d H:i:s");    //请求时间
        $data['sign'] = $this->carInsSign($data);   //签名

//        $response = Curl::to('http://api-mock.ztwltech.com/apply-mock/v2.0/mdata/risks')
//            ->returnResponseObject()
//            ->withData($data)
//            ->asJson()
//            ->withTimeout(60)
//            ->post();
        $str = '{
            "state": "1",
            "msg": "成功",
            "msgCode": null,
            "sendTime": "2017-05-24 17:35:42",
            "sign": "VUKDGq9Qvfny1MxkgGB\r\nscDxQa08qOFnbON/7raukcAQZqK5nVLEDLtGKb23OdQWjdc/Kj6tGFUzMeXK5L25Kk eynZNobTd1\r\nnax2krLBmHWS1vOzwBk=\r\n",
            "data": [
                {
                    "coverageCode": "A",
                    "coverageName": "机动车损失保险",
                    "insuredAmount": "Y"
                },
                {
                    "coverageCode": "D3",
                    "coverageName": "车上人员责任保险(驾驶员)",
                    "insuredAmount": "10000,20000,30000,50000,100000"
                }
            ]
        }';
        $response = json_decode($str, true);
        unset($response['sign']);
        return ['data'=>$response, 'status'=>200];
    }

    /**
     * 获取下起起保日期
     */
    public function getNextInsTime()
    {
        $input = $this->decodeOriginData();
//        dd($input);
//        $str = '{
//            "responseNo": "8f250190-31b9-4cf9-bea7-99f586ce31f1",
//            "licenseNo": "豫 JCC522",
//            "frameNo": "",
//            "brandCode": "16a65866-4fe2-49d3-b9f9-bd512c3274f9",
//            "engineNo": "",
//            "isTrans": "0",
//            "transDate": "",
//            "cityCode": "150200",
//            "ownerName": "张三",
//            "ownerMobile": "13323244424",
//            "ownerID": "130223198402134532",
//            "firstRegisterDate": "2015-09-17"
//        }';
//        $input = json_decode($str, true);
        //业务参数
        if(!isset($input['licenseNo']) || !isset($input['brandCode']) || !isset($input['isTrans']) || !isset($input['cityCode']) || !isset($input['firstRegisterDate'])){
            return response('参数异常', 400);
        }

        $data['data']['applicationID'] = $this->application_id; //请求方标识
        $data['data']['responseNo'] = isset($input['responseNo']) ? $input['responseNo'] : ''; //响应码
        $data['data']['licenseNo'] = $input['licenseNo']; //车牌号码
        $data['data']['frameNo'] = isset($input['frameNo']) ? $input['frameNo'] : ''; //车架号
        $data['data']['brandCode'] = $input['brandCode']; //品牌型号代码
        $data['data']['engineNo'] = isset($input['engineNo']) ? $input['engineNo'] : ''; //发动机号码
        $data['data']['isTrans'] = $input['isTrans']; //是否过户车
        $data['data']['transDate'] = $input['transDate']; //过户日期
        $data['data']['cityCode'] = $input['cityCode']; //机构代码
//        $data['data']['ownerName'] = $input['ownerName']; //车主姓名
//        $data['data']['ownerMobile'] = $input['ownerMobile']; //车主手机号
//        $data['data']['ownerID'] = $input['ownerID']; //车主身份证号
        $data['data']['firstRegisterDate'] = $input['firstRegisterDate']; //初登日期
        //基础参数
        $data['sendTime'] = date("Y-m-d H:i:s");    //请求时间
        $data['sign'] = $this->carInsSign($data);   //签名

//        $response = Curl::to('http://api-mock.ztwltech.com/apply-mock/v2.0/assist/effectiveDate')
//            ->returnResponseObject()
//            ->withData($data)
//            ->asJson()
//            ->withTimeout(60)
//            ->post();
        $str = '{
            "state": "1",
            "msg": "成功",
            "msgCode": null,
            "sendTime": "2017-05-27 17:00:45",
            "sign": "U4Wj5pUfYHVYWofQlDOFsImQkhGgpTUhr73ZbKYPzUG7ZpkDTLyl3tRJkMQzIPIvu8KCsCtxYvU7\r\n3eJfDyJdqc6wo UhJaWjV0vPFYEyNXDfLpUAMqgn5WOF3X0DhBcfQ/ajNiCRPcOC4FhbOWs3BHGEk\r\nH69Yn+2yTPkxp3tRqo8=\r\n",
            "data": {
                "ciLastEffectiveDate": "2018-05-01",
                "biLastEffectiveDate": "2018-05-01"
            }
        }';
        $return = json_decode($str, true);
        unset($return['sign']);
        return response($return, 200);
    }

    /**
     * 省份编码
     */
    public function getProvinces()
    {
        $input = $this->decodeOriginData();
        if(!isset($input['msg']) || ($input['msg'] != 'getProvinces')){
            return response('参数异常', 400);
        }

        $data = array();
        //业务参数
        $data['data']['applicationID'] = $this->application_id; //请求方标识
        //基础参数
        $data['sendTime'] = date("Y-m-d H:i:s");    //请求时间
        $data['sign'] = $this->carInsSign($data);   //签名

//        省份信息
//        $response = Curl::to('http://api-mock.ztwltech.com/apply-mock/v2.0/mdata/provinces')
//            ->returnResponseObject()
//            ->withData($data)
//            ->asJson()
//            ->withTimeout(60)
//            ->post();
        $str = '{
            "state": "1",
            "msg": "成功", "msgCode": null,
            "sendTime": "2017-05-23 10:05:59",
            "data": [
                {"provinceCode": "210200", "provinceName": "大连"},
                {"provinceCode": "330200", "provinceName": "宁波"},
                {"provinceCode": "370200", "provinceName": "青岛"},
                {"provinceCode": "440300", "provinceName": "深圳"},
                {"provinceCode": "350200", "provinceName": "厦门"},
                {"provinceCode": "340000", "provinceName": "安徽"},
                {"provinceCode": "350000", "provinceName": "福建"},
                {"provinceCode": "620000", "provinceName": "甘肃"},
                {"provinceCode": "440000", "provinceName": "广东"},
                {"provinceCode": "450000", "provinceName": "广西"},
                {"provinceCode": "520000", "provinceName": "贵州"},
                {"provinceCode": "460000", "provinceName": "海南"},
                {"provinceCode": "130000", "provinceName": "河北"},
                {"provinceCode": "410000", "provinceName": "河南"},
                {"provinceCode": "230000", "provinceName": "黑龙江"},
                {"provinceCode": "420002", "provinceName": "湖北"},
                {"provinceCode": "430000", "provinceName": "湖南"},
                {"provinceCode": "220000", "provinceName": "吉林"},
                {"provinceCode": "320000", "provinceName": "江苏"},
                {"provinceCode": "360000", "provinceName": "江西"},
                {"provinceCode": "210000", "provinceName": "辽宁"},
                {"provinceCode": "150000", "provinceName": "内蒙古"},
                {"provinceCode": "640000", "provinceName": "宁夏"},
                {"provinceCode": "630000", "provinceName": "青海"},
                {"provinceCode": "370000", "provinceName": "山东"},
                {"provinceCode": "140000", "provinceName": "山西"},
                {"provinceCode": "610000", "provinceName": "陕西"},
                {"provinceCode": "310000", "provinceName": "上海"},
                {"provinceCode": "510000", "provinceName": "四川"},
                {"provinceCode": "120000", "provinceName": "天津"},
                {"provinceCode": "540000", "provinceName": "西藏"},
                {"provinceCode": "650000", "provinceName": "新疆"},
                {"provinceCode": "530000", "provinceName": "云南"},
                {"provinceCode": "330000", "provinceName": "浙江"},
                {"provinceCode": "500000", "provinceName": "重庆"}
            ]
        }';
        $response = json_decode($str ,true);
        return response($response, 200);
    }

    public function getCities()
    {
        $input = $this->decodeOriginData();
        if(!isset($input['provinceCode']))
            return response('参数异常', 400);
        $data = array();
        //业务参数
        $data['data']['applicationID'] = $this->application_id; //请求方标识
        $data['data']['provinceCode'] = '213123'; //省国标码
        //基础参数
        $data['sendTime'] = date("Y-m-d H:i:s");    //请求时间
        $data['sign'] = $this->carInsSign($data);   //签名

//        城市信息
//        $response = Curl::to('http://api-mock.ztwltech.com/apply-mock/v2.0/mdata/cities')
//            ->returnResponseObject()
//            ->withData($data)
//            ->asJson()
//            ->withTimeout(60)
//            ->post();
        $str = '{
            "state": "1",
            "msg": "成功",
            "msgCode": null,
            "sendTime": "2017-05-24 11:30:10",
            "sign": "eDGP/UJw+6WieX8nNXwvakKs/jGnM\r\n5yhIftemouX2bi4nDV6KyzUnCkikTIxJ8WAyIVJKy08gaeMCozbRZeI3UQCY a1jj86/y8qr4C6cw\r\ngt7sQkF1lgOmyqkVQv8=\r\n",
            "data": [
                {
                    "cityCode": "130100",
                    "cityName": "石家庄市",
                    "cityPlate": "冀 A",
                    "countyList": [
                        {
                            "countyCode": "130101",
                            "countyName": "市辖区"
                        },
                        {
                            "countyCode": "130102",
                            "countyName": "长安区"
                        }
                    ]
                },
                {
                    "cityCode": "130200",
                    "cityName": "唐山市",
                    "cityPlate": "冀 B",
                    "countyList": [
                        {
                            "countyCode": "130201",
                            "countyName": "市辖区"
                        },
                        {
                            "countyCode": "130202",
                            "countyName": "路南区"
                        },
                        {
                            "countyCode": "130203",
                            "countyName": "路北区"
                        }
                    ]
                }
            ]
        }';
        $response = json_decode($str, true);
        unset($response['sign']);
        return response($response, 200);
    }



    /**
     * 算费
     */
    public function quote()
    {
        $input = $this->decodeOriginData();
        if(!isset($input['thpBizID']) || !isset($input['cityCode']) || !isset($input['biBeginDate']) || !isset($input['ciBeginDate']) || !isset($input['carInfo'])
            || !isset($input['carInfo']) || !isset($input['coverageList'])){
            return response('参数异常', 400);
        }
        $data = array();
        //业务参数
        $data['data']['applicationID'] = $this->application_id; //请求方标识
        $data['data']['provinceCode'] = '213123'; //省国标码

        //基础参数
        $data['sendTime'] = date("Y-m-d H:i:s");    //请求时间
        $data['sign'] = $this->carInsSign($data);   //签名

//        省份信息
//        $response = Curl::to('http://api-mock.ztwltech.com/apply-mock/v2.0/main/exactnessQuote')
//            ->returnResponseObject()
//            ->withData($data)
//            ->asJson()
//            ->withTimeout(60)
//            ->post();

        $str = '{
            "state": "1",
            "msg": "成功",
            "msgCode": null,
            "sendTime": "2017-06-09 11:16:40",
            "data": [
                {
                    "state": "1",
                    "msg": " 成 功 ",
                    "msgCode": null,
                    "bizID": "30282609",
                    "thpBizID": "201607111638024824",
                    "insurerCode": "ASTP",
                    "channelCode": "AS_INSURE",
                    "biBeginDate": "2017-08-01",
                    "biPremium": "2758.85",
                    "ciBeginDate": "2017-08-01",
                    "ciPremium": "950.00",
                    "carshipTax": "360.00",
                    "integral": "856.16",
                    "cIntegral": "3.00",
                    "bIntegral": "30.00",
                    "showCiCost": "3.0",
                    "showBiCost": "30.0",
                    "showSumIntegral": "856.16",
                    "bjCodeFlag": "",
                    "coverageList": [
                        {
                            "coverageCode": "MD3",
                            "coverageName": "不计免赔险(驾驶员责任险)",
                            "insuredAmount": "Y",
                            "insuredPremium": "3.19",
                            "flag": null,
                            "amount": ""
                        },
                        {
                            "coverageCode": "MD4",
                            "coverageName": "不计免赔险(乘客责任险)",
                            "insuredAmount": "Y",
                            "insuredPremium": "8.20",
                            "flag": null,
                            "amount": ""
                        },
                        {
                            "coverageCode": "L",
                            "coverageName": "车身划痕损失险",
                            "insuredAmount": "2000",
                            "insuredPremium": "202.35",
                            "flag": null,
                            "amount": "2000"
                        },
                        {
                            "coverageCode": "F",
                            "coverageName": "玻璃单独破碎险",
                            "insuredAmount": "Y",
                            "insuredPremium": "92.88",
                            "flag": null,
                            "amount": ""
                        },
                        {
                            "coverageCode": "Z",
                            "coverageName": "自燃损失险",
                            "insuredAmount": "Y",
                            "insuredPremium": "48.38",
                            "flag": null,
                            "amount": "79682.40"
                        },
                        {
                            "coverageCode": "MG1",
                            "coverageName": "不计免赔险（全车盗抢保险）",
                            "insuredAmount": "Y",
                            "insuredPremium": "51.65",
                            "flag": null,
                            "amount": ""
                        },
                        {
                            "coverageCode": "ML",
                            "coverageName": "不计免赔险（车身划痕损失险）",
                            "insuredAmount": "Y",
                            "insuredPremium": "30.36",
                            "flag": null,
                            "amount": ""
                        },
                        {
                            "coverageCode": "MA",
                            "coverageName": "不计免赔险(机动车损失保险)",
                            "insuredAmount": "Y",
                            "insuredPremium": "34.83",
                            "flag": null,
                            "amount": ""
                        },
                        {
                            "coverageCode": "G1",
                            "coverageName": "全车盗抢险",
                            "insuredAmount": "Y",
                            "insuredPremium": "258.22",
                            "flag": null,
                            "amount": "79682.40"
                        },
                        {
                            "coverageCode": "A",
                            "coverageName": "机动车损失保险",
                            "insuredAmount": "Y",
                            "insuredPremium": "1053.50",
                            "flag": null,
                            "amount": "79682.40"
                        },
                        {
                            "coverageCode": "FORCEPREMIUM",
                            "coverageName": " 交 强 险 ",
                            "insuredAmount": "Y",
                            "insuredPremium": "950.00",
                            "flag": null,
                            "amount": ""
                        },
                        {
                            "coverageCode": "D3",
                            "coverageName": "驾驶员责任险",
                            "insuredAmount": "10000",
                            "insuredPremium": "21.25",
                            "flag": null,
                            "amount": "10000"
                        },
                        {
                            "coverageCode": "MB",
                            "coverageName": "不计免赔险(商业第三者责任险)",
                            "insuredAmount": "Y",
                            "insuredPremium": "117.32",
                            "flag": null,
                            "amount": ""
                        },
                        {
                            "coverageCode": "B",
                            "coverageName": "商业第三者责任险",
                            "insuredAmount": "500000",
                            "insuredPremium": "782.08",
                            "flag": null,
                            "amount": "500000"
                        },
                        {
                            "coverageCode": "D4",
                            "coverageName": "乘客责任险",
                            "insuredAmount": "10000",
                            "insuredPremium": "54.64",
                            "flag": null,
                            "amount": "10000"
                        }
                    ],
                    "spAgreement": [
                        {
                            "spaCode": "",
                            "spaName": "为更好地保障您的权益,请您在发生保险事故后保留出险第一现场,并立即致电 24 小时服务电话:95589。",
                            "spaContent": "第一现场报案",
                            "riskCode": "0528"
                        }
                    ]
                }
            ]
        }';
        $response = json_decode($str, true);
        dd($response);

    }

    /**
     * 投保
     */
    public function buyIns()
    {
        $input = $this->decodeOriginData();
        if(!isset($input['bizID']) || !isset($input['addresseeName']) || !isset($input['addresseeMobile']) || !isset($input['addresseeDetails']) || !isset($input['addresseeCounty'])
            || !isset($input['channelCode']) || !isset($input['addresseeCity']) || !isset($input['addresseeProvince']) || !isset($input['policyEmail'])){
            return response('参数异常', 400);
        }
        $data = array();
        //业务参数
        $data['data']['applicationID'] = $this->application_id; //请求方标识
        $data['data']['provinceCode'] = '213123'; //省国标码
        //基础参数
        $data['sendTime'] = date("Y-m-d H:i:s");    //请求时间
        $data['sign'] = $this->carInsSign($data);   //签名
//        省份信息
//        $response = Curl::to('http://api-mock.ztwltech.com/apply-mock/v2.0/main/applyUnderwrite')
//            ->returnResponseObject()
//            ->withData($data)
//            ->asJson()
//            ->withTimeout(60)
//            ->post();

        $str = '{
            "state": "1",
            "msg": "成功",
            "msgCode": null,
            "sendTime": "2017-06-09 11:00:42",
            "sign": "oIfhGoCwWWmRKaQRPMMVPjcxoVbB+QAJzkQPx5ILI+RPRLz8TsOf/mZVAWWVMPPmM3GJNp+amMO7\r\ntmiV 30lmOp+AEkSTU344yAJ42laZpi3QqIf7TgsMJM3h26lbyN1JHnWjp4yg+hXYY78YFioq65xZ\r\np3sWTCVXrR/tWxQqS 9w=\r\n",
            "data": {
                "biProposalNo": "T085105282016075984",
                "ciProposalNo": "T085105072016817758",
                "payLink": "http://api-mock.ztwltech.com/zkyq-web/payments/pay?baseId=30282536",
                "synchFlag": "0",
                "bjCodeFlag": "",
                "bizID": "30282536",
                "thpBizID": "201607111638024824",
                "operType": null
            }
        }';
        $response = json_decode($str, true);
        unset($response['sign']);
        return response($response, 200);
    }



    /**
     * 核保回掉处理
     */
    public function checkCallBack()
    {
        $str = '{
            "msg": "回写核保信息报文",
            "sendTime": "2010-10-1000: 00: 00",
            "state": "1",
            "data": {
                "operType": "1",
                "thpBizID": "T050720151207105201",
                "bizID": "2312343231230720151207105201",
                "biProposalNo": "T050720151207105201",
                "ciProposalNo": "T050720151207105202",
                "payLink": "http: //138.10.10.11: 2357/zkyq/servelet",
                "expiredTime": "2016-07-2718: 00: 00"
            }
        }';
        $response = json_decode($str, true);
        unset($response['msg']);
        dd($response);
    }


    public function payCallBack()
    {
        $str = '{
            "state": "1",
            "msg": "保单信息回写报文",
            "sendTime": "2016-05-0116: 10: 10",
            "data": {
                "operType": "2",
                "thpBizID": "T050720151207105201",
                "bizID": "T050720151207105201",
                "payState": "1",
                "payMoney": "4327.00",
                "payTime": "2016-05-0116: 10: 10",
                "biPolicyNo": "8828900000144889",
                "ciPolicyNo": "8828900000144889"
            }
        }';
        $response = json_decode($str, true);
        unset($response['msg']);
        dd($response);
    }

    /**
     * 省份查询可投保公司
     */
    public function insurers()
    {
        $input = $this->decodeOriginData();
        if(!isset($input['provinceCode']))
            return response('参数异常', 400);
        $data = array();
        //业务参数
        $data['data']['applicationID'] = $this->application_id; //请求方标识
        $data['data']['provinceCode'] = '213123223'; //省国标码
        //基础参数
        $data['sendTime'] = date("Y-m-d H:i:s");    //请求时间
        $data['sign'] = $this->carInsSign($data);   //签名
    //        $response = Curl::to('http://api-mock.ztwltech.com/apply-mock/v2.0/mdata/insurers')
//            ->returnResponseObject()
//            ->withData($data)
//            ->asJson()
//            ->withTimeout(60)
//            ->post();
        $str = '{
            "state": "1",
            "msg": "成功",
            "msgCode": null,
            "sendTime": "2017-05-24 16:56:39",
            "sign": "fOQsAfOQsAfOQsAfOQsAfOQsAfOQsA",
            "data": [
                {
                    "insurerCode": "YGBX",
                    "insurerName": "阳光保险"
                },
                {
                    "insurerCode": "CPIC",
                    "insurerName": "太保"
                }
            ]
        }';
        $response = json_decode($str, true);
        unset($response['sign']);
        return response($response, 200);
    }

    protected function carInsSign($data)
    {
        return $data;
    }
}