<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Jobs\DemoTest;
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


class TkPayCallBack implements ShouldQueue
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
        $return = $this->param;
        LogHelper::logSuccess($return, 'Tk_pay_call_back');
        try{
            DB::beginTransaction();
            if(!is_array($return)){
                $return = json_decode($return,true);
            }
            $reason = urldecode(urldecode($return['reason']));
            $order = InsOrder::where(['union_order_code'=> $return['proposalNo'], 'api_from_uuid'=> 'Tk'])->first();
            if(empty($order)){
                return json_encode(['state'=>false, 'failMsg'=>'回调处理失败'], JSON_UNESCAPED_UNICODE);
            }
            $user = User::where('account_id',$order['create_account_id'])->first();
            if($return['result']){  //支付成功
                $order->status = 'pay_end';
                $order->pay_code = $return['billno'];
                $order->save();
                Insure::where('ins_order_id', $order->id)->update(['status'=> 'pay_end']);
                //查佣金比，统计财务
                $by_stages_way = preg_replace('/[^0-9]+/', '', $order->by_stages_way);
                $brokerage = InsApiBrokerage::where([
                    ['bind_id', '=', $order->bind_id],
                    ['insurance_id', '=', $order->ins_id],
                    ['status', '=', 1],
                    ['by_stages_way', '=', $by_stages_way],
                ])->first();
                $finance = new OrderFinance();
                $finance->order_id = $order->id;
                $finance->insurance_id = $order->ins_id;
                $finance->api_from_id = $brokerage->api_from_id;
                $finance->brokerage_id = $brokerage->id;
                $finance->union_order_code = $order->union_order_code;
                $finance->p_code = $order->p_code;
                $finance->private_p_code = $brokerage->private_p_code;
                $finance->brokerage_for_us = $order->total_premium * $brokerage->ratio_for_us / 100 ;
                $finance->brokerage_for_agency = $order->total_premium * $brokerage->ratio_for_agency / 100;
                $finance->save();
                $data = [
                    "notice_type"=> 'pay_call_back',
                    'data' => [
                        'status'=>true,
                        'ratio_for_agency'=> $brokerage->ratio_for_agency,
                        'brokerage_for_agency'=> $finance->brokerage_for_agency,
                        'union_order_code' => $return['proposalNo'],
                        'by_stages_way' => $order->by_stages_way,
                        'error_message' => $reason??"",
                    ]
                ];
            }else{
                Insure::where('ins_order_id', $order->id)->update(['status'=>'pay_error', 'pay_error_message'=>$return['reason']]);
                $data = [
                    "notice_type     "=> 'pay_call_back',
                    'data' => [
                        'status'=>false,
                        'account_id' => $user->account_id,
                        'union_order_code' => $return['proposalNo'],
                        'error_message' => $reason??"",
                    ]
                ];
            }
            $response = Curl::to('http://yunda.inschos.com/ins/call_back')
                ->returnResponseObject()
                ->withData($data)
                ->asJson()
                ->withTimeout(60)
                ->post();
            LogHelper::logSuccess($response, 'Tk_callback_yunda');
            if($response->status!=200){
                return json_encode(['state'=>false, 'failMsg'=>'回调处理失败'], JSON_UNESCAPED_UNICODE);
            }
            DB::commit();
            $res = json_encode(['state'=>true]);
            return $res;
        } catch (\Exception $e ){
            DB::rollBack();
            LogHelper::logError($return, json_encode($e->getMessage(), JSON_UNESCAPED_UNICODE), 'TK');
            return json_encode(['state'=>false, 'failMsg'=>'回调处理失败'], JSON_UNESCAPED_UNICODE);
        }
    }
}
