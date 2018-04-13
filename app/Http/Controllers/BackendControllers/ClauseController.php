<?php

namespace App\Http\Controllers\BackendControllers;

use Validator, DB, Image;
use App\Models\Clause;
use App\Models\Category;
use App\Models\Company;
use App\Models\Duty;

class ClauseController extends BaseController
{
    /**
     * 条款管理
     * @return mixed
     */
    public function index()
    {
        $input = $this->request->all();
        $where = array();
        $where['status'] = 'on';
        $name = isset($input['name']) ? $input['name'] : '';
        if($name){
            $where[] = ['name','like', '%'.$input['name'] .'%'];
        }

        $clauses = Clause::where($where)
            ->with(['category', 'duties'=>function($q){
                $q->where('ins_id', 0);
            }])
            ->paginate(config('list_num.backend.clause'));
//        dd($clauses);
        return view('backend.clause.index',compact('clauses', 'name'));
    }

    /**
     * 条款添加
     * @return mixed
     */
    public function add()
    {
        $categories = Category::where(['status'=>'on',['path', 'like', ',0,3,%']])
            ->select(DB::raw('*, concat(path,id) as npath'))
            ->orderBy('npath', 'asc')
            ->get();

        $duties = Duty::where('status', 'on')->get();
        $companies = Company::where('status', 'on')->get();
        return view('backend.clause.add', compact('categories', 'companies', 'duties'));
    }

    /**
     * 条款添加提交
     * @return mixed
     * @throws \Exception
     */
    public function addPost()
    {
        $input = $this->request->all();

        //验证
        $validator = $this->checkAdd($input);
        if ($validator->fails()) {
            return redirect('backend/product/clause/add')
                ->withErrors($validator)
                ->withInput();
        }

        //获取同类型责任ids
        if(empty($input['duty_main_ids']) && empty($input['duty_attach_ids']))
            return redirect('backend/product/clause/add')
                ->withErrors('请选择关联责任');

        if($input['type'] == 'attach'){
            $duty_ids = $input['duty_attach_ids'];
        } else {
            $duty_ids = $input['duty_main_ids'];
        }

        if(empty($this->request->file('file')))
            return redirect('backend/product/clause/add')
                ->withErrors('请上传条款附件');
        $file_url = $this->uploadFile($this->request->file('file'));

        //添加条款
        DB::beginTransaction();
        try{
            $clause = new Clause();
            $clause->name = $input['name'];
            $clause->display_name = $input['display_name'];
            $clause->category_id = $input['category_id'];
            $clause->company_id = $input['company_id'];
            $clause->type = $input['type'];
            $clause->content = $input['content'];
            $clause->file_url = $file_url;
            $clause->clause_code = $input['clause_code'];
            $clause->save();
            $clause->duties()->attach($duty_ids);
            DB::commit();
            return redirect('backend/product/clause/add')->with('status', '成功录入条款信息!');
        }catch (\Exception $e){
            DB::rollBack();
            $errors = $e->getMessage();
            return redirect('backend/product/clause/add')->withErrors($errors);
        }

    }

    /**
     * 条款更新
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $companies = Company::where('status', 'on')->get();
        $duties = Duty::where('status', 'on')->get();
        $categories = Category::where(['status'=>'on',['path', 'like', ',0,3,%']])
            ->select(DB::raw('*, concat(path,id) as npath'))
            ->orderBy('npath', 'asc')
            ->get();
        $clause = Clause::where('id', $id)
            ->with('duties')
            ->first();
        $c_d_ids = array();
        foreach($clause->duties as $k => $v){
            $c_d_ids[] = $v->id;
        }
        return view('backend.clause.update', compact('companies', 'categories', 'clause', 'duties', 'c_d_ids'));
    }

    /**
     * 条款更新提交
     * @return mixed
     * @throws \Exception
     */
    public function updatePost()
    {
        $input = $this->request->all();
        //验证
        $validator = $this->checkAdd($input);
        if ($validator->fails()) {
            return redirect('backend/product/clause/add')
                ->withErrors($validator)
                ->withInput();
        }

        //获取同类型责任ids
        if(empty($input['duty_main_ids']) && empty($input['duty_attach_ids']))
            return redirect('backend/product/clause/add')
                ->withErrors('请选择关联责任');

        if($input['type'] == 'attach'){
            $duty_ids = $input['duty_attach_ids'];
        } else {
            $duty_ids = $input['duty_main_ids'];
        }
        $file_url = '';
        if(!empty($this->request->file('file')))
            $file_url = $this->uploadFile($this->request->file('file'));

        DB::beginTransaction();
        try{
            $clause = Clause::where('id', $input['clause_id'])->first();
            $clause->name = $input['name'];
            $clause->display_name = $input['display_name'];
            $clause->category_id = $input['category_id'];
            $clause->clause_code = $input['clause_code'];
            $clause->company_id = $input['company_id'];
            $clause->type = $input['type'];
            $clause->content = $input['content'];
            if($file_url)
                $clause->file_url = $file_url;
            $clause->save(); //条款内容更新
            $old_duty_ids = $clause->duties()->pluck('duty.id')->toArray();
            //是否有更新关联责任
            if($duty_ids != $old_duty_ids){
                $clause->insurances()->update(['status'=> 'off']);  //关闭条款关联的产品
                $clause->duties()->sync($duty_ids); //更新关联责任
            }
            DB::commit();
            return redirect(url()->previous())->with('status', '成功录入条款信息!');
        }catch (\Exception $e){
            DB::rollBack();
            $errors = $e->getMessage();
            return redirect(url()->previous())->withErrors($errors);
        }
    }

    /**
     * 条款删除
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        Clause::where('id',$id)->update(['status'=>'off']);
        DB::table('clause_duty')->where('clause_id',$id)->delete();    //删除条款对应责任
        return redirect('backend/product/clause')->with('status', '删除成功');
    }

    /**
     * 条款添加验证
     * @param $input
     * @return mixed
     */
    protected function checkAdd($input)
    {
        //规则
        $rules = [
            'name' => 'required|string',
            'display_name' => 'required|string',
            'type' => 'required|string',
            'category_id' => 'required|integer',
            'company_id' => 'required|integer',
            'content' => 'required|string'
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
     *
     * @param $file
     * @return string
     * @throws \Exception
     */
    protected function uploadFile($file)
    {
        $types = array('jpg', 'jpeg', 'png', 'pdf');

        $extension = $file->getClientOriginalExtension();
        if(!in_array($extension, $types)){
            throw  new \Exception('文件类型错误');
        }

        $path = 'upload/backend/clause/file/';
        $name = date("YmdHis") . rand(1000, 9999) . '.' . $extension;
        $file -> move($path, $name);
        return $path . $name;
    }



}