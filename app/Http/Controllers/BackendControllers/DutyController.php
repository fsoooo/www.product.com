<?php

namespace App\Http\Controllers\BackendControllers;

use Validator, DB;
use App\Helper\Helper;
use App\Models\Duty;
use App\Models\Category;

class DutyController extends BaseController
{
    /**
     * 责任管理
     * @return mixed
     */
    public function index()
    {
        $name = $_GET['name'] ?? "";
        $categories = Category::where(['status'=>'on',['path', 'like', ',0,4,%']])
            ->select(DB::raw('*, concat(path,id) as npath'))
            ->orderBy('npath', 'asc')
            ->get();
        $duty = Duty::where('status','on')->where('duty.name', 'like', '%'.$name.'%')->with('category')->paginate(config('list_num.backend.insurance'));
        return view('backend.duty.index',compact('duty', 'categories','name'));
    }

    /**
     * 责任添加
     * @return mixed
     */
    public function add()
    {
        $input = $this->request->all();
        //验证
        $validator = $this->checkAddDuty($input);
        if ($validator->fails()) {
            return redirect('backend/product/duty')
                ->withErrors($validator)
                ->withInput();
        }
        $duty = new Duty();
        $duty->name = $input['name'];
        $duty->description = $input['description'];
        $duty->detail = $input['detail'];
        $duty->type = $input['type'];
        $duty->category_id = $input['category_id'];
        $duty->need_coverage = $input['need_coverage'];
        $duty->save();

        return redirect('backend/product/duty')->with('status', '成功录入责任信息!');
    }

    /**
     * 责任更新
     * @param $id
     * @return mixed
     */
    public function updateDuty($id)
    {
        $categories = Category::where(['status'=>'on',['path', 'like', ',0,4,%']])
            ->select(DB::raw('*, concat(path,id) as npath'))
            ->orderBy('npath', 'asc')
            ->get();
        $data=Duty::with('category')->where('id',$id)->get();
//        dd($categories);
        return view('backend.duty.update',compact('categories','data','id'));
    }

    /**
     * 责任更新验证
     * @return mixed
     */
    public function updataDutySubmit()
    {
        $input=$this->request->all();
        $validator = $this->checkAddDuty($input);
        if($validator->fails()){
            return redirect('backend/product/updateDuty')
                ->withErrors($validator)
                ->withInput();
        }
        $duty['name'] = $input['name'];
        $duty['description'] = $input['description'];
        $duty['detail'] = $input['detail'];
        $duty['type'] = $input['type'];
        $duty['category_id'] = $input['category_id'];
        $duty['need_coverage'] = $input['need_coverage'];
        DB::table('duty')
            ->where('id',$input['id'])
            ->update($duty);
        return redirect('/backend/product/duty')->with('status','更新成功');
    }

    /**
     * 责任删除
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        Duty::where('id',$id)->update(['status'=> 'off']);
        DB::table('clause_duty')->where('duty_id', $id)->delete();    //删除责任绑定的条款
        return redirect('/backend/product/duty')->with('status','删除成功');
    }

    /**
     * 添加责任验证
     * @param $input
     * @return mixed
     */
    protected function checkAddDuty($input)
    {
        //规则
        $rules = [
            'name' => 'required|string',
            'description' => 'required|string',
            'detail' => 'required|string',
            'category_id' => 'required|integer',
            'type' => 'required|string',
            'need_coverage' => 'required|integer'
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

}