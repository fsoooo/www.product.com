<?php
/**
 * Created by PhpStorm.
 * User: wangsl
 * Date: 2017/5/24
 * Time: 18:45
 */
namespace App\Http\Controllers\BackendControllers;
header("Content-Type:application/json; charset=utf8");
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inter;
use Illuminate\Support\Facades\DB;
use App\Helper\Ucpaas;
use App\Helper\Email;
use App\Models\MsgOrder;

header('Access-Control-Allow-Origin:*');

class InterOrdersController extends Controller
{
    public function smsOrder(){
        $nums = MsgOrder::all()->where('ispay','1');
        // $nums = Msg_order::count()->where('company',$_GET['id'])->where('name',$_GET['type']);
        $count = count($nums);
        return view('backend.order.smsindex')->with(['nums'=>$nums,'count'=>$count]);

    }
    public function emailOrder(){
        return '邮箱订单';
    }
    
	
	public function doPay(){
		//把对象转换成数组
    	$object = unserialize($_GET['data']);
    	if (is_object($object)) {
		    foreach ($object as $key => $value) {
		      $array[$key] = $value;
		    }
		  }
		  else {
		    $array = $object;
		  }
		  // return $	array;
		  $order_num = $array['order_num'];
		  $company = $array['company'];
		  $money = $array['money'];
		  $paytype = $array['pay_type'];
		  $name = $array['name'];
		  var_dump($paytype);
		  if($paytype=="alipay"){
//		  	return '支付宝接口';
		  	return view('backend.order.alipay')->with('data',$order_num)->with('company',$company);
		  }elseif($paytype=='wechat'){
//		  	return '微信支付接口';
              return view('backend.order.wechat');
		  }elseif($paytype=='bank'){
//		  	return '银行卡支付接口';
              return view('backend.order.bank');
		  }
   		
	}


	public function doReply(){
        // var_dump($_GET);
        $status = $_GET['status'];
        $order = $_GET['order'];
        $object = DB::table('Msg_orders')->where('order_num',$order)->first();
        // var_dump($object);
        //对象转换数组
        if (is_object($object)) {
            foreach ($object as $key => $value) {
                $array[$key] = $value;
            }
        }else{
            $array = $object;
        }

        // var_dump($array);
        $company = $array['company'];
        $money = $array['money'];

        $name  = $array['name'];
        $datas = DB::table('inters')->where('compony_id',$company)->where('name',$name)->first();
        if (is_object($datas)) {
            foreach ($datas as $key => $value) {
                $arrays[$key] = $value;
            }
        }else {
            $arrays = $datas;
        }
        // var_dump($arrays);
        $token =  $arrays['token'];
        // var_dump($token);
       // var_dump($order);
       // var_dump($status);
	    if($status =="1"){

            
	        // var_dump($res);
            //curl 返回
            $ch = curl_init();//初始化curl
            // var_dump($ch);
            //提交路径
            $url = "doreply?status=".$status.'&order='.$order;
            //设置选项
            // var_dump($url);
            curl_setopt($ch,CURLOPT_URL,$url);//路径
            // curl_setopt($ch,CURLOPT_POST,true);//post方式提交
            // curl_setopt($ch,CURLOPT_POSTFIELDS,$data);//设置提交数据
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);//设置返回获取的输出为文本流
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);//连接超时,3秒未连接上，直接退出
            curl_setopt($ch,CURLOPT_TIMEOUT, 20);//接收数据时超时设置,如果20秒内数据未接收完，直接退出
            curl_setopt($ch,CURLOPT_HEADER,0);
            //执行选项
            curl_exec($ch);
           // echo $response;
            //关闭
            curl_close ($ch);

            DB::table('inters')->where('token',$token)->update(['money' =>$money,'status'=>'1' ]);
            return (['status' => 0, 'message' => 'success']);
        }else{
            return (['status' => 1, 'message' => 'error']);
        }
    }


    public function payInfo(){
	    return '查看订单号为'.$_GET['id'].'订单详情';
    }

    public function doSmsOrder(){

        $data = unserialize($_GET['data']);
            // var_dump($data);
        // $res = Msg_order::insert(['name'=>$data['name'],'order_num'=>$data['order_num'],'company'=>$data['company'],'create_time'=>time(),'pay_type'=>$data['pay_type'],'money'=>$data['money']]);
            // var_dump($res);
         $res = DB::table('msg_orders')->insert(['name'=>$data['name'],'order_num'=>$data['order_num'],'company'=>$data['company'],'create_time'=>time(),'pay_type'=>$data['pay_type'],'money'=>$data['money']]);

         if($res){

          return (['status' => 0, 'message' => '订单提交success']);
         }
    }
}