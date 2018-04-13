<?php

namespace App\Http\Controllers\BackendControllers;

use App\Models\Company;
use App\Models\Clause;
use App\Models\ApiFrom;
use App\Models\Category;
use App\Models\Insurance;
use App\Models\InsuranceAttributeModule;
use App\Models\RestrictGene;
use App\Repositories\ApiFromRepository;
use App\Repositories\InsuranceApiFromRepository;
use App\Repositories\InsuranceAttributesRepository;
use \Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Helper\RsaSignHelp;
use App\Helper\UploadFileHelper;
use App\Models\InsuranceApiInfo;
use App\Models\InsuranceHealth;

class InsuranceController extends BaseController
{
    /**
     * 保险产品管理
     * @return mixed
     */
    public function index()
    {
        $insurance = Insurance::where('status', 'on')
            ->with(['category','company','clauses','clauses.duties'])
            ->paginate(config('list_num.backend.insurance'));
        return view('backend.insurance.index', compact('insurance'));
    }

    /**
     * 保险产品添加
     * @return mixed
     */
    public function add()
    {
        $companies = Company::with([
            'clauses'=>function($q){
                $q->where('status', 'on')->whereHas('duties');
            },
            'clauses.duties'=>function($q){
                $q->where(['need_coverage'=> 1, 'status'=>'on'])->wherePivot('ins_id', 0);
            }
        ])
            ->where('status', 'on')
            ->get();
        $categories = Category::where(
                ['status'=>'on', ['path', 'like', ',0,2,%']
            ])
            ->select(DB::raw('*, concat(path,id) as npath'))
            ->orderBy('npath', 'asc')
            ->get();
        return view('backend.insurance.add', compact('categories', 'companies', 'clauses'));
    }

    /**
     * 保险产品提交
     * @return mixed
     */
    public function addPost()
    {
        $input = $this->request->all();
        //验证
        $validator = $this->checkAdd($input);
        if ($validator->fails()) {
            return redirect('backend/product/insurance/add')
                ->withErrors($validator)
                ->withInput();
        }
        $company_id = $input['company_id'];
        //获取条款ids
        if(empty($input['clause_main_ids'][$company_id]))
            return back()->withErrors('请选择关联条款');

        $clause_ids = $input['clause_main_ids'][$company_id];
        if(!empty($input['clause_attach_ids'][$company_id])){
            $clause_ids = array_merge($input['clause_main_ids'][$company_id], $input['clause_attach_ids'][$company_id]);
        }
        //条款中需要设置责任保额的ID
        $clauses_duty_need_coverage = Clause::whereIn('id', $clause_ids)
            ->whereHas('duties',function($q){
                $q->where('need_coverage', 1);
            })->pluck('id')->toArray();

        $coverage_bs = count($clauses_duty_need_coverage) ? array() : $clause_ids; //条款保额倍数
        $coverage_jc = array(); //责任基础保额
        $duty_ids = array();    //所选条款中所有责任的id
        foreach($clause_ids as $ck =>$c_id){
            if(isset($input['coverage_bs'])){
                if(!isset($input['coverage_bs'][$c_id]) && in_array($c_id, $clauses_duty_need_coverage))
                    return redirect('backend/product/insurance/add')->withErrors(['请输入所选条款对应的保额倍数']);
                $coverage_bs[$c_id] = ['coverage_bs'=> $input['coverage_bs'][$c_id] ];
                foreach($input['duty_coverage'][$c_id] as $duty_id => $dv){
                    if(in_array($duty_id, $duty_ids))
                        return redirect('backend/product/insurance/add')->withErrors(['所选条款中包含重复责任']);
                    if(!$dv)
                        return redirect('backend/product/insurance/add')->withErrors(['请输入所选条款下责任对应的基础保额']);
                    $coverage_jc[$c_id][$duty_id] = ['coverage_jc' =>$dv];
                    $duty_ids[] =$duty_id;
                }
            }
        }
        DB::beginTransaction();
        try{
            $insurance = new Insurance();
            $insurance->name = $input['name'];
            $insurance->display_name = $input['display_name'];
            $insurance->min_math = $input['min_math'];
            $insurance->max_math = $input['max_math'];
            $insurance->category_id = $input['category_id'];
            $insurance->company_id = $input['company_id'];
            $insurance->type = $input['insurance_type'];
            $insurance->content = $input['content'];
            $insurance->base_price = $input['base_price'] * 100;  //基础保费
            $insurance->base_stages_way = $input['base_stages_way'];  //缴别
            $insurance->base_ratio = $input['base_ratio'];  //基础佣金比例
            $insurance->first_date = $input['first_date'];  //观察期
            $insurance->latest_date = $input['latest_date'];  //观察期
            $insurance->observation_period = $input['observation_period'];  //观察期
            $insurance->period_hesitation = $input['period_hesitation'];  //犹豫期
//            $insurance->health_status = $input['health'];  //有无健康告知
//            $insurance->health_content = $input['health_notice'];  //健康告知内容


            $image_paths = [];
            if(!empty($input['insure_files'])&& (count($input['insure_files'])!=0)){
                foreach ($input['insure_files'] as $key=>$value){
                    $file_name = $_FILES['insure_files']['name'][$key];
                    $path = 'upload/insure_resourse/'.$insurance->id.'/'.$file_name.'/';
                    $image_path = UploadFileHelper::uploadFile($value, $path);//理赔上传图片路径（存数据库）
                    $image_paths[$file_name] = $image_path;
                }
                $image_path_json = json_encode($image_paths)??"";
                $insurance->insure_resourse = $image_path_json;
            }

            $insurance->save();
            //绑定条款时 录入对应保额倍数
            $insurance->clauses()->sync($coverage_bs);
            //绑定条款对应责任的基础保额
            foreach($insurance->clauses as $k => $clause){
                //插入产品ID
                foreach($coverage_jc[$clause->id] as $ck => $cv){
                    $coverage_jc[$clause->id][$ck]['ins_id'] = $insurance->id;
                }
                $clause->duties()->attach($coverage_jc[$clause->id]);
            }

            DB::commit();
            return redirect('backend/product/insurance')->with('status', '成功录入保险产品!');
        }catch (\Exception $e){
            DB::rollBack();
            $errors = $e->getMessage();
            return redirect('backend/product/insurance')->withErrors($errors);
        }
    }

