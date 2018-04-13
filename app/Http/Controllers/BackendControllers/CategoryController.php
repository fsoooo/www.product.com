<?php

namespace App\Http\Controllers\BackendControllers;

use Validator, DB;
use App\Models\Category;

class CategoryController extends BaseController
{

    /**
     * 分类管理首页
     * @return mixed
     */
    public function index()
    {
        $categories = Category::where('status','on')->select(DB::raw('*, concat(path,id) as npath'))->orderBy('npath', 'asc')->get();
        return view('backend.category.index',compact('categories'));
    }

    /**
     * 添加分类处理
     * @return mixed
     */
    public function add()
    {
        $input = $this->request->all();
            if(preg_match("/[\x7f-\xff]/",$input['slug'])){
            return redirect('backend/product/category')->withErrors('唯一标识不能为中文!');
        }
        $validator = $this->checkAddCategory($input);
        if ($validator->fails()) {
            return redirect('backend/product/category')
                ->withErrors($validator)
                ->withInput();
        }


        $category = new Category();
        $category->name = $input['name'];
        $category->slug = $input['slug'];
        $category->path = ',' . 0 . ',';
        if(!empty($input['pid'])){
            $parent = Category::where('status','on')->find($input['pid']);
            $category->pid = $parent->id;
            $category->sort = $parent->sort + 1;
            $category->path = $parent->path . $parent->id . ',';
        }

        $category->save();
        return redirect('backend/product/category')->with('status', '成功录入分类信息!');
    }

    /**
     * 分类修改
     * @return mixed
     */
    public function alter()
    {
        $input  = $this->request->all();
        $name   = $input['name'];
        $pid    = $input['pid'];
        $slug   = $input['slug'];
        $result = category::where(['id'=>$pid])->update(['name' => $name,'slug'=>$slug]);
        if($result){
            return redirect('backend/product/category')->with('status', '成功修改分类信息!');
        }else{
            return back()->with('status', '修改失败，请重新修改!');
        }
    }

    /**
     * 分类的删除
     */
    public function omit()
    {
        $id = intval($_GET['id']);
        $ids = [$id];
        $del = Category::where([['path','like','%,'.$id.',%'], ['status', 'on']])->select('id')->get();
        if ($del) {
            foreach ($del as $key => $value) {
                $ids[] = $value->id;
            }
            $ids = array_unique($ids);
        }
        $res = Category::whereIn('id', $ids)->update(['status'=>'off']);
        if($res){
            return redirect('backend/product/category')->with('status', '成功删除分类信息!');
        }else{
            return back()->with('status', '删除失败，请稍等!');
        }
    }

    /**
     * 分类添加验证
     * @param $input
     * @return mixed
     */
    protected function checkAddCategory($input)
    {
        //规则
        $rules = [
            'pid' => 'integer|nullable',
            'name' => 'required|string',
            'slug' => 'required|string|unique:category',
        ];

        //自定义错误信息
        $messages = [
            'required' => 'The :attribute is null.',
            'unique' => 'The :attribute is exist',
            'integer' => 'The :attribute mast be integer.',
            'string' => 'The :attribute mast be string.',
        ];
        //验证
        $validator = Validator::make($input, $rules, $messages);
        return $validator;
    }

}