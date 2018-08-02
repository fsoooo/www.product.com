<?php

namespace App\Console\Commands;

use DB;
use App\Helper\LogHelper;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Console\Command;
use App\Helper\IdentityCardHelp;



class BaiDu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'baidu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'baidu Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $connect = DB::connection('baidu_riders');
        //查询可投保数据
        $res = $connect
            ->table('work_table')
            ->where('insure_status', '0')
            ->where('refuse_status', '<>', '1')
            ->take(10)
            ->get();
        if(!count($res))
            die;

        //封装初始信息
        $data = array();
        $data['TRANS_ID'] = time(). rand(100000, 999999);   //交易码
        $data['TRANS_DATE'] = date('YmdHis'); //日期
        $time = date('Y-m-d H:i:s');
        $start_time = date('Ymd');
        $end_time = date('Ymd', strtotime($time ." +23 hours 59 minutes 59 seconds"));
        $refuse_ids = array();
        $insured = array();

        //身份证验证
        foreach($res as $k => $v){
            $card_number_verification = IdentityCardHelp::getIDCardInfo($v->idcredit);
            //不合规
            if(($card_number_verification['status'] != 2) || ($card_number_verification['isAdult'] == 1)){
                $refuse_ids[] = $v->id;
                unset($res[$k]);
            } else {
                $insured[] = [
                    "INSUREDNAME"=> $v->chinesename,
                    "IDENTIFYNUMBER"=> $v->idcredit,
                    "STARTDATE"=> $start_time,
                    "ENDDATE"=> $end_time
                ];
            }
        }

        //身份证校验失败处理
        $connect->table('work_table')->whereIn('id', $refuse_ids)->update(['refuse_status'=>1, 'refuse_message'=>'身份证信息有误或未成年']);

        $data['BASE_PART']['insInfos']['insInfo'] = $insured;
        $response = Curl::to('http://218.17.219.106:9090/baiduRiding/execute.action')
            ->returnResponseObject()
            ->withData($data)
            ->asJson()
            ->withTimeout(60)
            ->post();

        if($response->content->RETURN_MESSAGE == '成功'){
            $insert = array();
            $ids = array();
            foreach($res as $k => $v){
                $ids[] = $v->id;
                $insert[$k]['order_no'] = $data['TRANS_ID'];
                $insert[$k]['userid'] = $v->userid;
                $insert[$k]['chinesename'] = $v->chinesename;
                $insert[$k]['idcredit'] = $v->idcredit;
                $insert[$k]['phone'] = $v->phone;
                $insert[$k]['first_join_time'] = $v->first_join_time;
                $insert[$k]['insure_time'] = $time;
            }
            $connect->beginTransaction();
            try{
                $connect->table('work_table')->whereIn('id', $ids)->update(['insure_time'=>$time,'insure_status'=>'1']);
                $connect->table('insure_log')->insert($insert);
                $connect->commit();
            }catch(\Exception $e){
                $message = $e->getMessage();
                $connect->rollBack();
                LogHelper::logError($data, $message, 'baidu', 'add_and_update_data');
            }

        } else {
            LogHelper::logError($data, 'curl_error', 'baidu', 'insure');
        }
    }
}
