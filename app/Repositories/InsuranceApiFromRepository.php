<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\InsuranceApiInfo;

class InsuranceApiFromRepository
{
    /**
     * 根据产品ID查询，选择状态为1的记录
     *
     * @param $insurance_id
     * @return mixed
     */
    public function getApiStatusOn($insurance_id)
    {
        return InsuranceApiInfo::where('insurance_id', $insurance_id)
            ->with('insurance')
            ->where('status', 1)
            ->first();
    }

    /**
     * 通过内部产品码查询，不判断状态
     *
     * @param $private_p_code
     * @return mixed
     */
    public function getApiByPrivatePCode($private_p_code)
    {
        return InsuranceApiInfo::where('private_p_code', $private_p_code)
            ->with('insurance')
            ->first();
    }

    /**
     * 根据绑定的ID，查询API来源ID
     *
     * @param $bind_id
     * @return mixed
     */
    public function getApiFromIdByBindId($bind_id)
    {
        return DB::table('insurance_api_from')
            ->where('id', $bind_id)
            ->pluck('api_from_id')
            ->first();
    }

    /**
     * 查询绑定关系
     *
     * @param $bind_id
     * @return mixed
     */
    public function getBindRelationByBindId($bind_id)
    {
        $select = [
            'c.name as api_from_name',
            'b.p_code',
            'b.status',
            'b.id as bind_id',
            'a.name as insurance_name'
        ];

        return DB::table('insurance as a') // a.产品表
            ->join('insurance_api_from as b', 'a.id', '=', 'b.insurance_id') // b.中间表
            ->join('api_from as c', 'c.id', '=', 'b.api_from_id') // c.API来源表
            ->where('b.id', $bind_id)
            ->select($select)
            ->first();
    }
}
