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
	//TODO  测试环境-http
	//接口地址
	const API_INSURE_URL_TEST = 'https://test-mobile.health.pingan.com:42443/ehis-hm';
	//渠道编码
	const API_CHANNEL_CODE_TEST = 'BJTYKJ';
	//渠道编号
	const API_CHANNEL_id_TEST = '710';
	//秘钥
	const API_INSURE_URL_KEY_TEST = '123456';
	//收银台渠道id
	const API_PAY_id_TEST = '000102';
	//收银台APP秘钥
	const API_PAY_URL_KEY_TEST = '4OQ5RBPPJC5F8PUKXMUDGDU7G96BVHVW9FFW4AJVZ2KWH2W8WP9QSTNVQXEAOREURU3UQJ6N9CWPVM731K2SQDP5RZXBGII9MDJXIKJ9D4W9MSEZR3FQEQBTTD8L6MRY';
	//支付请求地址
	const API_PAY_URL_TEST = 'http://test-mobile.health.pingan.com/ehis-hm/cashier/pay.do';

	//TODO 生产环境-https===================================================================
	//接口地址
	const API_INSURE_URL = 'https://mobile.health.pingan.com/ehis-hm';
	//渠道编码
	const API_CHANNEL_CODE = 'BJTYKJ';
	//渠道编号
	const API_CHANNEL_id = '710';
	//秘钥
	const API_INSURE_URL_KEY = '2aercwjAPglTbcVm';
	//收银台渠道id
	const API_PAY_id = '000102';
	//收银台APP秘钥
	const API_PAY_URL_KEY = 'LGAUMJ5SDF3C7TDJRBE9CP8CVT6C29E4OUXWCWUNIXRKCDUI14V8DE6UWVPK72CKVKZA28MJ8R798EVPCFSY7MZXNOG3EOGAQLHDK318OD9E87KLFTCXRIT8FGMU99DQ';
	//支付请求地址
	const API_PAY_URL = 'https://mobile.health.pingan.com/ehis-hm/cashier/pay.do';

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
//		if(!preg_match("/call_back/",$this->request->url())) {
//			$this->original_data = $this->decodeOriginData();
//			//业务参数 解析出源数据json字符串
//			$repository = new InsuranceApiFromRepository();
//			//天眼产品ID
//			$insurance_id = isset($this->original_data['ty_product_id']) ? $this->original_data['ty_product_id'] : 0;
//			//内部产品唯一码
//			$private_p_code = isset($this->original_data['private_p_code']) ? $this->original_data['private_p_code'] : 0;
//			if ($insurance_id) {
//				$this->bind = $repository->getApiStatusOn($insurance_id);    //获取当前正在启用的API来源信息
//			} else {
//				$this->bind = $repository->getApiByPrivatePCode($private_p_code);   //通过内部产品唯一码获得相关信息
//			}
//			if (empty($this->bind)) {
//				return ['data' => 'product not exist', 'code' => 400];
//			}
//		}
	}

	/**
	 * 产品详情
	 * @access public
	 * @params productId|产品编号|String|是
	 * @requstr_type POST
	 * @return mixed
	 */
	public function getApiOption()
	{
		$insuranceAttributesRepository = new InsuranceAttributesRepository();
		$restrictGeneRepository = new RestrictGeneRepository();
		$input = $this->original_data;
		$insurance_id = $input['ty_product_id'];
		$result['ty_product_id'] = $insurance_id;
		$result['private_p_code'] = $this->bind ->private_p_code;
		$result['bind_id'] = $this->bind ->id;
		//投保属性
		$insurance_attributes = $insuranceAttributesRepository->findAttributesRecursionByBindId($this->bind ->id);
		foreach ($insurance_attributes as &$insurance_attribute) {
			if (isset($insurance_attribute['productAttributes'])) {
				foreach ($insurance_attribute['productAttributes'] as &$insurance_attribute) {
					if (isset($insurance_attribute['attributeValues'])) {
						foreach ($insurance_attribute['attributeValues'] as &$value) {
							unset($value['controlValue']);
						}
					}
					unset($insurance_attribute['apiName']);
				}
			}
		}
		$result['option']['insurance_attributes'] = $insurance_attributes;
		//试算因子对应选项
		$restrict_genes = $restrictGeneRepository->findRestrictGenesRecursionByBindId($this->bind ->id);
		$result['option']['restrict_genes'] = $this->onlyTyKey($restrict_genes);
		//获得默认试算选项
		$result['option']['selected_options'] = $restrictGeneRepository->findDefaultRestrictGenes($this->bind ->id);
		$result['option']['selected_options'] = $this->onlyTyKey($result['option']['selected_options']);
		$result['option']['area'] = json_decode(config('hg_msg.area'),true);//地区信息
		$result['option']['jobs'] = json_decode(config('hg_msg.job'),true);//职业信息
		$result['option']['bank'] = config('hg_msg.bank');//银行信息
		$result['option']['pay_code'] = config('hg_msg.pay_code');//支付状态码
		//todo   保障权益，还没获取，算费价格待定
//        $default_quote = $this->formatQuote($result['option']['selected_options']);
//        dd($default_quote);
//        //获得默认保费及保障内容
//        $result['option']['price'] = $default_quote->price;
		$min_tariff= DB::table('hg_tariff')->min('tariff');
		$min_tariff = 6.2;
//        $results = DB::table('hg_tariff')->orderBy('tariff')->get();
//        $min_tariff = $results->tariff;
		$result['option']['price'] = $min_tariff*10000;//单位为分
//        //保障内容
//        $result['option']['protect_items'] = $default_quote->protect_items;
		$result['option']['protect_items'] = '';
		return ['data'=> $result, 'code'=> 200];
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
		$input['productId'] = 'A000000030';
		$input['applyDate'] = date('Y-m-d',time());
		$input['isShareCoverage'] = 'N';
		$input['premType'] = '1';//缴费频次
		$input['insurants'][] = [
			'seqno'=>'1',
			'birthday'=>'1995-01-15',
			'sex'=>'F',//M男F女
			'hasSocialSecurity'=>'Y',//是否有社保(Y/N)
			'relationshipWithPrimaryInsurant'=>'1',//与主被保人关系
			'coverages'=>[
				'0'=>[
					'benLevel'=>'01',
					'sumInsured'=>'59500',
					'period'=>'12',//保障期间（月），默认12
					'periodDay'=>'0',//保障期间（天），默认为0
				],
			],
		];
		$input['insurants'][] = [
			'seqno'=>'2',
			'birthday'=>'1994-12-30',
			'sex'=>'F',//M男F女
			'hasSocialSecurity'=>'Y',//是否有社保(Y/N)
			'relationshipWithPrimaryInsurant'=>'1',//与主被保人关系
			'coverages'=>[
				'0'=>[
					'benLevel'=>'01',
					'sumInsured'=>'59500',
					'period'=>'12',//保障期间（月），默认12
					'periodDay'=>'0',//保障期间（天），默认为0
				],
			],
		];
		$input['insurants'][] = [
			'seqno'=>'3',
			'birthday'=>'1995-01-12',
			'sex'=>'M',//M男F女
			'hasSocialSecurity'=>'Y',//是否有社保(Y/N)
			'relationshipWithPrimaryInsurant'=>'1',//与主被保人关系
			'coverages'=>[
				'0'=>[
					'benLevel'=>'01',
					'sumInsured'=>'59500',
					'period'=>'12',//保障期间（月），默认12
					'periodDay'=>'0',//保障期间（天），默认为0
				],
			],
		];
		//echo json_encode($input['insurants'][0]['coverages']).'-------';
		$request_data = [];
		$request_data['requestId'] =  self::API_CHANNEL_CODE.time();
		$key = self::API_INSURE_URL_KEY;//测试环境
		$input = json_encode($input,JSON_UNESCAPED_UNICODE);
		dump($input);
		$input = $this->aes_crypt_helper->encrypt($input,$key);
		$request_data['data'] = $input;
		dump(json_encode($request_data));
		$request_url = self::API_INSURE_URL.'/outChannel/calculatePremium.do?c='.self::API_CHANNEL_CODE;
		//dump($request_url);
		$response = Curl::to($request_url)
			->returnResponseObject()
			->withData($request_data)
			->withHeader("Content-Type:application/json;charset=UTF-8,Accept:application/json;charset=UTF-8")
			->asJson()
			->withTimeout($this->retry_time)
			->post();
		$responses = json_encode($response, JSON_FORCE_OBJECT);
		//print_r($responses);
		//失败返回
		if($response->status != 200)
			return ['data'=> 'default quote error', 'code'=> 400];
		if($response->content->returnCode != 00){
			LogHelper::logError($request_data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'pingan', 'insAttr');
			return json_encode(['data'=>$response->content->returnMsg, 'code'=> 400], JSON_UNESCAPED_UNICODE);
		}
		$data = $response->content->data;//获取算费密文
		//$data = '240E5ADD76E8EA9F27296BD9F325E795F78A07D60183702EF505C89F04472280EB5F14CEDAFA17923DDBBB3B2744B4337D58C4D1C80808E2A6537FBD7EAFE73C219ECDF37807506275E4738A93A27B4E';
		//TODO  解密
		$data = $this->aes_crypt_helper->decrypt($data, $key);
		dd($data);
		$msg['data']['price'] = json_decode($data,true)['totalPrem'];
		$msg['data']['selected_options'] = '';
		$msg['code'] = 200;
		return $msg;
		//return ['data'=>json_decode($data,true), 'code'=>200];
	}

	/**
	 * 保险核保-个险Personal
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
	public function checkPersonalIns()
	{
		$input = [];
		$input['productId'] = 'A000000042';//产品编码
		$input['applyDate'] = date('Y-m-d',time());
		$input['effDate'] = date('Y-m-d',strtotime('+1 day'));//保单生效日期
		$input['totalPremium'] = '210.00';
		$input['outChannelOrderId'] = '123456789';//渠道方订单号,最大32位
		//投保人信息
		$input['applicant'] = [];
		$input['applicant']['name'] = '程景峰';
		$input['applicant']['idType'] = '1';
		$input['applicant']['idno'] = '410822199501120035';
		$input['applicant']['birthday'] = '1995-01-12';
		$input['applicant']['sex'] = 'M';
		$input['applicant']['contactInfo']['mobile'] = '15701681524';
		$input['applicant']['contactInfo']['email'] = 'wangsl@inschos.com';
		//被保人信息列表
		$input['insurants'] = [];
		$input['insurants'][0]['seqno'] = '1';
		$input['insurants'][0]['name'] = '程景峰';
		$input['insurants'][0]['idType'] = '1';
		$input['insurants'][0]['idno'] = '410822199501120035';
		$input['insurants'][0]['birthday'] = '1995-01-12';
		$input['insurants'][0]['sex'] = 'M';

		$input['insurants'][0]['relationshipWithApplicant'] = '1';//与投保人关系1	本人2	配偶9	其他I	父母J	子女
		$input['insurants'][0]['relationshipWithPrimaryInsurant'] = '1';//与第一被保险人关系1	主被保险人2	配偶3	子女4	父母6	其他
		$input['insurants'][0]['contactInfo']['mobile'] = '15701681524';
		$input['insurants'][0]['contactInfo']['email'] = 'wangsl@inschos.com';

		$input['insurants'][0]['socialSecurity'] = 'Y';//是否有社保: 有：Y/N
		$input['insurants'][0]['socialSecurityLocation'] = 'H010000';//社保所在地


		$input['insurants'][0]['coverages'][0]['planType'] = '0';//险种类别（0主险/1附加险），默认为0
		$input['insurants'][0]['coverages'][0]['sumInsured'] = '400000';//保额
		$input['insurants'][0]['coverages'][0]['benLevel'] = '01';//档次
		$input['insurants'][0]['coverages'][0]['period'] = '12';//保险期间（月）
		$input['insurants'][0]['coverages'][0]['periodDay'] = '0';//保险期间（天）
		$input['insurants'][0]['coverages'][0]['paymentPeriod'] = '12';//缴费期间（月）
		$input['insurants'][0]['coverages'][0]['paymentPeriodDay'] = '0';//缴费期间（天）
		$input['insurants'][0]['coverages'][0]['actualPrem'] = '210.00';//实际保费，单位元，小数点后两位
		$input['insurants'][0]['healthNotes'][0]['questionId'] = 'P00500001';//健康告知问题ID
		$input['insurants'][0]['healthNotes'][0]['answer'] = 'Y';//答案值Y/N
		$input['insurants'][0]['healthNotes'][0]['healthNoteSeq'] = '1';//告知批次号,1,2,3：如果接口方会记录且传给健康险历史告知记录，则该字段用于区分各批次健康告知，否则默认传值为
		//授权信息
		$input['authInfo'] = [];
		$input['authInfo']['initialChargeMode'] = '9';//首期收费方式
		//服务约定信息
		$input['serviceAgreementInfo'] = [];
		$input['serviceAgreementInfo']['premType'] = '5';//缴费频次,1	趸缴2	月缴3	季缴4	半年缴5	年缴6	趸缴累加
		dump($input);
		$request_data = [];
		$request_data['requestId'] =  self::API_CHANNEL_CODE.time();
		$key = self::API_INSURE_URL_KEY;//测试环境
		$input = json_encode($input,JSON_UNESCAPED_UNICODE);
		dump($input);
		$input = $this->aes_crypt_helper->encrypt($input,$key);
		$request_data['data'] = $input;
		$request_url = self::API_INSURE_URL.'/outChannel/validate.do?c='.self::API_CHANNEL_CODE;;
		//dump($re、quest_url);
		dump(json_encode($request_data));
		$response = Curl::to($request_url)
			->returnResponseObject()
			->withData($request_data)
			->withHeader("Content-Type:application/json;charset=UTF-8,Accept:application/json;charset=UTF-8")
			->asJson()
			->withTimeout($this->retry_time)
			->post();
		$responses = json_encode($response, JSON_FORCE_OBJECT);
		print_r($responses);
		//失败返回
		if($response->status != 200)
			return ['data'=> 'default quote error', 'code'=> 400];
		if($response->content->returnCode != 00){
			LogHelper::logError($request_data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'pingan', 'insAttr');
			return json_encode(['data'=>$response->content->returnMsg, 'code'=> 400], JSON_UNESCAPED_UNICODE);
		}
		$data = $response->content->data;//获取密文
		$data = $this->aes_crypt_helper->decrypt($data, $key);
		print_r($data);die;
		return ['data'=>json_decode($data,true), 'code'=>200];
	}

	/**
	 * TODO 等平安开发接口2018-04-28
	 * 保险核保-团险corporate  todo 3-30 保单生效
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
	public function checkCorporateIns()
	{
		$input = [];
		$input['productId'] = 'A000000029';//产品编码
		$input['outChannelOrderId'] = '12345678901';//渠道方订单号,最大32位
		$input['applyDate'] = date('Y-m-d',time());
		$input['effDate'] = date('Y-m-d',strtotime('+3 day'));//保单生效日期 TODO 3-30 做限制
		$input['totalPremium'] = '414.00';
		$input['isNoticeConfirm'] = 'Y';//是否同意页面告知：Y/N
		//投保企业信息
		$input['applyOrg'] = [];
		$input['applyOrg']['name'] = '北京天眼互联科技有限公司';//企业名称	String	VARCHAR2(60)
		$input['applyOrg']['industryCode'] = 'G';//行业类型。参见附录3.11	String	VARCHAR2(4)
		$input['applyOrg']['provinceCode'] = '110000';//企业所在地(省)。参见附录3.12	String		VARCHAR2(6)
		$input['applyOrg']['cityName'] = '北京市';//企业所在地(市)	String		VARCHAR2(100)
		$input['applyOrg']['areaName'] = '东城区';//企业所在地(县/区)	String		VARCHAR2(100)
		$input['applyOrg']['address'] = '北京市东城区夕照寺中街14号';//企业所在地（详细地址）	String		VARCHAR2(300)
		$input['applyOrg']['orgCodeType'] = '1';//证件类型（1:社会统一信用代码、2:组织机构代码证、3：企业营业执照）	String		VARCHAR2(2)
		$input['applyOrg']['organizationCode'] = '91110105MA007MTL18';//证件号码	String		VARCHAR2(20)
		$input['applyOrg']['orgCodeEffDate'] = '2036-08-16';//证件有效期YYYY-MM-DD	String
//		$input['applyOrg']['imUploads'] = [];//企业影像
//		$input['applyOrg']['imUploads'][0]['imUploadFlag'] = 'N';//是否上传影像：Y/N
//		$input['applyOrg']['imUploads'][0]['imUploadType'] = '';//上传影像类型
//		$input['applyOrg']['imUploads'][0]['imUploadFileId'] = '';//影像id
		$input['isSendMail'] = 'N';//是否发送欢迎信电子凭证：Y/N

		//企业联系人信息
		$input['applyOrg']['orgContactPerson'] = [];
		$input['applyOrg']['orgContactPerson'] = [
			'name'=>'王石磊',//姓名	String		VARCHAR2(80)
			'certificateTypeCode'=>'1',//证件类型。参见附录3.3	String	VARCHAR2(2)
			'certificateNo'=>'410881199406056514',//证件号码	String		VARCHAR2(20)
			'birthday'=>'1994-06-05',//出生日期，YYYY-MM-DD	String
			'sex'=>'M',//性别，男M女F	String	VARCHAR2(1)
			'phoneNo'=>'15701681524',//手机号码	String	VARCHAR2(32)
			'email'=>'wangsl@inschos.com',//经办人邮箱	String	VARCHAR2(50)
		];
		$input['applyOrg']['orgContactPerson'] = json_decode(json_encode($input['applyOrg']['orgContactPerson']));
		//被保人信息列表
		$input['insurants'] = [];
		$input['insurants'][0]['seqno'] = '1';
		$input['insurants'][0]['name'] = '史亚文';
		$input['insurants'][0]['idType'] = '1';
		$input['insurants'][0]['idno'] = '412723199501151643';
		$input['insurants'][0]['birthday'] = '1995-01-15';
		$input['insurants'][0]['sex'] = 'F';
		$input['insurants'][0]['socialSecurity'] = 'Y';//是否有社保: 有：Y/N
		$input['insurants'][0]['socialSecurityLocation'] = 'H010000';//社保所在地
		$input['insurants'][0]['relationshipWithApplicant'] = '1';//与投保人关系1	本人2	配偶9	其他I	父母J	子女
		$input['insurants'][0]['relationshipWithPrimaryInsurant'] = '1';//与第一被保险人关系1主被保险人2	配偶3子女4父母6其他
		$input['insurants'][0]['contactInfo']['mobile'] = '15701681524';
		$input['insurants'][0]['contactInfo']['email'] = 'wangsl@inschos.com';
		$input['insurants'][0]['coverages'][0]['planType'] = '0';//险种类别（0主险/1附加险），默认为0
		$input['insurants'][0]['coverages'][0]['sumInsured'] = '59500';//保额
		$input['insurants'][0]['coverages'][0]['benLevel'] = '01';//档次
		$input['insurants'][0]['coverages'][0]['period'] = '12';//保险期间（月）
		$input['insurants'][0]['coverages'][0]['periodDay'] = '0';//保险期间（天）
		$input['insurants'][0]['coverages'][0]['paymentPeriod'] = '12';//缴费期间（月）
		$input['insurants'][0]['coverages'][0]['paymentPeriodDay'] = '0';//缴费期间（天）
		$input['insurants'][0]['coverages'][0]['actualPrem'] = '138.00';//实际保费，单位元，小数点后两位


		$input['insurants'][1]['seqno'] = '2';
		$input['insurants'][1]['name'] = '聂文瑾';
		$input['insurants'][1]['idType'] = '1';
		$input['insurants'][1]['idno'] = '41088119941230078X';
		$input['insurants'][1]['birthday'] = '1994-12-30';
		$input['insurants'][1]['sex'] = 'F';
		$input['insurants'][1]['socialSecurity'] = 'Y';//是否有社保: 有：Y/N
		$input['insurants'][1]['socialSecurityLocation'] = 'H010000';//社保所在地
		$input['insurants'][1]['relationshipWithApplicant'] = '1';//与投保人关系1	本人2	配偶9	其他I	父母J	子女
		$input['insurants'][1]['relationshipWithPrimaryInsurant'] = '1';//与第一被保险人关系1主被保险人2	配偶3子女4	父母6	其他
		$input['insurants'][1]['contactInfo']['mobile'] = '15701681524';
		$input['insurants'][1]['contactInfo']['email'] = 'wangsl@inschos.com';
		$input['insurants'][1]['coverages'][0]['planType'] = '0';//险种类别（0主险/1附加险），默认为0
		$input['insurants'][1]['coverages'][0]['sumInsured'] = '59500';//保额
		$input['insurants'][1]['coverages'][0]['benLevel'] = '01';//档次
		$input['insurants'][1]['coverages'][0]['period'] = '12';//保险期间（月）
		$input['insurants'][1]['coverages'][0]['periodDay'] = '0';//保险期间（天）
		$input['insurants'][1]['coverages'][0]['paymentPeriod'] = '12';//缴费期间（月）
		$input['insurants'][1]['coverages'][0]['paymentPeriodDay'] = '0';//缴费期间（天）
		$input['insurants'][1]['coverages'][0]['actualPrem'] = '138.00';//实际保费，单位元，小数点后两位


		$input['insurants'][2]['seqno'] = '3';
		$input['insurants'][2]['name'] = '程景峰';
		$input['insurants'][2]['idType'] = '1';
		$input['insurants'][2]['idno'] = '410822199501120035';
		$input['insurants'][2]['birthday'] = '1995-01-12';
		$input['insurants'][2]['sex'] = 'M';
		$input['insurants'][2]['socialSecurity'] = 'Y';//是否有社保: 有：Y/N
		$input['insurants'][2]['socialSecurityLocation'] = 'H010000';//社保所在地
		$input['insurants'][2]['relationshipWithApplicant'] = '1';//与投保人关系1	本人2	配偶9	其他I	父母J	子女
		$input['insurants'][2]['relationshipWithPrimaryInsurant'] = '1';//与第一被保险人关系1主被保险人2	配偶3子女4	父母6	其他
		$input['insurants'][2]['contactInfo']['mobile'] = '15701681524';
		$input['insurants'][2]['contactInfo']['email'] = 'wangsl@inschos.com';
		$input['insurants'][2]['coverages'][0]['planType'] = '0';//险种类别（0主险/1附加险），默认为0
		$input['insurants'][2]['coverages'][0]['sumInsured'] = '59500';//保额
		$input['insurants'][2]['coverages'][0]['benLevel'] = '01';//档次
		$input['insurants'][2]['coverages'][0]['period'] = '12';//保险期间（月）
		$input['insurants'][2]['coverages'][0]['periodDay'] = '0';//保险期间（天）
		$input['insurants'][2]['coverages'][0]['paymentPeriod'] = '12';//缴费期间（月）
		$input['insurants'][2]['coverages'][0]['paymentPeriodDay'] = '0';//缴费期间（天）
		$input['insurants'][2]['coverages'][0]['actualPrem'] = '138.00';//实际保费，单位元，小数点后两位
		//授权信息
		$input['authInfo'] = [];
		$input['authInfo']['initialChargeMode'] = '9';//首期收费方式
		//服务约定信息
		$input['serviceAgreementInfo'] = [];
		$input['serviceAgreementInfo']['premType'] = '5';//缴费频次,1	趸缴2	月缴3	季缴4	半年缴5	年缴6	趸缴累加
		//渠道信息
//		$input['channelInfo'] = [];
//		$input['channelInfo']['sellerCode'] = 'sellerCode63';
//		$input['channelInfo']['sellerName'] = '测试销售员14';
		$input = json_encode($input,JSON_UNESCAPED_UNICODE);
		dump($input);
		$request_data = [];
		$request_data['requestId'] =  self::API_CHANNEL_CODE.time();
		$key = self::API_INSURE_URL_KEY;//测试环境
		$input = $this->aes_crypt_helper->encrypt($input,$key);
		$request_data['data'] = $input;
		$request_url = self::API_INSURE_URL.'/outChannel/validate.do?c='.self::API_CHANNEL_CODE;;
		//dump($re、quest_url);
		dump(json_encode($request_data));
		$response = Curl::to($request_url)
			->returnResponseObject()
			->withData($request_data)
			->withHeader("Content-Type:application/json;charset=UTF-8,Accept:application/json;charset=UTF-8")
			->asJson()
			->withTimeout($this->retry_time)
			->post();
		dump($response);
		$responses = json_encode($response, JSON_FORCE_OBJECT);
		print_r($responses);
		//失败返回
		if($response->status != 200)
			return ['data'=> 'default quote error', 'code'=> 400];
		if($response->content->returnCode != 00){
			LogHelper::logError($request_data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'pingan', 'insAttr');
			return json_encode(['data'=>$response->content->returnMsg, 'code'=> 400], JSON_UNESCAPED_UNICODE);
		}
		$data = $response->content->data;//获取密文
		dump($data);
		$data = $this->aes_crypt_helper->decrypt($data, $key);
		dd($data);
		return ['data'=>json_decode($data,true), 'code'=>200];
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
		$input['channel_order_no'] = '88000000022255867';//健康险订单号
		$input['goods_desc'] = '平安e生保plus';//商品描述
		$input['total_fee'] = '41400';//支付金额 精确到分
		$input['channel_id'] = self::API_CHANNEL_id;//渠道编码 固定值
		$input['channel_id'] = '000102';//渠道编码 固定值
		$input['return_url'] = 'https://n183967a96.iask.in:49292/api/pa_pay_result';//支付完成后，页面跳转地址
		$input['notify_url'] = 'https://n183967a96.iask.in:49292/api/pa_pay_call_back';//支付完成后，后台异步通知支付结果
		$data = $input;
		ksort($input);//按字母升序排序
		$str = '';
		foreach ($input as $key=>$value){
			$str.=$key.'='.$value.'&';
		}
		$str = substr($str,0,strlen($str)-1).self::API_PAY_URL_KEY;
		$sign = hash("sha256", $str);
		$data['sign_type'] = 'SHA-256';
		$data['sign'] = $sign;//将请求参数除去sign_type、sign，按字母升序排序后加上加密密钥，进行SHA-256加密	Y
		$request_url = self::API_PAY_URL.
			'?channel_id='.$data['channel_id'].
			'&channel_order_no='.$data['channel_order_no'].
			'&goods_desc='.urlencode($data['goods_desc']).
			'&notify_url='.urlencode($data['notify_url']).
			'&return_url='.urlencode($data['return_url']).
			'&sign='.$data['sign'].
			'&sign_type='.$data['sign_type'].
			'&total_fee='.$data['total_fee'];
		dump($request_url);
	}

	/**
	 * 保险支付-收银台支付成功跳转地址
	 * @access public
	 * @return mixed
	 */
	public function payResult(){
		return json_encode(['status'=>'200','content'=>'支付中，请稍后...'],JSON_UNESCAPED_UNICODE);
	}

	/**
	 * 保险支付-支付回调
	 * 渠道方回调返回:TRUE/FALSE,为TRUE时,不再回调.为FALSE时，5分钟回调1次，最多50次
	 * @access public
	 * @params channel_order_no	String 最长20位字符	健康险订单号，来自核保	Y	0000000001
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
		$input = $this->request->all();
		LogHelper::logSuccess($input, 'pingan', 'pay_call_back');
		return json_encode(['status'=>false]);
//		$input = [];
//		$input['channel_order_no'] = '';
//		$input['pay_serial_no'] = '';
//		$input['pay_status'] = '';
//		$input['pay_time'] = '';
//		$input['total_fee'] = '';
//		$input['pay_type'] = '';
//		$input['sign_type'] = '';
//		$input['sign'] = '';
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
		$input['orderId'] = '99000000042845469';//健康险订单号
		$request_data = [];
		$request_data['requestId'] =  self::API_CHANNEL_CODE.time();
		$key = self::API_INSURE_URL_KEY;//测试环境
		$input = json_encode($input);
		dump($input);
		$input = $this->aes_crypt_helper->encrypt($input,$key);
		$request_data['data'] = $input;
		dump(json_encode($request_data));
		$request_url = self::API_INSURE_URL.'/outChannel/accept.do?c='.self::API_CHANNEL_CODE;
		dump($request_url);
		$response = Curl::to($request_url)
			->returnResponseObject()
			->withData($request_data)
			->withHeader("Content-Type:application/json;charset=UTF-8,Accept:application/json;charset=UTF-8")
			->asJson()
			->withTimeout($this->retry_time)
			->post();
//		dd($response);
		$responses = json_encode($response, JSON_FORCE_OBJECT);
		print_r($responses);
		//失败返回
		if($response->status != 200)
			return ['data'=> 'default quote error', 'code'=> 400];
		if($response->content->returnCode != 00){
			LogHelper::logError($request_data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'pingan', 'insAttr');
			return json_encode(['data'=>$response->content->returnMsg, 'code'=> 400], JSON_UNESCAPED_UNICODE);
		}
		$data = $response->content->data;//获取密文
		$data = $this->aes_crypt_helper->decrypt($data, $key);
		dd($data);
		return ['data'=>json_decode($data,true), 'code'=>200];
	}

	/**
	 * 保单查询
	 * TODO 接口显示无该保单相应数据,平安回复测试环境暂不能自动承保，需要手动处理
	 * @access public
	 * @URL	/outChannel/qryPolByPolNo.do?c=渠道代码
	 * @requstr_type POST
	 * @return mixed
	 */
	public function selPolicy(){
		$input = [];
		$input['policyNo'] = '99000000042845207';//保单号
		$request_data = [];
		$request_data['requestId'] =  self::API_CHANNEL_CODE.time();
		$key = self::API_INSURE_URL_KEY;//测试环境
		$input = json_encode($input);
		dump($input);
		$input = $this->aes_crypt_helper->encrypt($input,$key);
		$request_data['data'] = $input;
		dump(json_encode($request_data));
		$request_url = self::API_INSURE_URL.'/outChannel/qryPolByPolNo.do?c='.self::API_CHANNEL_CODE;
		dump($request_url);
		$response = Curl::to($request_url)
			->returnResponseObject()
			->withData($request_data)
			->withHeader("Content-Type:application/json;charset=UTF-8,Accept:application/json;charset=UTF-8")
			->asJson()
			->withTimeout($this->retry_time)
			->post();
		$responses = json_encode($response, JSON_FORCE_OBJECT);
		print_r($responses);
		//dd($response);
		//失败返回
		if($response->status != 200)
			return ['data'=> 'default quote error', 'code'=> 400];
		if($response->content->returnCode != 00){
			LogHelper::logError($request_data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'pingan', 'insAttr');
			return json_encode(['data'=>$response->content->returnMsg, 'code'=> 400], JSON_UNESCAPED_UNICODE);
		}
		$data = $response->content->data;//获取密文
		$data = $this->aes_crypt_helper->decrypt($data, $key);
		dd($data);
		return ['data'=>json_decode($data,true), 'code'=>200];
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
		$input['policyNo'] = '99000000042845207';//保单号
		//退费方式，付款方式无需付款A: 渠道方退款，后继结算，下面的银行相关字段无需填
		//银行批扣C: 健康险退款，后继结算，下面的银行相关字段必须填
		$input['paymentMode'] = 'C';
		$input['bankAccountNo'] = '6222022002006651860';//退保银行账号
		$input['bankAccountName'] = '王石磊';//开户人姓名
		$input['bankDescription'] = '9200200050348380';//保单号
		$request_data = [];
		$request_data['requestId'] =  self::API_CHANNEL_CODE.time();
		$key = self::API_INSURE_URL_KEY;//测试环境
		$input = json_encode($input);
//		dump($input);
		$input = $this->aes_crypt_helper->encrypt($input,$key);
		$request_data['data'] = $input;
//		dump(json_encode($request_data));
		$request_url = self::API_INSURE_URL.'/outChannel/revokePolicy.do?c='.self::API_CHANNEL_CODE;
		//dump($request_url);
		$response = Curl::to($request_url)
			->returnResponseObject()
			->withData($request_data)
			->withHeader("Content-Type:application/json;charset=UTF-8,Accept:application/json;charset=UTF-8")
			->asJson()
			->withTimeout($this->retry_time)
			->post();
//		dd($response);
		//失败返回
		if($response->status != 200)
			return ['data'=> 'default quote error', 'code'=> 400];
		if($response->content->returnCode != 00){
			LogHelper::logError($request_data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'pingan', 'insAttr');
			return json_encode(['data'=>$response->content->returnMsg, 'code'=> 400], JSON_UNESCAPED_UNICODE);
		}
		$data = $response->content->data??"";//获取密文
		if(empty($data)){
			return json_encode(['data'=>'操作成功', 'code'=>200],JSON_UNESCAPED_UNICODE);
		}
		$data = $this->aes_crypt_helper->decrypt($data, $key);
		return ['data'=>json_decode($data,true), 'code'=>200];
	}

	/**
	 * 电子保单下载
	 * TODO  保单号解密失败
	 * @access public
	 * @URL /outChannel/downloadPolicy.do?c=渠道代码&policyNo= URLEncoder.encode(加密之后的保单号, "utf-8");
	 * @requstr_type 访问方式	GET
	 * @return mixed
	 */
	public function downloadPolicy(){
		$warranty_code = urlencode('99000000042845207');
		$request_url = self::API_INSURE_URL.'/outChannel/downloadPolicy.do?c='.self::API_CHANNEL_CODE.'&policyNo='.$warranty_code;
		dump($request_url);
	}

