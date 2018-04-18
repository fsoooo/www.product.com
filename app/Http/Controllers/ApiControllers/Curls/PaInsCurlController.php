<?php
/**
 * Created by PhpStorm.
 * User: wangsl
 * Date: 2018/04/17
 * Time: 14:51
 * 平安健康险的接口：详情，算费，核保，支付，承保，出单等。
 *
 * 所有接口均以 HTTP /HTTPS方式进行调用（TODO 生产：HTTPS）
 * HTTP header:
 * Content-Type:application/json;charset=UTF-8
 * Accept:application/json;charset=UTF-8
 * 请求参数示例：
 * {
		"requestId":"请求编号，以便区分请求，参考格式：渠道代码_时间戳，可不传",
		"data":"加密之后的json串"
	}
 * 相应参数示例：
 * {
		"requestId":"与请求报文中的请求编号一致",
		"returnCode":"接入响应码",
		"returnMsg":"接入响应信息"
		"data": "加密之后的json串"
   }
 *
 * 产品信息-个险产品-平安e生保PLUS  A000000042  P005_01
 * 产品信息-团险产品-e企保（年保单）  A000000029  G005
 * 产品信息-团险产品-e企保（月保单）  A000000030  G006
 */
namespace App\Http\Controllers\ApiControllers\Curls;

use App\Helper\LogHelper;
use DB;
use App\Models\Insurance;
use App\Models\Tariff;
use App\Helper\ExcelHelper;
use Illuminate\Http\Request;
use App\Helper\RsaSignHelp;
use Ixudra\Curl\Facades\Curl;
use SoapBox\Formatter\Formatter;
use App\Repositories\RestrictGeneRepository;
use App\Repositories\InsuranceApiFromRepository;
use App\Repositories\InsuranceAttributesRepository;
use App\Http\Controllers\ApiControllers\Curls\WkInsCurlController;
use App\Helper\AesCrypt;
use App\Models\InsOrder;
use App\Models\ApiFrom;
use App\Models\Policy;
use App\Models\Insure;
use App\Models\User;
use App\Models\OrderFinance;
use App\Models\InsApiBrokerage;
use App\Helper\AesCryptHelper;


class PaInsCurlController
{
    protected $request;

    protected $charset;

    protected $encrypt;

    protected $retry_time;

    protected $sign_help;

    protected $private_key;

    protected $public_key;

    protected $wk_public_key;

    //接口地址(测试)
    const API_INSURE_URL = 'https://test-mobile.health.pingan.com:42443/ehis-hm';
    //渠道编码
    const API_CHANNEL_CODE = 'BJTYKJ';
    //渠道编号
    const API_CHANNEL_id = '710';
    //秘钥
    const API_INSURE_URL_KEY = '123456';
    //收银台APP秘钥
	const API_PAY_URL_KEY = '4OQ5RBPPJC5F8PUKXMUDGDU7G96BVHVW9FFW4AJVZ2KWH2W8WP9QSTNVQXEAOREURU3UQJ6N9CWPVM731K2SQDP5RZXBGII9MDJXIKJ9D4W9MSEZR3FQEQBTTD8L6MRY';
    //测试环境请求支付请求地址：(TODO GET)
    const API_PAY_URL = 'http://test-mobile.health.pingan.com/ehis-hm/cashier/pay.do';
    //生产环境查询支付请求地址:(TODO https)
    const API_PAY_URL_PRODUCT = 'https://mobile.health.pingan.com/ehis-hm/cashier/pay.do';

	/**
	 * 初始化
	 * @access public
	 *
	 */
	public function __construct(Request $request)
	{
		$this->request = $request;
		$this->charset = 'UTF-8';
		$this->encrypt = 'AES';
		$this->retry_time = '180';//重试时间间隔为3分钟
		$this->sign_help = new RsaSignHelp();
		$this->aes_crypt = new AesCrypt();
		$this->aes_crypt_helper = new AesCryptHelper();
		$this->private_key = file_get_contents('../config/rsa_private_key_1024_pkcs8.pem');
		$this->public_key = file_get_contents('../config/rsa_public_key_1024_pkcs8.pem');
		$this->wk_public_key = file_get_contents('../config/wk_rsa_public_key.pem');
	}