    /**
     * 保险产品编辑
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $data = Insurance::where(['status'=>'on','id'=>$id])
            ->with(['category','company','clauses'=>function($q){
                $q->withPivot('coverage_bs');
            },
                'clauses.duties'=>function($q) use($id) {
                    $q->where('need_coverage', 1)->wherePivot('ins_id', $id)->withPivot('coverage_jc');
                }])
            ->first();
        $categories = Category::where(['status'=>'on',['path', 'like', ',0,2,%']])
            ->select(DB::raw('*, concat(path,id) as npath'))
            ->orderBy('npath', 'asc')
            ->get();
        $companies = Company::where('status', 'on')->with(['clauses'=>function($q){
            $q->where('status', 'on');
        }, 'clauses.duties'=>function($q){
            $q->where(['need_coverage'=> 1, 'status'=> 'on'])->wherePivot('ins_id', 0);
        }])->get();

        $clause_ids = array();
        $clause_coverage_bs = array();
        $coverage_jc = array();
        foreach($data->clauses as $k => $v){
            $clause_ids[] = $v->id;
            $clause_coverage_bs[$v->id] = $v->pivot->coverage_bs;
            foreach($v->duties as $dk => $dv){
                $coverage_jc[$v->id][$dv->id] = $dv->pivot->coverage_jc;
            }
        }
        return view('backend.insurance.edit',compact('data','categories','companies','clauses', 'clause_ids', 'clause_coverage_bs','coverage_jc'));
    }

    /**
     * 保险产品编辑提交
     * @return mixed
     */
    public function editSubmit()
    {
        $input = $this->request->all();
        $validator = $this->checkAdd($input);
        if ($validator->fails()) {
            return redirect(url()->previous())
                ->withErrors($validator)
                ->withInput();
        }
        $company_id = $input['company_id'];
        //获取条款ids
        if(empty($input['clause_main_ids'][$company_id]))
            return back()->withErrors('请选择关联条款');

        $clause_ids = $input['clause_main_ids'][$company_id];
        if(!empty($input['clause_attach_ids'][$company_id])){
            $clause_ids = array_merge($input['clause_main_ids'][$company_id], $input['clause_attach_ids'][$company_id]);
        }
        //条款中需要设置责任保额的ID
        $clauses_duty_need_coverage = Clause::whereIn('id', $clause_ids)
            ->whereHas('duties',function($q){
                $q->where('need_coverage', 1);
            })->pluck('id')->toArray();

        $coverage_bs = count($clauses_duty_need_coverage) ? array() : $clause_ids; //条款保额倍数
        $coverage_jc = array(); //责任基础保额
        $duty_ids = array();    //所选条款中所有责任的id

        foreach($clause_ids as $ck =>$c_id){
            if(isset($input['coverage_bs'])){
                if(!isset($input['coverage_bs'][$c_id]) && in_array($c_id, $clauses_duty_need_coverage))
                    return redirect(url()->previous())->withErrors(['请输入所选条款对应的保额倍数']);
                $coverage_bs[$c_id] = ['coverage_bs'=> $input['coverage_bs'][$c_id] ];
                foreach($input['duty_coverage'][$c_id] as $duty_id => $dv){
                    if(in_array($duty_id, $duty_ids))
                        return redirect(url()->previous())->withErrors(['所选条款中包含重复责任']);
                    if(!$dv)
                        return redirect(url()->previous())->withErrors(['请输入所选条款下责任对应的基础保额']);
                    $coverage_jc[$c_id][$duty_id] = ['coverage_jc' =>$dv];
                    $duty_ids[] =$duty_id;
                }
            }
        }
        try{
            $insurance = Insurance::where('id', $input['insurance_id'])->first();
            $insurance->name = $input['name'];
            $insurance->display_name = $input['display_name'];
            $insurance->min_math = $input['min_math'];
            $insurance->max_math = $input['max_math'];
            $insurance->category_id = $input['category_id'];
            $insurance->company_id = $input['company_id'];
            $insurance->type = $input['insurance_type'];
            $insurance->content = $input['content'];

            $insurance->base_price = $input['base_price'] * 100;  //基础保费
            $insurance->base_stages_way = $input['base_stages_way'];  //缴别
            $insurance->base_ratio = $input['base_ratio'];  //基础佣金比例
            $insurance->first_date = $input['first_date'];  //观察期
            $insurance->latest_date = $input['latest_date'];  //观察期
            $insurance->observation_period = $input['observation_period'];  //观察期
            $insurance->period_hesitation = $input['period_hesitation'];  //犹豫期
            $insurance->save();
            //绑定条款时 录入对应保额倍数
            $insurance->clauses()->sync($coverage_bs);
            //绑定条款对应责任的基础保额
            foreach($insurance->clauses as $k => $clause){
                //插入产品ID
                foreach($coverage_jc[$clause->id] as $ck => $cv){
                    $coverage_jc[$clause->id][$ck]['ins_id'] = $insurance->id;
                }
                DB::table('clause_duty')->where(['clause_id'=>$clause->id, 'ins_id'=>$insurance->id])->delete();
                $clause->duties()->attach($coverage_jc[$clause->id]);
            }
            DB::commit();
            return redirect(url()->previous())->with('status', '成功录入保险产品!');
        }catch (\Exception $e){
            DB::rollBack();
            $errors = $e->getMessage();
            return redirect(url()->previous())->withErrors($errors);
        }
    }


