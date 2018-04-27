<?php

namespace App\Http\Controllers\ApiControllers;

use App\Models\Insurance;
use App\Models\Duty;
use App\Models\Tariff;
use App\Models\Clause;
use App\Models\User;
use App\Models\Company;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Helper\RsaSignHelp;
use App\Models\InsuranceApiInfo;
use Validator, DB, Image, Schema;


class IndexController extends BaseController{

    protected $sign_help;
    //初始化
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->sign_help = new RsaSignHelp();
    }

    public function index()
    {
        echo 'insurance api';
    }

    /**
     * 中介公司获取产品列表
     * @return mixed
     */
    public function getData()
    {
        $all = $this->request->all();
        $b = $this->request->get('biz_content');
        $input = $this->sign_help->tyDecodeOriginData($b);  //解析源数据
        $page = isset($input['page']) ? $input['page'] : 1; //页码
        $num = isset($input['num']) ? $input['num'] : config('list_num.api.insurance'); //单页数量
        $user = User::where('account_id', $all['account_id'])->first();
//        $sell_status = $user->sell_status;
		$sell_status = '3';
        $input['ids'] = isset($input['ids']) ? $input['ids'] : [];

        //符合条件产品总数
        $count = Insurance::where(['status'=> 'on', ['sell_status','>=', $sell_status]])
            ->whereNotIn('id',$input['ids'])
            ->where('base_price','<>',' ')
            ->where('base_stages_way','<>',' ')
            ->where('base_ratio','<>',' ')
            ->select('id')
            ->count();
        //当前页产品
        $insurance = Insurance::where(['status'=> 'on', ['sell_status','>=', $sell_status]])
            ->whereNotIn('id',$input['ids'])
            ->where('base_price','<>',' ')
            ->where('base_stages_way','<>',' ')
            ->where('base_ratio','<>',' ')
            ->offset(($page-1)*$num)
            ->limit($num)
            ->with(['category', 'company', 'clauses','insurance_api_info',])
            ->get();

        $companys = [];
        $category = [];
        if($count > 0){
            //公司
            $company_res = Company::get();
            foreach ($company_res as $value){
                $companys[] = $value['display_name'];
            }
            $companys = array_unique($companys);

            //分类
            $category  = Category::where('status','on')
                ->select(DB::raw('*, concat(path,id) as npath'))
                ->orderBy('npath', 'asc')
                ->get();
        }

        $pages = ceil($count/$num);
        $data['result'] = $insurance->toArray();
        $data['limit'] = [
            'page' => $page,    //当前页数
            'pages' => $pages,  //总页数
            'count' => $count   //符合条件产品数量
        ];
        $data['category'] = $category;
        $data['companys'] = $companys;
        return response()->json(['status' => true, 'data' => $data], 200);
    }
    /**
     * 中介公司所选择的的商品详情
     * @return array
     */
    public function getProductInfo()
    {
        $biz_content = $this->request->get('biz_content');
        $biz_content = $this->sign_help->tyDecodeOriginData($biz_content);  //解析源数据
        $product_id = $biz_content['productid'];
        $product_ids = [];
        $product_ids[] = $product_id;
        $result = Insurance::where('status', 'on')
            ->whereIn("id", $product_ids)
            ->with([
                'category', 'company','insurance_api_info', 'clauses'=>function($q){
                    $q->where('status', 'on')->withPivot('coverage_bs');
                },
                'clauses.duties'=>function($q) use($product_ids) {
                    $q->where('status', 'on')->where('need_coverage', 1)->wherePivotIn('ins_id', $product_ids)->withPivot('coverage_jc', 'ins_id', 'duty_id');
                },
                'binds' => function($q){
                    $q->where('status', 1);
                },
                'binds.insApiBrokerage' => function($query){
                    $query->where('status', 1)
                        ->select(['id','by_stages_way', 'pay_type_unit', 'ratio_for_agency', 'bind_id']);
                }
            ])
            ->get();
        $clause = [];
        foreach ($result as $k => $value)
        {
            $clause = $value->clauses;
            if($value->binds){
                $result[$k]['brokerage'] = $value->binds[0]->insApiBrokerage;
                unset($result[$k]['binds']);
            }
            foreach($value->clauses as $ck => $cv){
                foreach($cv->duties as $dk => $dv){
                    if($dv->pivot->ins_id != $value->id)
                        unset($result[$k]['clauses'][$ck]['duties'][$dk]);
                }
            }
        }

        $clauses_id = [];
        foreach ($clause as $value){
            $clauses_id[] = $value['id'];
        }
        $clause = Clause::whereIn('id',$clauses_id)
            ->with('category','company','duties')
            ->get();
        $categorys  = Category::where('status','on')
            ->select(DB::raw('*, concat(path,id) as npath'))
            ->orderBy('npath', 'asc')
            ->get();
        $data = ['status'=>'true','data'=>['res'=>$result,'clause'=>$clause,'category'=>$categorys]];
        $data = json_encode($data);
        return $data;
    }
    /**
     * 中介同步产品
     * @return array
     */
    public function getProducts()
    {
        $biz_content = $this->request->get('biz_content');
        $biz_content = $this->sign_help->tyDecodeOriginData($biz_content);  //解析源数据
//        $biz_content  = json_decode($this->sign_help->base64url_decode(strrev($biz_content)), true);
        $product_id = $biz_content['product_id'];//数组
        $result = Insurance::whereIn('id',$product_id)
            ->where('status', 'on')
            ->with([
                    'category', 'company','insurance_api_info', 'clauses'=>function($q){
                    $q->where('status', 'on')->withPivot('coverage_bs');
                },
                'clauses.duties'=>function($q) use($product_id) {
                    $q->where('status', 'on')->where('need_coverage', 1)->wherePivotIn('ins_id', $product_id)->withPivot('coverage_jc', 'ins_id', 'duty_id');
                },
                    'binds' => function($q){
                        $q->where('status', 1);
                    },
                    'binds.insApiBrokerage' => function($query){
                        $query->where('status', 1)
                         ->select(['id','by_stages_way', 'pay_type_unit', 'ratio_for_agency', 'bind_id']);
                    },
                    'insuranceHealth'
                ])
            ->get();
        $company_res = Company::get();
        $companys = [];
        foreach ($company_res as $value){
            $companys[] = $value['display_name'];
        }
        $companys = array_unique($companys);
        $clause = [];
        foreach ($result as $k => $value)
        {
            $clause = $value->clauses;
            if($value->binds){
                $result[$k]['brokerage'] = $value->binds[0]->insApiBrokerage;
                unset($result[$k]['binds']);
            }
            foreach($value->clauses as $ck => $cv){
                foreach($cv->duties as $dk => $dv){
                    if($dv->pivot->ins_id != $value->id)
						unset($result[$k]['clauses'][$ck]['duties'][$dk]);
                }
            }
        }

        $clauses_id = [];
        foreach ($clause as $value){
            $clauses_id[] = $value['id'];
        }
        $clause = Clause::whereIn('id',$clauses_id)
                ->with('category','company','duties')
                ->get();
        $categorys  = Category::where('status','on')
            ->select(DB::raw('*, concat(path,id) as npath'))
            ->orderBy('npath', 'asc')
            ->get();
        $data = ['status'=>'true','data'=>['res'=>$result,'clause'=>$clause,'category'=>$categorys,'companys'=>$companys]];
        $data = json_encode($data);
        return $data;
    }

    public function testSign()
    {
        $data = $this->request->all();
        print_r(json_encode($this->sign_help->tySign($data)));
    }
}