	/**
	 * 保费试算
	 * @access public
	 * @params productId|产品编号|String|是
	 * @params applyDate|申请日期（YYYY-MM-DD），用于计算年龄	|String|是 TODO 按照周岁计算年龄，即生日当天年龄不+1，生日第二天年龄才+1
	 * @params isShareCoverage|是否家庭共用（Y/N）|String|依产品形态
	 * @params premType|缴费频次|String|个险必传 1趸缴 2月缴 3季缴 4半年缴 5年缴 6趸缴累加
	 *
	 * @params insurants|被保人列表
							seqno|被保人序号，当有多人时，分别为1,2,3等。只有1人则默认为1|String|是
							birthday|生日（YYYY-MM-DD）|String|是
							sex|性别：M男F女	|String|是
							hasSocialSecurity|是否有社保(Y/N)|String|	依产品形态
							relationshipWithPrimaryInsurant|与第一被保险人关系|默认为”1”|依产品形态
	  						coverages|保障列表
												benLevel|档次|String|	是
												A000000042	P005_01	平安e生保PLUS	计划一	01	400000
																					计划二	02	1000000
																					计划三	03	2000000
																					计划四	04	6000000
												A000000029	G005	e企保（年保单）	经济款	01	59500
																					标准款	02	119000
																					豪华款	03	238000
												A000000030	G006	e企保（月保单）	经济款	01	59500
																					标准款	02	119000
																					豪华款	03	238000
												sumInsured|保额,单位元,整数|String|个险必传
												period|保障期间(月),默认12|String|个险必传
												periodDay|保障期间(天),默认为0|String|个险必传
	 * 保障期间问题
		period (保险期间月)periodDay（保险期间天），paymentPeriod（缴费期间月）,paymentPeriodDay（缴费期间天）
		其他非H804产品均为1年期，period和paymentPeriod为12，periodDay和paymentPeriodDay为0即可。
	 * @reponse returnCode|响应码：00成功；其它：失败	|String
	 * @reponse returnMsg|响应信息|String
	 * @reponse data TODO data需要解密
						totalPrem|总保费，单位元，2位小数	|String
						isShareCoverage|是否家庭共用	|String
	 					insurants(被保人列表)
												seqno 	被保人序号
												insurantPrem 	被保人保费，单位元，2位小数
	 * @requstr_type POST
	 * @return mixed
	 *
	 */
	public function quote()
	{
		$input = [];
		$input['productId'] = 'A000000042';
		$input['applyDate'] = date('Y-m-d',time());
		$input['isShareCoverage'] = 'N';
		$input['premType'] = '1';
		$input['insurants'][] = [
			'seqno'=>'1',
			'birthday'=>'1994-06-05',
			'sex'=>'M',//M男F女
			'hasSocialSecurity'=>'Y',//是否有社保(Y/N)
			'relationshipWithPrimaryInsurant'=>'1',//
			'coverages'=>[
				'0'=>[
					'benLevel'=>'02',
					'sumInsured'=>'1000000',
					'period'=>'12',//保障期间（月），默认12
					'periodDay'=>'0',//保障期间（天），默认为0
				],
			],
		];
//		dump($input);
		$request_data = [];
		$request_data['requestId'] =  self::API_CHANNEL_CODE.time();
		$key = self::API_INSURE_URL_KEY;//测试环境
		$input = json_encode($input,JSON_UNESCAPED_UNICODE);
		$input = $this->aes_crypt_helper->encrypt($input,$key);
		$request_data['data'] = $input;
		//dump(json_encode($request_data));
		$request_url = self::API_INSURE_URL.'/outChannel/calculatePremium.do?c='.self::API_CHANNEL_CODE;
		//dump($request_url);
		$response = Curl::to($request_url)
			->returnResponseObject()
			->withData($request_data)
			->withHeader("Content-Type:application/json;charset=UTF-8,Accept:application/json;charset=UTF-8")
			->asJson()
			->withTimeout($this->retry_time)
			->post();
//        dd($response);
		//失败返回
		if($response->status != 200)
			return ['data'=> 'default quote error', 'code'=> 400];
		if($response->content->returnCode != 00){
			LogHelper::logError($request_data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'pingan', 'insAttr');
			return json_encode(['data'=>$response->content->returnMsg, 'code'=> 400], JSON_UNESCAPED_UNICODE);
		}
		$data = array();
		$data['price'] = $response->content->data->totalPrem;//算费价格
		$data = json_encode($data, JSON_UNESCAPED_UNICODE);
		return ['data'=>$data, 'code'=>200];
	}