//	==================================续保接口(Renewal)==========================================

	/**
	 * 续保渠道查询
	 * 根据渠道代码查询续保保单列表，投保人信息，保单号，险种，满期日根据渠道代码查询续保保单列表，投保人信息，保单号，险种，满期日
	 * @access public
	 * @URL outChannel/renewal/queryByPolicyNo.do?c=渠道代码&requestId=请求编号(渠道_时间戳)
	 * @params page	页数	int	Y
	 * @params size	大小，最大5000	int	Y
	 * @params polType	保单类型，用来区分保单类型G 为团单，P为个单	String	Y
	 * @requstr_type POST
	 * @return mixed
	 */
	public function selRenewalChannel(){
		$input = [];
		$input['page'] = '1';
		$input['size'] = '100';
		$input['polType'] = 'P';
		$request_data = [];
		$request_data['requestId'] =  self::API_CHANNEL_CODE.time();
		$key = self::API_INSURE_URL_KEY;//测试环境
		$input = json_encode($input,JSON_UNESCAPED_UNICODE);
		$input = $this->aes_crypt_helper->encrypt($input,$key);
		$request_data['data'] = $input;
		//dump(json_encode($request_data));
		$request_url = self::API_INSURE_URL.'/outChannel/calculatePremium.do?c='.self::API_CHANNEL_CODE.'&requestId='.self::API_CHANNEL_CODE.time();
		dump($request_url);
		$response = Curl::to($request_url)
			->returnResponseObject()
			->withData($request_data)
			->withHeader("Content-Type:application/json;charset=UTF-8,Accept:application/json;charset=UTF-8")
			->asJson()
			->withTimeout($this->retry_time)
			->post();
		dump($response);die;
		//失败返回
		if($response->status != 200)
			return ['data'=> 'default quote error', 'code'=> 400];
		if($response->content->returnCode != 00){
			LogHelper::logError($request_data, json_encode($response->content, JSON_UNESCAPED_UNICODE), 'pingan', 'insAttr');
			return json_encode(['data'=>$response->content->returnMsg, 'code'=> 400], JSON_UNESCAPED_UNICODE);
		}
		$data = $response->content->data;//获取密文
		$data = $this->aes_crypt_helper->decrypt($data, $key);
		return ['data'=>json_decode($data,true), 'code'=>200];
	}

	/**
	 * 续保保单查询
	 * @access public
	 * @URL outChannel/renewal/queryByChannel.do?c=渠道代码&requestId=请求编号(渠道_时间戳)
	 * @params policyNo	保单号	String		Y
	 * @requstr_type POST
	 * @return mixed
	 */
	public function selRenewalPolicy(){
		$input = [];
		$input['policyNo'] = '9200200050348936';//保单号	String		Y
		$request_data = [];
		$request_data['requestId'] =  self::API_CHANNEL_CODE.time();
		$key = self::API_INSURE_URL_KEY;//测试环境
		$input = json_encode($input);
		dump($input);
		$input = $this->aes_crypt_helper->encrypt($input,$key);
		$request_data['data'] = $input;
		dump(json_encode($request_data));
		$request_url = self::API_INSURE_URL.'outChannel/renewal/queryByChannel.do?c='.self::API_CHANNEL_CODE.'&requestId='.self::API_CHANNEL_CODE.time();;
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
		$data = $response->content->data;//获取密文
		$data = $this->aes_crypt_helper->decrypt($data, $key);
		return ['data'=>json_decode($data,true), 'code'=>200];
	}

	/**
	 * 续保试算
	 * @access public
	 * @URL outChannel/renewal/calc.do?c=渠道代码&requestId=请求编号(渠道_时间戳)
	 * @requstr_type POST
	 * @return mixed
	 */
	public function quoteRenewal(){
		$input = [];
		$input['policyNo'] = '9200200050348936';//policyNo	保单号	String	Y
		$input['renewalNo'] = '9200200050348936';//续保保单号	String	Y
		$input['benLevel'] = '01';//计划	String	Y
		$input['sumInsured'] = '400000';//保额	String	Y
		$input['isChange'] = 'Y';//是否转保(Y为是，N为否)	String	Y
		$input['productId'] = 'A000000042';//转保后产品ID（是否转保为Y时才传）	String	N
		$input['socialSecurity'] = 'N';//是否有社保 Y/N，默认为N	String	N
		$request_data = [];
		$request_data['requestId'] =  self::API_CHANNEL_CODE.time();
		$key = self::API_INSURE_URL_KEY;//测试环境
		$input = json_encode($input);
		dump($input);
		$input = $this->aes_crypt_helper->encrypt($input,$key);
		$request_data['data'] = $input;
		dump(json_encode($request_data));
		$request_url = self::API_INSURE_URL.'outChannel/renewal/calc.do?c='.self::API_CHANNEL_CODE.'&requestId='.self::API_CHANNEL_CODE.time();;
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
		$data = $response->content->data;//获取密文
		$data = $this->aes_crypt_helper->decrypt($data, $key);
		return ['data'=>json_decode($data,true), 'code'=>200];
	}

	/**
	 * 续保出单
	 * @access public
	 * @URL outChannel/renewal/create.do?c=渠道代码&requestId=请求编号(渠道_时间戳)
	 * @requstr_type POST
	 * @return mixed
	 */
	public function issueRenewal(){
		$input = [];
		$input['policyNo'] = '9200200050348936';//policyNo	保单号	String	Y
		$input['outChannelOrderId'] = '9200200050348936';//外部渠道订单号	String	Y
		$input['benLevel'] = '01';//计划	String	Y
		$input['sumInsured'] = '400000';//保额	String	Y
		$input['isChange'] = 'Y';//是否转保(Y为是，N为否)	String	Y
		$input['productId'] = 'A000000042';//转保后产品ID（是否转保为Y时才传）	String	N
		$input['socialSecurity'] = 'N';//是否有社保 Y/N，默认为N	String	N
		$input['uwMedicalId'] = '9200200050348936';//核保号	String	N
		$input['renewalNo'] = '9200200050348936';//续保保单号	String	Y
		$input['nextTotPrem'] = '241.00';//续保保费，单位，元，两位小数	String	Y
		$input['healthNotes'] = [];//是否有社保 Y/N，默认为N	String	N
		$input['healthNotes'][0]['answer'] = '';//答案值Y/N	String	Y
		$input['healthNotes'][0]['questionId'] = '';//问题ID：参考”险种相关”健康告知ID	String	N
		$input['healthNotes'][0]['description'] = '';//描述	String	Y
		$input['healthNotes'][0]['healthNoteSeq'] = '';//告知批次号,1,2,3：如果接口方会记录且传给健康险历史告知记录则该字段，用于区分各批次健康告知，否则默认传值为1
		$request_data = [];
		$request_data['requestId'] =  self::API_CHANNEL_CODE.time();
		$key = self::API_INSURE_URL_KEY;//测试环境
		$input = json_encode($input);
		dump($input);
		$input = $this->aes_crypt_helper->encrypt($input,$key);
		$request_data['data'] = $input;
		dump(json_encode($request_data));
		$request_url = self::API_INSURE_URL.'outChannel/renewal/create.do?c='.self::API_CHANNEL_CODE.'&requestId='.self::API_CHANNEL_CODE.time();;
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
		$data = $response->content->data;//获取密文
		$data = $this->aes_crypt_helper->decrypt($data, $key);
		return ['data'=>json_decode($data,true), 'code'=>200];
	}

	/**
	 * 续保确认
	 * @access public
	 * @URL outChannel/renewal/confirm.do?c=渠道代码&requestId=请求编号(渠道_时间戳)
	 * @requstr_type POST
	 * @return mixed
	 */
	public function confirmRenewal(){
		$input = [];
		$input['orderId'] = '9200200050348936';//健康险订单号	String	Y
		$request_data = [];
		$request_data['requestId'] =  self::API_CHANNEL_CODE.time();
		$key = self::API_INSURE_URL_KEY;//测试环境
		$input = json_encode($input);
		dump($input);
		$input = $this->aes_crypt_helper->encrypt($input,$key);
		$request_data['data'] = $input;
		dump(json_encode($request_data));
		$request_url = self::API_INSURE_URL.'outChannel/renewal/confirm.do?c='.self::API_CHANNEL_CODE.'&requestId='.self::API_CHANNEL_CODE.time();;
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
		$data = $response->content->data;//获取密文
		$data = $this->aes_crypt_helper->decrypt($data, $key);
		return ['data'=>json_decode($data,true), 'code'=>200];
	}

	/**
	 * 电子发票查询
	 * @access public
	 * @URL outChannel/qryInvoice.do?c=渠道代码
	 * @requstr_type POST
	 * @return mixed
	 */
	public function selElectronicInvoice(){
		$input = [];
		$input['phone'] = '15701681524';//	phone	手机号码	String	是
		$input['policyNo'] = '9200200050348936';//保单号	String	是
		$request_data = [];
		$request_data['requestId'] =  self::API_CHANNEL_CODE.time();
		$key = self::API_INSURE_URL_KEY;//测试环境
		$input = json_encode($input);
		dump($input);
		$input = $this->aes_crypt_helper->encrypt($input,$key);
		$request_data['data'] = $input;
		dump(json_encode($request_data));
		$request_url = self::API_INSURE_URL.'outChannel/renewal/qryInvoice.do?c='.self::API_CHANNEL_CODE.'&requestId='.self::API_CHANNEL_CODE.time();;
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
		$data = $response->content->data;//获取密文
		$data = $this->aes_crypt_helper->decrypt($data, $key);
		return ['data'=>json_decode($data,true), 'code'=>200];
	}

//    =================================公共方法==================================

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