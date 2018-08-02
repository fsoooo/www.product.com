<?php

namespace App\Console\Commands;

use Mail, DB;
use App\Helper\LogHelper;
use Illuminate\Console\Command;
use App\Mail\BaiduWorkInsureList;

class BaiDuWorkClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'baidu_work_clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'baidu_work_clear Command description';

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
        //
        $connect = DB::connection('baidu_riders');
        $connect->beginTransaction();
        $day = date("Y-m-d");
        $data = array();
        //当天投保人员信息
        $email_data = $connect->table('insure_log')->whereNull('send_email_time')
//            ->where('insure_time', 'like', '%'.$day. '%')
            ->get();
        //当天拒保名单
        $refuse_data = $connect->table('work_table')->where('refuse_status', 1)
            ->where('first_join_day', $day)
            ->get();
        $data['day'] = $day;
        $data['email_data'] = $email_data;
        $data['refuse_data'] = $refuse_data;

        $user_ids = array();
        $log_ids = array();
        foreach($email_data as $k => $v){
            $user_ids[] = $v->userid;
            $log_ids[] = $v->id;
        }

        try{
            //发送邮件
            Mail::to([
                '494153534@qq.com', //国寿财
                'luying@yq16.net',  //百度
                'xuyn@inschos.com',
                'yangqi@inschos.com',
                'mas@inschos.com',
                'shimq@inschos.com',
                ])
                ->send(new BaiduWorkInsureList($data));

            $connect->table('work_table')->whereIn('userid', $user_ids)->delete();    //清除已投保的爬虫人员记录
            $connect->table('insure_log')->whereIn('id', $log_ids)->update(['send_email_time'=>date('Y-m-d H:i:s')]);
            $connect->commit();
        }catch(\Exception $e){
            $message = $e->getMessage();
            $connect->rollBack();
            LogHelper::logError($email_data, $message, 'baidu', 'clear_work_data');
        }

    }
}