    /**
     * 保险产品删除
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        DB::table('insurance')->where('id',$id)->update(['status'=>'off']);
        DB::table('insurance_clause')->where('insurance_id',$id)->delete(); //删除条款关联
        DB::table('clause_duty')->where('ins_id',$id)->delete();    //删除条款、责任对应的保额关联
        return redirect('backend/product/insurance')->with('status', '删除成功');
    }

    /**
     * 保险产品添加验证
     * @param $input
     * @return mixed
     */
    protected function checkAdd($input)
    {
        //规则
        $rules = [
            'name' => 'required|string',
            'display_name' => 'required|string',
            'category_id' => 'required|integer',
            'insurance_type'=>'required|integer',
            'company_id' => 'required|integer',
            'content' => 'required|string',
        ];

        //自定义错误信息
        $messages = [
            'required' => 'The :attribute is null.',
            'integer' => 'The :attribute mast be integer.',
            'string' => 'The :attribute mast be string.',
        ];
        //验证
        $validator = Validator::make($input, $rules, $messages);
        return $validator;
    }

    /**
     * 页面：绑定产品与API来源
     * @param null $insurance_id
     * @param InsuranceApiFromRepository $repository
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bind($insurance_id = null, InsuranceApiFromRepository $repository)
    {
        if (!$insurance_id) {
            return $this->bindPageWithoutInsuranceId();
        }

        return $this->bindPageWithInsuranceId($insurance_id);
    }

    protected function bindPageWithoutInsuranceId()
    {
        $bind_api_from = null;
        $bind_insurance = null;
        $insurances = Insurance::all();
        $api_froms = ApiFrom::all();

        return view('backend.insurance.bind', get_defined_vars());
    }

    protected function bindPageWithInsuranceId($insurance_id)
    {
        $bind_insurance = Insurance::where('id', $insurance_id)->first();
        if (!$bind_insurance) {
            return redirect()->route('insurance.bind.index')->withErrors('不存在的产品');
        }
        $bind_api_from = $bind_insurance->apiFroms()->wherePivot('status', 1)->first();
        $insurances = Insurance::all();
        $api_froms = ApiFrom::all();

        return view('backend.insurance.bind', get_defined_vars());
    }

    /**
     * 绑定产品与API来源
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function bindStore(Request $request)
    {
        $insurance_id = $request->input('insurance_id');
        $api_from_id = $request->input('api_from_id');

        $insurance = Insurance::where('id', $insurance_id)->first();
        DB::beginTransaction();
        try {
            if ($api_from_id == 0) { // 若全部不选中，则状态全部改为0
                $bind_api_from_ids = $insurance->apiFroms()->pluck('api_from.id')->toArray();
                $insurance->apiFroms()->wherePivotIn('api_from_id', $bind_api_from_ids)->update(['insurance_api_from.status' => 0]);
            } else {
                // 若不存在绑定关系，则插入，并且状态为1
                if (!$insurance->apiFroms()->wherePivot('api_from_id', $api_from_id)->first()) {
                    $api_from = ApiFrom::where('id', $api_from_id)->first();
                    $insurance->apiFroms()->save($api_from, ['status' => 1]);
                }
                // 当前记录的状态改为1
                $insurance->apiFroms()->wherePivot('api_from_id', $api_from_id)->update(['insurance_api_from.status' => 1]);
                // 除了当前记录的状态为0
                $insurance->apiFroms()->wherePivot('api_from_id', '!=', $api_from_id)->update(['insurance_api_from.status' => 0]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return back()->withErrors('绑定失败');
        }
        return back()->with('status', '绑定成功');
    }

    /**
     * 产品与API来源绑定列表
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function insList()
    {
        $select = [
            'c.name as api_from_name',
            'c.uuid as uuid',
            'c.id as api_from_id',
            'b.id as bind_id',
            'b.p_code',
            'b.status',
            'a.id as insurance_id',
            'a.name as insurance_name',
            'a.sell_status',
            'a.type as insurance_type',
            'b.private_p_code',
        ];
        $lists = DB::table('insurance as a') // a.产品表
            ->where('a.status', 'on')
            ->join('insurance_api_from as b', 'a.id', '=', 'b.insurance_id') // b.中间表
            ->join('api_from as c', 'c.id', '=', 'b.api_from_id') // c.API来源表
            ->orderBy('insurance_id', 'asc')
            ->orderBy('api_from_id', 'asc')
            ->where('b.status', 1)
            ->select($select)
            ->paginate(30);
        return view('backend.insurance.list', compact('lists'));
    }

    /**
     * 页面：上传模板
     * @param $bind_id
     * @param InsuranceApiFromRepository $repository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function template($bind_id, InsuranceApiFromRepository $repository)
    {
        $relation = $repository->getApiStatusOn($bind_id);
        return view('backend.insurance.template', compact('relation'));
    }

    /**
     * 上传团险模板
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadTemplate(){
        $input = $this->request->all();

        $id = $input['id'];
        $template_url = !empty($this->request->file('template_url')) ? $this->uploadExl($this->request->file('template_url')) : '';
        //print_r($template_url);die;

        if (empty($template_url)) return redirect()->route('insurance.bind.list')->withErrors('未上传文件！');
        if ($template_url == 'type_error') return redirect()->route('insurance.bind.list')->withErrors('文件类型错误！');

        $update = [
            'template_url'=> $template_url
        ];

        DB::table('insurance_api_from')
            ->where('id', $id)
            ->update($update);

        return redirect()->route('insurance.bind.list')->with('status', '上传成功');
    }

    protected function uploadExl($file)
    {
        $types = array('xls', 'xlsx', 'xlsm', 'xlt', 'xltx','xltm');

        $extension = $file->getClientOriginalExtension();

        if(!in_array($extension, $types)){
            return 'type_error';
        }

        $path = 'upload/backend/template/';
        $name = date("YmdHis") . rand(1000, 9999) . '.' . $extension;
        $file -> move($path, $name);
        return $path . $name;
    }

    /**
     * 页面：更新产品码
     *
     * @param $bind_id
     * @param InsuranceApiFromRepository $repository
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bindPCode($bind_id, InsuranceApiFromRepository $repository)
    {
        if (!$relation = $repository->getBindRelationByBindId($bind_id)) {
            return redirect()->route('insurance.bind.list')->withErrors('不存在的绑定关系');
        }

        return view('backend.insurance.pcode', compact('relation'));
    }


    /**
     * 更新产品码
     *
     * @param Request $request
     * @param InsuranceApiFromRepository $repository
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function bindPCodeStore(
        Request $request,
        InsuranceApiFromRepository $repository)
    {
        $bind_id = $request->input('bind_id');
        $p_code = $request->input('p_code');
        if (!$p_code) {
            return back()->withErrors('请输入产品码');
        }
        $api_from_id = $repository->getApiFromIdByBindId($bind_id);
        $api_from = ApiFrom::where('id', $api_from_id)->first();
        $help = new RsaSignHelp();
        $update = [
            'p_code' => $p_code,
            'private_p_code' => md5($api_from->uuid . '-' . $p_code)
        ];
        DB::table('insurance_api_from')
            ->where('id', $bind_id)
            ->update($update);

        return redirect()->route('insurance.bind.list')->with('status', '更新成功');
    }

    /**
     * 保险产品售卖状态变更
     * @param Request $request
     * @return mixed
     */
    public function doSetSellStatus(Request $request){
        $input = $request->all();
//        dd($input);
        Insurance::where('id',$input['id'])->update([
            'sell_status'=>$input['sell_status'],
        ]);
        return redirect('/backend/product/insurance/bind/list')->with('status', '设置成功');
    }

