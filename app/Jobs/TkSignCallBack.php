<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Models\ApiFrom;
use App\Models\InsApiBrokerage;
use App\Models\OrderFinance;
use App\Models\Policy;
use App\Models\User;
use App\Repositories\InsuranceApiFromRepository;
use App\Repositories\InsuranceAttributesRepository;
use App\Repositories\RestrictGeneRepository;
use Carbon\Carbon;
use function Couchbase\defaultDecoder;
use Illuminate\Http\Request;
use App\Helper\RsaSignHelp;
use Illuminate\Support\Facades\DB;
use Ixudra\Curl\Facades\Curl;
use App\Models\InsOrder;
use App\Models\Insure;
use App\Helper\LogHelper;
;

class TkSignCallBack implements ShouldQueue
{
    /**
     * The number of seconds the job can run before timing out.
     * 超时时间
     * @var int
     */
    public $timeout = 120;

    /**
     * The number of times the job may be attempted.
     * 尝试次数
     * @var int
     */
    public $tries = 5;

    protected $param;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($param)
    {
        $this->param = $param;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $return  = $this->param;
        $response = Curl::to('http://yunda.inschos.com/channelsapi/contract_call_back')
            ->returnResponseObject()
            ->withData($return)
            ->asJson()
            ->withTimeout(60)
            ->post();
        if ($response->status != 200) {
            return json_encode(['state' => false, 'failMsg' => '回调处理失败'], JSON_UNESCAPED_UNICODE);
        }
//        $contract_code = $return['contract_code'];//签约协议号	当初请求自己生成的
//        $request_serial = $return['request_serial'];//请求序列号
//        $openid = $return['openid'];//用户的唯一标识	Appid下，用户的唯一标识
//        $change_type = $return['change_type'];//变更类型	ADD--签约 DELETE--解约
//        $operate_time = $return['operate_time'];//操作时间	yyyy-MM-dd HH:mm:ss
//        $contract_id = $return['contract_id'];//委托代扣协议id	签约成功后，微信返回的委托代扣协议id（代扣款请求时用）
//        $contract_expired_time = $return['contract_expired_time'];//协议到期时间	yyyy-MM-dd HH:mm:ss
    }
}