	/**
	 * 保险核保
	 * @access public
	 *
	 * @params productId|产品编号|String	|是
	 * @params outChannelOrderId|渠道方订单号|String|是 TODO VARCHAR2 (32)
	 * @params effDate|保单生效的日期，为投保日期+1YYYY-MM-DD	|String|是
	 * @params applyDate|即客户投保的日期，用于计算保费年龄|YYYY-MM-DD|String是
	 * @params totalPremium	|总保费，单位元，两位小数|String|是number(12,2)
	 *
	 * @params applicant|投保人,非企业投保人专用
	 * @params insurants|被保人列表
	 * @params channelInfo|渠道信息
	 * @params authInfo|授权信息
	 * @params serviceAgreementInfo|服务约定信息
	 * @requstr_type POST
	 * @return mixed
	 */
	public function checkIns()
	{
		$input = [];
		$input['productId'] = 'A000000042';//产品编码
		$input['applyDate'] = date('Y-m-d',time());
		$input['effDate'] = date('Y-m-d',strtotime('+1 day'));//保单生效日期
		$input['totalPremium'] = '249.00';
		$input['outChannelOrderId'] = '123456789';//渠道方订单号
		//投保人信息
		$input['applicant'] = [];
		$input['applicant']['name'] = '王石磊';
		$input['applicant']['idType'] = '1';
		$input['applicant']['idno'] = '410881199406056514';
		$input['applicant']['birthday'] = '1994-06-05';
		$input['applicant']['sex'] = 'M';
		$input['applicant']['contactInfo']['mobile'] = '157016811524';
		$input['applicant']['contactInfo']['email'] = 'wangsl@inschos.com';
		//被保人信息列表
		$input['insurants'] = [];
		$input['insurants'][0]['seqno'] = '12345678901';
		$input['insurants'][0]['name'] = '王石磊';
		$input['insurants'][0]['idType'] = '1';
		$input['insurants'][0]['idno'] = '410881199406056514';
		$input['insurants'][0]['birthday'] = '1994-06-05';
		$input['insurants'][0]['sex'] = 'M';
		$input['insurants'][0]['relationshipWithApplicant'] = '1';//与投保人关系1	本人2	配偶9	其他I	父母J	子女
		$input['insurants'][0]['relationshipWithPrimaryInsurant'] = '1';//与第一被保险人关系1	主被保险人2	配偶3	子女4	父母6	其他
		$input['insurants'][0]['contactInfo']['mobile'] = '15701681524';
		$input['insurants'][0]['contactInfo']['email'] = 'wangsl@inschos.com';
		$input['insurants'][0]['coverages'][0]['planType'] = '0';//险种类别（0主险/1附加险），默认为0
		$input['insurants'][0]['coverages'][0]['sumInsured'] = '1000000';//保额
		$input['insurants'][0]['coverages'][0]['benLevel'] = '02';//档次
		$input['insurants'][0]['coverages'][0]['period'] = '12';//保险期间（月）
		$input['insurants'][0]['coverages'][0]['periodDay'] = '0';//保险期间（天）
		$input['insurants'][0]['coverages'][0]['paymentPeriod'] = '12';//缴费期间（月）
		$input['insurants'][0]['coverages'][0]['paymentPeriodDay'] = '0';//缴费期间（天）
		$input['insurants'][0]['coverages'][0]['actualPrem'] = '249.00';//实际保费，单位元，小数点后两位
		$input['insurants'][0]['healthNotes'][0]['questionId'] = '1';//健康告知问题ID
		$input['insurants'][0]['healthNotes'][0]['answer'] = 'Y';//答案值Y/N
		$input['insurants'][0]['healthNotes'][0]['healthNoteSeq'] = '1';//告知批次号,1,2,3：如果接口方会记录且传给健康险历史告知记录，则该字段用于区分各批次健康告知，否则默认传值为
		//授权信息
		$input['authInfo'] = [];
		$input['authInfo']['initialChargeMode'] = '1';//首期收费方式
		//服务约定信息
		$input['serviceAgreementInfo'] = [];
		$input['serviceAgreementInfo']['premType'] = '1';//缴费频次,1	趸缴2	月缴3	季缴4	半年缴5	年缴6	趸缴累加
		$request_data = [];
		$request_data['requestId'] =  self::API_CHANNEL_CODE.time();
		$key = self::API_INSURE_URL_KEY;//测试环境
		$input = json_encode($input,JSON_UNESCAPED_UNICODE);
		dump($input);
		$input = $this->aes_crypt_helper->encrypt($input,$key);
		$request_data['data'] = $input;
		$request_url = self::API_INSURE_URL.'/outChannel/validate.do?c='.self::API_CHANNEL_CODE;;
		dump($request_data);
		$response = Curl::to($request_url)
			->returnResponseObject()
			->withData($request_data)
			->withHeader("Content-Type:application/json;charset=UTF-8,Accept:application/json;charset=UTF-8")
			->asJson()
			->withTimeout($this->retry_time)
			->post();
		dd($response);
	    //失败返回
		if($response->status != 200)
			return ['data'=> 'default quote error', 'code'=> 400];
		if($response->content->returnCode != 00){
			LogHelper::logError($request_data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'pingan', 'insAttr');
			return json_encode(['data'=>$response->content->returnMsg, 'code'=> 400], JSON_UNESCAPED_UNICODE);
		}
		$data = array();
		$data['price'] = $response->content->data->totalPrem;//算费价格
		$data = json_encode($data, JSON_UNESCAPED_UNICODE);
		return ['data'=>$data, 'code'=>200];
	}

