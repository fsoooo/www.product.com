<?php

namespace App\Http\Controllers\ApiControllers;

use App\Models\InsuranceApiInfo;
use App\Repositories\InsuranceApiFromRepository;
use Validator, DB, Image, Schema;
use App\Models\Company;
use App\Models\Clause;
use App\Models\Category;
use App\Models\Insurance;
use App\Helper\RsaSignHelp;
use Illuminate\Http\Request;

class InsuranceController extends BaseController
{
    protected $sign_help;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->sign_help = new RsaSignHelp();
    }

    public function index()
    {
        $b = $this->request->get('biz_content');
        $input = json_decode($this->sign_help->base64url_decode(strrev($b)), true);
        $page = !empty($input['page']) ? $input['page']-1 : 0;
        $num = !empty($input['num']) ? $input['num'] : config('list_num.api.insurance');
        $res = $num;//每页显示几条
        $product_apis = InsuranceApiInfo::where('status','1')->get();
        $insurance_ids = [];
        foreach ($product_apis as $v){
            $insurance_ids[] = $v['insurance_id'];
        }
        $insurance_ids = array_unique($insurance_ids);
        $i = Insurance::where('status', 'on')
            ->whereIn('id',$insurance_ids)
            ->with(['category', 'company', 'clauses'])
            ->count();
        $insurance = Insurance::where('status', 'on')
            ->with(['category', 'company', 'clauses'])
            ->whereIn('id',$insurance_ids)
            ->skip($page*$num)
            ->take($num)
            ->offset(($page-1)*$res)
            ->limit($res)
            ->get();
        $result = Insurance::paginate($res);
        $pages = $result->lastPage();
        $data['result'] = $insurance->toArray();
        $data['limit'] = [
            'page' => $input['page'],
            'pages' => $pages,
            'count' => $i
        ];
        return response()->json(['status' => true, 'data' => $data], 200);
    }
    

    
}