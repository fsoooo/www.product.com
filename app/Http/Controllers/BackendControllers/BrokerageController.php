<?php
/**
 * Created by PhpStorm.
 * User: xyn
 * Date: 2017/9/5
 * Time: 15:49
 */

namespace App\Http\Controllers\BackendControllers;

use App\Models\InsApiBind;
use App\Models\Insurance;
use App\Models\InsApiBrokerage;
use Illuminate\Support\Facades\DB;

class BrokerageController extends BaseController
{
    /**
     * 佣金管理
     * @param $bind_id
     * @return mixed
     */
    public function index($bind_id)
    {
        $bind = InsApiBind::with(['insurance', 'apiFrom', 'insApiBrokerage'=>function($q){
            $q->where('status', 1);
        }])->find($bind_id);
        return view('backend.insurance.ratio', compact('bind'));
    }

    /**
     * 佣金管理添加提交
     * @return mixed
     */
    public function doSubmit()
    {
        $data = $this->request->all();
        \DB::beginTransaction();
        try{
            $bind = InsApiBind::find($data['bind_id']);
            InsApiBrokerage::where('bind_id', $bind->id)->update(['status'=> 0]);
            $insert_data = array();
            foreach($data['pay_type'] as $k => $pv){
                if(is_null($pv))
                    return redirect('backend/product/brokerage/show/' . $data['bind_id'])->withErrors('部分缴期方式为空');
                if(!$data['ratio_for_us'][$k])
                    return redirect('backend/product/brokerage/show/' . $data['bind_id'])->withErrors('部分内部收益佣金比为空');
                if(!$data['ratio_for_out'][$k])
                    return redirect('backend/product/brokerage/show/' . $data['bind_id'])->withErrors('部分渠道支出佣金比为空');
                if($data['ratio_for_out'][$k] > $data['ratio_for_us'][$k])
                    return redirect('backend/product/brokerage/show/' . $data['bind_id'])->withErrors('渠道支出佣金不得大于内部所获佣金');
                $insert_data[$k] = [
                    'insurance_id' => $bind->insurance_id,
                    'api_from_id' => $bind->api_from_id,
                    'bind_id' => $bind->id,
                    'private_p_code' => $bind->private_p_code,
                    'p_code' => $bind->p_code,
                    'by_stages_way' => $pv,
                    'pay_type_unit' => $data['pay_type_unit'][$k],
                    'ratio_for_us' => $data['ratio_for_us'][$k],
                    'ratio_for_agency' => $data['ratio_for_out'][$k],
                ];
            }
            InsApiBrokerage::insert($insert_data);
            \DB::commit();
            return redirect('backend/product/brokerage/show/' . $data['bind_id'])->with('status', '成功更新佣金数据!');
        } catch(\Exception $e){
            \DB::rollBack();
            return redirect('backend/product/brokerage/show/' . $data['bind_id'])->withErrors('更新佣金数据失败');
        }
    }

    /**
     * Boss佣金统计
     */
    public function brokerage()
    {
        //todo   查询的数据库表
        //ins_order_finance
        //insurance
        //api_from
        $select = [
            'a.id',
            'c.name as api_from_name',
            'b.name as insurance_name',
            'a.brokerage_for_us',
            'a.brokerage_for_agency',
            'a.us_settlement_status as us_settlement_status',
            'a.agency_settlement_status as agency_settlement_status',
            'a.created_at as c_at'
        ];
        $lists = DB::table('ins_order_finance as a')
            ->join('insurance as b', 'a.insurance_id', '=', 'b.id')
            ->join('api_from as c', 'a.api_from_id', '=', 'c.id')
//            ->join('ins_order as d', 'a.union_order_code', '=', 'd.union_order_code')
//            ->where('d.status', 'pay_end')
            ->select($select)
            ->paginate(config('list_num.backend.finance'));

        $all = DB::table('ins_order_finance as a')
            ->join('insurance as b', 'a.insurance_id', '=', 'b.id')
            ->join('api_from as c', 'a.api_from_id', '=', 'c.id')
            ->select($select)
            ->get();

        $sum['us'] = $all->sum('brokerage_for_us');
        $sum['agency'] = $all->sum('brokerage_for_agency');
        $status = $this->getStatus();
        return view('backend.backend.boss.brokerage.brokerage', compact('lists', 'status','sum'));
    }

    /**
     * 佣金统计状态标识
     * @return array
     */
    protected function getStatus()
    {
        return [
            'us_settlement_status' => [
                0 => '未结算',
                1 => '结算中',
                2 => '已结算',
                3 => '已结算'
            ],
            'agency_settlement_status' => [
                0 => '未结算',
                1 => '结算中',
                2 => '已结算',
                3 => '已结算'
            ]
        ];
    }
}