	/**
	 * 保险支付-收银台
	 * 注意：此接口支持重复承保，如果渠道方接收响应超时或响应代码为3301或3302，请渠道方过3分钟后再试。
	 * @access public
	 * @params channel_order_no	String 最长20位字符	健康险订单号，来自核保	Y	0000000001
	 * @params goods_desc	String     最长500位字符	商品描述	Y	平安e家保
	 * @params total_fee	long 	   支付金额 精确到分	Y	100000
	 * @params channel_id	String	   渠道编码 固定值，向我方申请	Y	00001
	 * @params return_url	String     最长500位字符	支付完成后，页面跳转地址，目前只支持https	Y	https://xxxx/pay/result
	 * @params notify_url	String     最长500位字符	支付完成后，后台异步通知支付结果,目前只支持https(443端口)	Y	https://xxxx/pay/notify
	 * @params sign_type	String 	   加签类型，目前只支持SHA-256	Y	SHA-256
	 * @params sign	        String	   加签结果，将请求参数除去sign_type、sign，按字母升序排序后加上加密密钥，进行SHA-256加密	Y	e0afb5f18c969149a7d7d34205d8de26
	 * @requstr_type POST
	 * @return mixed
	 */
	public function payIns(){
		$input = [];
		$input['channel_order_no'] = '1234567890';//健康险订单号
		$input['goods_desc'] = '平安e家保';//商品描述
		$input['total_fee'] = '10000';//支付金额 精确到分
		$input['channel_id'] = self::API_CHANNEL_CODE;//渠道编码 固定值
		$input['return_url'] = 'https://dev308.inschos.com/pay/result';//支付完成后，页面跳转地址
		$input['notify_url'] = 'https://dev308.inschos.com/pay/notify';//支付完成后，后台异步通知支付结果
		$input['goods_desc'] = urlencode($input['goods_desc']);
		$input['return_url'] = urlencode($input['return_url']);
		$input['notify_url'] = urlencode($input['notify_url']);
		$data = $input;
		sort($input);//按字母升序排序
		$sign = hash("sha256", json_encode($input).self::API_PAY_URL_KEY);
		$data['sign_type'] = 'SHA-256';
		$data['sign'] = $sign;//将请求参数除去sign_type、sign，按字母升序排序后加上加密密钥，进行SHA-256加密	Y

		$request_url = self::API_PAY_URL.
			'?channel_id='.$data['channel_id'].
			'&channel_order_no='.$data['channel_order_no'].
			'&goods_desc='.$data['goods_desc'].
			'&notify_url='.$data['notify_url'].
			'&return_url='.$data['return_url'].
			'&sign='.$data['sign'].
			'&sign_type='.$data['sign_type'].
			'&total_fee='.$data['total_fee'];
		dump($data);
		dump($request_url);
	}

