<?php

namespace App\Http\Controllers\FrontendControllers;


use Mail, DB;
use App\Helper\RsaSignHelp;
use Illuminate\Http\Request;
use App\Mail\BaiduWorkInsureList;

class IndexController extends BaseController
{
    public function index(Request $request)
    {
//        $input = $request->all();
//        $str = $input['str'];
//        $h = new RsaSignHelp();
//        $a = $h->tyDecodeOriginData($str);
//        dd($a);

//        $json = '{
//	"new_val": "[{\"protectItemId\":\"0\",\"sort\":-1,\"ty_key\":\"ty_age\",\"value\":\"出生满30日-70周岁\"},{\"protectItemId\":\"0\",\"sort\":1,\"ty_key\":\"ty_buy_count\",\"value\":\"1份\"},{\"protectItemId\":\"0\",\"sort\":2,\"ty_key\":\"ty_duration_period_value\",\"value\":\"7天\"},{\"protectItemId\":\"0\",\"sort\":4,\"ty_key\":\"coverageB\",\"value\":\"50万元\"},{\"protectItemId\":\"0\",\"sort\":5,\"ty_key\":\"coverageC\",\"value\":\"20万元\"},{\"protectItemId\":\"0\",\"sort\":6,\"ty_key\":\"coverageD\",\"value\":\"10万元\"},{\"protectItemId\":\"0\",\"sort\":7,\"ty_key\":\"isInsure\",\"value\":\"含\"}]",
//	"old_val": "{\"protectItemId\":\"0\",\"sort\":2,\"ty_key\":\"ty_duration_period_value\",\"value\":\"7天\"}",
//	"old_option": "[{\"defaultValue\":\"出生满30日-70周岁\",\"display\":true,\"name\":\"承保年龄\",\"sort\":-1,\"ty_key\":\"ty_age\",\"type\":6,\"values\":[{\"max\":70,\"min\":0,\"step\":0,\"subDictionary\":{\"min\":1,\"unit\":\"天\"},\"ty_value\":\"出生满30日-70\",\"type\":\"1\",\"unit\":\"周岁\"}]},{\"defaultValue\":\"1份\",\"display\":true,\"name\":\"购买份数\",\"sort\":1,\"ty_key\":\"ty_buy_count\",\"type\":6,\"values\":[{\"max\":1,\"min\":1,\"step\":0,\"ty_value\":\"1\",\"type\":\"1\",\"unit\":\"份\"}]},{\"defaultValue\":\"7天\",\"display\":true,\"name\":\"保障期限\",\"sort\":2,\"ty_key\":\"ty_duration_period_value\",\"type\":0,\"values\":[{\"max\":7,\"min\":7,\"step\":0,\"ty_value\":\"7\",\"type\":\"1\",\"unit\":\"天\"},{\"max\":15,\"min\":15,\"step\":0,\"ty_value\":\"15\",\"type\":\"1\",\"unit\":\"天\"},{\"max\":30,\"min\":30,\"step\":0,\"ty_value\":\"30\",\"type\":\"1\",\"unit\":\"天\"},{\"max\":366,\"min\":366,\"step\":0,\"ty_value\":\"366\",\"type\":\"1\",\"unit\":\"天\"}]},{\"defaultValue\":\"50万元\",\"display\":false,\"name\":\"节假日民航班机意外身故\",\"sort\":4,\"ty_key\":\"coverageB\",\"type\":0,\"values\":[{\"max\":50,\"min\":50,\"step\":1,\"ty_value\":\"50\",\"type\":\"1\",\"unit\":\"万元\"}]},{\"defaultValue\":\"20万元\",\"display\":false,\"name\":\"节假日列车、轮船意外身故\",\"sort\":5,\"ty_key\":\"coverageC\",\"type\":0,\"values\":[{\"max\":20,\"min\":20,\"step\":1,\"ty_value\":\"20\",\"type\":\"1\",\"unit\":\"万元\"}]},{\"defaultValue\":\"10万元\",\"display\":false,\"name\":\"节假日公共汽车、班车意外身故\",\"sort\":6,\"ty_key\":\"coverageD\",\"type\":0,\"values\":[{\"max\":10,\"min\":10,\"step\":1,\"ty_value\":\"10\",\"type\":\"1\",\"unit\":\"万元\"}]},{\"defaultValue\":\"含\",\"display\":true,\"name\":\"法定节假日意外身故\",\"sort\":7,\"ty_key\":\"isInsure\",\"type\":0,\"values\":[{\"max\":0,\"min\":0,\"step\":0,\"ty_value\":\"含\",\"type\":\"1\"},{\"max\":0,\"min\":0,\"step\":0,\"ty_value\":\"不含\",\"type\":\"1\"}]}]",
//	"private_p_code": "\"UXgtUVgwMDAwMDAwMDE2MTY\"",
//	"old_protect_item": "[{\"defaultValue\":\"100万元\",\"description\":\"在保险期间内,若被保险人以乘客身份在乘坐民航班机时遭受意外伤害且自该意外伤害发生之日起180日内身故,保险公司给付身故保险金,本合同终止。\",\"name\":\"民航班机意外身故\",\"protectItemId\":7963,\"sort\":0},{\"defaultValue\":\"100万元\",\"description\":\"在保险期间内,若被保险人以乘客身份在乘坐民航班机时遭受意外伤害且自该意外伤害发生之日起180日内因该意外伤害导致伤残,保险公司按照《人身保险伤残评定标准(行业标准)》比例给付伤残保险金,累计达到保险金额时,本合同终止。\",\"name\":\"民航班机意外伤残-1类\",\"protectItemId\":7964,\"sort\":0},{\"defaultValue\":\"25万元\",\"description\":\"在保险期间内,若被保险人以乘客身份在乘坐列车或轮船时遭受意外伤害且自该意外伤害发生之日起180日内身故,保险公司给付身故保险金,本合同终止。\",\"name\":\"列车、轮船意外身故\",\"protectItemId\":7965,\"sort\":0},{\"defaultValue\":\"25万元\",\"description\":\"在保险期间内,若被保险人以乘客身份在乘坐列车或轮船时遭受意外伤害且自该意外伤害发生之日起180日内因该意外伤害导致伤残,保险公司按照《人身保险伤残评定标准(行业标准)》比例给付伤残保险金,累计达到保险金额时,本合同终止。\",\"name\":\"列车、轮船意外伤残-1类\",\"protectItemId\":7966,\"sort\":0},{\"defaultValue\":\"10万元\",\"description\":\"在保险期间内,若被保险人以乘客身份在乘坐公共汽车、班车时遭受意外伤害且自该意外伤害发生之日起180日内身故,保险公司给付身故保险金,本合同终止。\",\"name\":\"公共汽车、班车意外身故\",\"protectItemId\":7967,\"sort\":0},{\"defaultValue\":\"10万元\",\"description\":\"在保险期间内,若被保险人以乘客身份在乘坐公共汽车、班车时遭受意外伤害且自该意外伤害发生之日起180日内因该意外伤害导致伤残,保险公司按照《人身保险伤残评定标准(行业标准)》比例给付伤残保险金,累计达到保险金额时,本合同终止。\",\"name\":\"公共汽车、班车意外伤残-1类\",\"protectItemId\":7968,\"sort\":0},{\"defaultValue\":\"50万元\",\"description\":\"在保险期间内(法定节假日期间),若被保险人以乘客身份在乘坐民航班机时遭受意外伤害且自该意外伤害发生之日起180日内身故,保险公司给付身故保险金,本合同终止。(本保障可与民航班机意外身故累计赔付)\",\"name\":\"节假日民航班机意外身故\",\"protectItemId\":7969,\"relateCoverage\":\"coverageB\",\"sort\":0},{\"defaultValue\":\"20万元\",\"description\":\"在保险期间内(法定节假日期间),若被保险人以乘客身份在乘坐列车或轮船时遭受意外伤害且自该意外伤害发生之日起180日内身故,保险公司给付身故保险金,本合同终止。(本保障可与列车、轮船意外身故累计赔付)\",\"name\":\"节假日列车、轮船意外身故\",\"protectItemId\":7970,\"relateCoverage\":\"coverageC\",\"sort\":0},{\"defaultValue\":\"10万元\",\"description\":\"在保险期间内(法定节假日期间),若被保险人以乘客身份在乘坐公共汽车、班车时遭受意外伤害且自该意外伤害发生之日起180日内身故,保险公司给付身故保险金,本合同终止。(本保障可与公共汽车、班车意外身故累计赔付)\",\"name\":\"节假日公共汽车、班车意外身故\",\"protectItemId\":7971,\"relateCoverage\":\"coverageD\",\"sort\":0}]"
//}';
//        $data = json_decode($json, true);
//
//        $a = $h->tySign($data);
//        dd($a);
//        $day = date("Y-m-d");
//        $data = array();
//        $connect = DB::connection('baidu_riders');
//        //当天投保人员信息
//        $email_data = $connect->table('insure_log')->whereNull('send_email_time')
////            ->where('insure_time', 'like', '%'.$day. '%')
//            ->get();
//        //当天拒保名单
//        $refuse_data = $connect->table('work_table')->where('refuse_status', 1)
//            ->where('first_join_day', $day)
//            ->get();
//        $data['day'] = $day;
//        $data['email_data'] = $email_data;
//        $data['refuse_data'] = $refuse_data;
//
//        $user_ids = array();
//        $log_ids = array();
//        foreach($email_data as $k => $v){
//            $user_ids[] = $v->userid;
//            $log_ids[] = $v->id;
//        }
//
//        $connect->beginTransaction();
//        try{
//            //发送邮件
//            Mail::to([
//                'xuyn@inschos.com',
//            ])
//                ->send(new BaiduWorkInsureList($data));
//            $connect->table('work_table')->whereIn('userid', $user_ids)->delete();    //清除已投保的爬虫人员记录
//            $connect->table('insure_log')->whereIn('id', $log_ids)->update(['send_email_time'=>date('Y-m-d H:i:s')]);
//            $connect->commit();
//
//        } catch (\Exception $e){
//            $message = $e->getMessage();
//            $connect->rollBack();
//            dd($message);
////            LogHelper::logError($email_data, $message, 'baidu', 'clear_work_data');
//        }
//        phpinfo();
//        $job = json_decode(config('hg_msg.job'),true);
////        dump(count($job));
//        foreach($job as $key=>$value){
//            if($value['id'] == '20206'&&$value['type']=='7'){
////         dump($key);
//            }
//        }
////        dump($job['39']);
////        dump($job['40']);
//        unset($job['40']);
//        rsort($job);
////        dump(count($job));
////        dump($job);
//        print_r(json_encode($job));
        return view('frontend.index.index');
    }
}
