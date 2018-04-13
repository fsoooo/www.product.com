<?php
namespace App\Helper;
use App\Models\WarrantyRecognizee;
use DB;
use Ixudra\Curl\Facades\Curl;
use App\Models\Warranty;
use League\Flysystem\Exception;
use App\Models\WarrantyRule;
class Issue{  //出单接口

protected  $signHelp;
    public function __construct()
    {
        $this->signHelp = new RsaSignHelp();
    }

    public function issue($api_from_uuid,$warranty_rule)
    {

        $order_id = $warranty_rule->order_id;
        //获取被保人保单号
        $recognizee = WarrantyRecognizee::where('order_id',$order_id)
            ->first();
        if($api_from_uuid == 'Wk'){//悟空保出单接口
            $biz_content = array(
                'orderCode'=>$recognizee->order_code,
                'unionOrderCode'=>$warranty_rule->union_order_code,
                'productCode'=>$warranty_rule->warranty_product->product_number,
            );

            //天眼接口参数封装
            $data = $this->signHelp->tySign($biz_content, ['api_from_uuid' => $api_from_uuid]);
            $response = Curl::to(env('TY_API_SERVICE_URL') . '/ins_curl/issue')
                ->returnResponseObject()
                ->withData($data)
                ->withTimeout(60)
                ->post();
            $status = $response->status;
            $content = $response->content;
            if($status == 200){
                $result = $this->addWarranty($warranty_rule,$content);
                if($result){
                    return $result;
                }else{
                    return false;
                }
            }else{
                return false;
            }

        }

    }


    //写一个方法，用来添加保单信息
    public function addWarranty($warranty_rule,$content)
    {
        $content = json_decode($content);
        DB::beginTransaction();
        try{//添加到保单表中，添加到关联表中
            $Warranty = new Warranty();
            $Warranty->warranty_code =
            $Warranty->deal_type = 0;  //成交方式，线上成交
            $Warranty->start_time = $content->policyBeginDate;
            $Warranty->end_time = $content->policyEndDate;
            $warranty_id = DB::table('warranty')->insertGetId(
                array(
                    'warranty_code'=>$content->supplierInsurancePolicyCode,
                    'deal_type'=>0,
                    'start_time'=>$content->policyBeginDate,
                    'end_time'=>$content->policyEndDate,
                )
            );
            $id = $warranty_rule->id;
            $WarrantyRule = WarrantyRule::find($id);
            $WarrantyRule->warranty_id = $warranty_id;
            $result = $WarrantyRule->save();
            if($warranty_id&&$result){
                DB::commit();
                return $warranty_id;
            }else{
                DB::rollBack();
                return false;
            }
        }catch (Exception $e){
            DB::rollBack();
            return false;
        }



    }


}