	/**
	 * 保险支付-支付回调
	 * 渠道方回调返回:TRUE/FALSE,为TRUE时,不再回调.为FALSE时，5分钟回调1次，最多50次
	 * @access public
	 * @params channel_order_no	String 最长20位字符	健康险订单号，来自核保	Y	0000000001
	 * @params channel_order_no	String 	健康险订单号	Y
	 * @params pay_serial_no	String 	收银台收单号	Y
	 * @params pay_status	String 	支付结果 00成功， 01失败	Y
	 * @params pay_time	String 	支付时间 格式：yyyy-MM-dd HH:mm:ss	Y
	 * @params total_fee	long	收款金额（需校验下，收款金额是否和订单金额相同，若不相同，不要承保）	Y
	 * @params pay_type	String 	支付类型：wx、yqb、yhk、zfb	Y
	 * @params sign_type	String 	加签类型，目前只支持SHA-256	Y
	 * @params sign	String 	加签结果，将请求参数除去sign_type、sign，按字母升序排序后加上加密密钥，进行SHA-256加密	Y
	 * @requstr_type POST
	 * @return mixed
	 */
	public function payCallBack(){
		$input  = $this->request->all();//TODO 解密？
		$channel_order_no = $input['channel_order_no'];//健康险订单号
		$pay_serial_no = $input['pay_serial_no'];//收银台收单号
		$pay_status = $input['pay_status'];//支付结果 00成功， 01失败
		$pay_time = $input['pay_time'];//支付时间 格式：yyyy-MM-dd HH:mm:ss
		$total_fee = $input['total_fee'];//收款金额（需校验下，收款金额是否和订单金额相同，若不相同，不要承保）
		$pay_type = $input['pay_type'];//支付类型：wx、yqb、yhk、zfb
		$sign_type = $input['sign_type'];//	加签类型，目前只支持SHA-256
		$sign = $input['sign'];//加签结果，将请求参数除去sign_type、sign，按字母升序排序后加上加密密钥，进行SHA-256加密
		if($pay_status!='00'){
			return false;
		}
		if($total_fee!=''){//收款金额是否和订单金额相同，若不相同，不要承保
			return false;
		}
		return true;

	}

	/**
	 * 保险承保-出单接口
	 * 注意：此接口支持重复承保，如果渠道方接收响应超时或响应代码为3301或3302，请渠道方过3分钟后再试。
	 * @access public
	 * @params orderId|健康险订单号|String|是
	 * @requstr_type POST
	 * @return mixed
	 */
	public function issue()
	{
		$input = [];
		$input['orderId'] = '111111111111111111111';//健康险订单号
		$request_data = [];
		$request_data['requestId'] =  self::API_CHANNEL_CODE.time();
		$key = self::API_INSURE_URL_KEY;//测试环境
		$input = json_encode($input);
		dump($input);
		$input = $this->aes_crypt_helper->encrypt($input,$key);
		$request_data['data'] = $input;
		dump(json_encode($request_data));
		$request_url = self::API_INSURE_URL.'/outChannel/accept.do?c='.self::API_CHANNEL_CODE;
		//dump($request_url);
		$response = Curl::to($request_url)
			->returnResponseObject()
			->withData($request_data)
			->withHeader("Content-Type:application/json;charset=UTF-8,Accept:application/json;charset=UTF-8")
			->asJson()
			->withTimeout($this->retry_time)
			->post();
		dd($response);
		//失败返回
		if($response->status != 200)
			return ['data'=> 'default quote error', 'code'=> 400];
		if($response->content->returnCode != 00){
			LogHelper::logError($request_data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'pingan', 'insAttr');
			return json_encode(['data'=>$response->content->returnMsg, 'code'=> 400], JSON_UNESCAPED_UNICODE);
		}
		$data = array();
		$data['price'] = $response->content->data->totalPrem;//算费价格
		$data = json_encode($data, JSON_UNESCAPED_UNICODE);
		return ['data'=>$data, 'code'=>200];
	}