    /**
     * 保险产品健康告知
     * @param $id
     * @return mixed
     */
    public function health($id){
        $health_res = InsuranceHealth::where('insurance_id',$id)->get();
        $ins_info = Insurance::where('id',$id)->with('insurance_api_info')->first();
        return view('backend.insurance.health', compact('health_res','ins_info','id'));
    }

    /**
     * 保险产品添加健康告知
     * @param Request $request
     * @return mixed
     */
    public function healthSubmit(Request $request){
        $input = $request->all();
        if(!empty($input)&&count($input)!=0&&isset($input['health_id'])){
            $id = $input['health_id'];
            unset($input['_token']);
            unset($input['health_id']);
            InsuranceHealth::where('id',$id)->update($input);
        }else{
            $health = new InsuranceHealth();
            $health->content  = $input['content'];
            $health->insurance_id  = $input['insurance_id'];
            $health->order  = $input['order'];
            $health->checked  = $input['checked'];
            $health->condition  = $input['condition'];
            $health->condition_value  = $input['condition_value'];
            $health->save();
        }
        return redirect('/backend/product/insurance/health/'.$input['insurance_id'])->with('操作成功！');
    }


    public function otherSupport($id)
    {
        $bind = InsuranceApiInfo::find($id);
        $json = json_decode($bind->other_support, true);
        return view('backend.insurance.other_support',compact('bind', 'json'));
    }

    public function otherSupportPost()
    {
        $input = $this->request->all();

        $bind = InsuranceApiInfo::find($input['bind_id']);
        unset($input['_token']);
        unset($input['bind_id']);
        $json = json_encode($input);
        $bind->other_support = $json;
        $bind->save();
        return redirect('/backend/product/insurance/other_support/'.$bind->id)->with('status','操作成功！');
    }
}