	/**
	 * 保单查询
	 * @access public
	 * @URL	/outChannel/qryPolByPolNo.do?c=渠道代码
	 * @requstr_type POST
	 * @return mixed
	 */
	public function selPolicy(){
		$input = [];
		$input['policyNo'] = '111111111111111111111';//保单号
		$request_data = [];
		$request_data['requestId'] =  self::API_CHANNEL_CODE.time();
		$key = self::API_INSURE_URL_KEY;//测试环境
		$input = json_encode($input);
		dump($input);
		$input = $this->aes_crypt_helper->encrypt($input,$key);
		$request_data['data'] = $input;
		dump(json_encode($request_data));
		$request_url = self::API_INSURE_URL.'/outChannel/qryPolByPolNo.do?c='.self::API_CHANNEL_CODE;
		//dump($request_url);
		$response = Curl::to($request_url)
			->returnResponseObject()
			->withData($request_data)
			->withHeader("Content-Type:application/json;charset=UTF-8,Accept:application/json;charset=UTF-8")
			->asJson()
			->withTimeout($this->retry_time)
			->post();
		dd($response);
		//失败返回
		if($response->status != 200)
			return ['data'=> 'default quote error', 'code'=> 400];
		if($response->content->returnCode != 00){
			LogHelper::logError($request_data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'pingan', 'insAttr');
			return json_encode(['data'=>$response->content->returnMsg, 'code'=> 400], JSON_UNESCAPED_UNICODE);
		}
		$data = array();
		$data['price'] = $response->content->data->totalPrem;//算费价格
		$data = json_encode($data, JSON_UNESCAPED_UNICODE);
		return ['data'=>$data, 'code'=>200];
	}

	/**
	 * 撤件接口
	 * 注意：保单生效前客户撤件。
	 * @access public
	 * @URL	/outChannel/revokePolicy.do?c=渠道代码
	 * @requstr_type POST
	 * @return mixed
	 */
	public function cacelPolicy(){
		$input = [];
		$input['policyNo'] = '111111111111111111111';//保单号
		//退费方式，付款方式无需付款A: 渠道方退款，后继结算，下面的银行相关字段无需填
		//银行批扣C: 健康险退款，后继结算，下面的银行相关字段必须填
		$input['paymentMode'] = 'A';
//		$input['bankAccountNo'] = '111111111111111111111';//退保银行账号
//		$input['bankAccountName'] = '111111111111111111111';//开户人姓名
//		$input['bankDescription'] = '111111111111111111111';//保单号
		$request_data = [];
		$request_data['requestId'] =  self::API_CHANNEL_CODE.time();
		$key = self::API_INSURE_URL_KEY;//测试环境
		$input = json_encode($input);
		dump($input);
		$input = $this->aes_crypt_helper->encrypt($input,$key);
		$request_data['data'] = $input;
		dump(json_encode($request_data));
		$request_url = self::API_INSURE_URL.'/outChannel/revokePolicy.do?c='.self::API_CHANNEL_CODE;
		//dump($request_url);
		$response = Curl::to($request_url)
			->returnResponseObject()
			->withData($request_data)
			->withHeader("Content-Type:application/json;charset=UTF-8,Accept:application/json;charset=UTF-8")
			->asJson()
			->withTimeout($this->retry_time)
			->post();
		dd($response);
		//失败返回
		if($response->status != 200)
			return ['data'=> 'default quote error', 'code'=> 400];
		if($response->content->returnCode != 00){
			LogHelper::logError($request_data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'pingan', 'insAttr');
			return json_encode(['data'=>$response->content->returnMsg, 'code'=> 400], JSON_UNESCAPED_UNICODE);
		}
		$data = array();
		$data['price'] = $response->content->data->totalPrem;//算费价格
		$data = json_encode($data, JSON_UNESCAPED_UNICODE);
		return ['data'=>$data, 'code'=>200];
	}

	/**
	 * 电子保单下载
	 * @access public
	 * @URL /outChannel/downloadPolicy.do?c=渠道代码&policyNo= URLEncoder.encode(加密之后的保单号, "utf-8");
	 * @requstr_type 访问方式	GET
	 * @return mixed
	 */
	public function downloadPolicy(){
		$warranty_code = '123456789';
		$request_url = self::API_INSURE_URL.'/outChannel/downloadPolicy.do?c='.self::API_CHANNEL_CODE.'&policyNo='.urlencode($warranty_code);
		dump($request_url);
	}


//	=============================================================续保接口(Renewal)===============================================================
	/**
	 * 续保保单查询
	 * @access public
	 * @URL outChannel/renewal/queryByPolicyNo.do?c=渠道代码&requestId=请求编号(渠道_时间戳)
	 * @requstr_type POST
	 * @return mixed
	 */
	public function selRenewalChannel(){

	}

	/**
	 * 续保渠道查询
	 * @access public
	 * @URL outChannel/renewal/queryByChannel.do?c=渠道代码&requestId=请求编号(渠道_时间戳)
	 * @requstr_type POST
	 * @return mixed
	 */
	public function selRenewalPolicy(){

	}

	/**
	 * 续保试算
	 * @access public
	 * @URL outChannel/renewal/calc.do?c=渠道代码&requestId=请求编号(渠道_时间戳)
	 * @requstr_type POST
	 * @return mixed
	 */
	public function quoteRenewal(){

	}

	/**
	 * 续保出单
	 * @access public
	 * @URL outChannel/renewal/create.do?c=渠道代码&requestId=请求编号(渠道_时间戳)
	 * @requstr_type POST
	 * @return mixed
	 */
	public function issueRenewal(){

	}

	/**
	 * 续保确认
	 * @access public
	 * @URL outChannel/renewal/confirm.do?c=渠道代码&requestId=请求编号(渠道_时间戳)
	 * @requstr_type POST
	 * @return mixed
	 */
	public function confirmRenewal(){

	}

	/**
	 * 电子发票查询
	 * @access public
	 * @URL outChannel/qryInvoice.do?c=渠道代码
	 * @requstr_type POST
	 * @return mixed
	 */
	public function selElectronicInvoice(){

	}

//    ==============================================================公共方法==========================================================

    /**
     * 业务参数 解析出源数据json字符串
     *
     */
    protected function decodeOriginData()
    {
        $input = $this->request->all();
        $original_data_array = $this->sign_help->tyDecodeOriginData($input['biz_content']);
        return $original_data_array;
    }

    /**
     * 测试处理费率表execl
     *
     */
    public function testExcel()
    {
        set_time_limit(0);//永不超时
//        $a = ExcelHelper::excelToArray("upload/tariff/hg_tariff.xlsx");
//        $a =[
//			["code","message"],
//			["P","个单"],
//			["G","团单"],
//		];
//        $title = $a['0'];
//        $tariff = $a;
//        unset($tariff[0]);
//        $insert_data = [];
//        foreach($tariff as $k => $v){
//            foreach($v as $vk => $vv){
//                if(!empty($vv)){
//                    $insert_data[$k][$title[$vk]] = str_replace(array("\r\n", "\r", "\n"),  '', $vv);
//                }
//            }
//        }
//        print_r(json_encode($insert_data));
    }

    /**
     * 封装默认试算参数
     * @param $restrict_genes
     * @return array
     */
    protected function encapsulateDefaultData($restrict_genes)
    {
        $data = [];
        foreach ($restrict_genes as $restrict_gene) {
            $data[$restrict_gene['ty_key']] = $restrict_gene['value'];
        }
        $data['productCode'] = $this->api_m_info->p_code;
        return $data;
    }

    /**
     * 转换格式，用于调用接口
     * @param $restrict_genes
     * @param $data
     * @return mixed
     */
    protected function transformToApiWay($restrict_genes, $data)
    {
        foreach ($restrict_genes as $restrict_gene) {
            if (isset($data[$restrict_gene['ty_key']])) {
                foreach ($restrict_gene['values'] as $value) {
                    if ($data[$restrict_gene['ty_key']] == $value['ty_value']) {
                        $data[$restrict_gene['ty_key']] = $value['value'];
                        break;
                    }
                }
                $data[$restrict_gene['key']] = $data[$restrict_gene['ty_key']];
                unset($data[$restrict_gene['ty_key']]);
            }
        }

        return $data;
    }

    /**
     * 去掉不用的Key
     * @param $data
     * @return mixed
     */
    protected function onlyTyKey($data)
    {
        foreach ($data as &$item) {
            if (isset($item['values'])) {
                foreach ($item['values'] as &$value) {
                    unset($value['value']);
                }
            }
            unset($item['key']);
        }

        return $data;
    }

    /**
     * 内外键值转换
     * @param $input
     * @return $data
     */
    protected function ins_key_value_change($input){
        $tmp_data = [];
        $original_data = $input;
        $insurance_attributes = (new InsuranceAttributesRepository())->findAttributesRecursionByBindId($this->bind->id);
        $attributes = $original_data['insurance_attributes'];
        foreach ($insurance_attributes as $insurance_attribute) {
            $module_key = $insurance_attribute['module_key'];
            if (isset($attributes[$module_key]) && isset($insurance_attribute['productAttributes'])) {
                if (isset($attributes[$module_key][0])) { // 如果是二维数组
                    foreach ($attributes[$module_key] as $key => $item) {
                        foreach ($insurance_attribute['productAttributes'] as $productAttribute) {
                            $ty_key = $productAttribute['ty_key'];
                            if (isset($attributes[$module_key][$key][$ty_key]) && isset($productAttribute['attributeValues'])) {
                                foreach ($productAttribute['attributeValues'] as $attributeValue) {
                                    $ty_value = $attributeValue['ty_value'];
                                    if ($ty_value == $attributes[$module_key][$key][$ty_key]) {
                                        $attributes[$module_key][$key][$ty_key] = $attributeValue['controlValue'];
                                    }
                                }
                                if ($ty_key == 'ty_beibaoren_occupation') {
                                    $occupation = $attributes[$module_key][$ty_key];
                                    list(,,$code) = explode('-', $occupation);
                                    $attributes[$module_key][$key][$ty_key] = $code;
                                }
                            }
                            $tmp_data[$module_key][$key][$productAttribute['apiName']] = $attributes[$module_key][$key][$ty_key];
                        }
                    }
                } else {
                    foreach ($insurance_attribute['productAttributes'] as $productAttribute) {
                        $ty_key = $productAttribute['ty_key'];
                        if (isset($attributes[$module_key][$ty_key]) && isset($productAttribute['attributeValues'])) {
                            foreach ($productAttribute['attributeValues'] as $attributeValue) {
                                $ty_value = $attributeValue['ty_value'];
                                if ($ty_value == $attributes[$module_key][$ty_key]) {
                                    $attributes[$module_key][$ty_key] = $attributeValue['controlValue'];
                                }
                            }
                            if ($ty_key == 'ty_toubaoren_occupation') {
                                $occupation = $attributes[$module_key][$ty_key];
                                list(,,$code) = explode('-', $occupation);
                                $attributes[$module_key][$ty_key] = $code;
                            }
                        }
                        $tmp_data[$module_key][$productAttribute['apiName']] = $attributes[$module_key][$ty_key];
                    }
                }
            }
        }
        $data = [];
        foreach ($tmp_data as $module_key => $item) {
            if($module_key == 'ty_beibaoren') {
                $data['policyInsuredList'] = $item;//被保人列表
            }elseif ($module_key == 'ty_toubaoren') {
                $data['policyHolder'] = $item;//投保人
            }elseif ($module_key == 'ty_shouyiren') {
                $data['bnf'] = $item;//受益人
            }elseif($module_key == 'ty_base') {
                $data['base'] = $item;//基础信息
            }else{
                array_push($data, $item);
            }
        }
        return $data;
    }

    /**
     * 根据身份证号获取性别，生日，年龄（周岁）
     * @param $id 身份证号
     * @return $data
     */
    public function getMSgByID($id){
        //过了这年的生日才算多了1周岁
        if(empty($id)) return [];
        $date=strtotime(substr($id,6,8));
        //获得出生年月日的时间戳
        $today=strtotime('today');
        //获得今日的时间戳
        $diff=floor(($today-$date)/86400/365);
        //得到两个日期相差的大体年数
        //strtotime加上这个年数后得到那日的时间戳后与今日的时间戳相比
        $age=strtotime(substr($id,6,8).' +'.$diff.'years')>$today?($diff+1):$diff;
        //通过身份证号查询出性别与生日
        $birth = strlen($id)==15 ? ('19' . substr($id, 6, 6)) : substr($id, 6, 8);
        $sex = substr($id, (strlen($id)==15 ? -2 : -1), 1) % 2 ? '男' : '女'; //1为男 2为女
        $data = [];
        $data['sex'] = $sex;
        $data['birth'] = $birth;
        $data['age'] = $age;
        return $data;
    }
}