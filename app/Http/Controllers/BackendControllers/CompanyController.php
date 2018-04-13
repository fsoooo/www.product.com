<?php

namespace App\Http\Controllers\BackendControllers;

use Validator, DB, Image;
use App\Models\Company;
use App\Models\Category;

class CompanyController extends BaseController
{

    /**
     * 公司管理
     * @return mixed
     */
    public function index()
    {
        $categories = Category::where(['status'=>'on',['path', 'like', ',0,1,%']])
            ->select(DB::raw('*, concat(path,id) as npath'))
            ->orderBy('npath', 'asc')
            ->get();
        $companies = Company::where('status','0')->with('category')->paginate(config('list_num.backend.company'));
        return view('backend.company.index',compact('categories', 'companies'));
    }

    /**
     * 公司添加
     * @return mixed
     * @throws \Exception
     */
    public function add()
    {
        $input = $this->request->all();
        //验证
        $validator = $this->checkAddCompany($input);
        if ($validator->fails()) {
            return redirect('backend/product/company')
                ->withErrors($validator)
                ->withInput();
        }
        //上传LOGO
        $logo_url = $this->uploadLogo($this->request->file('logo'));
        $code_img = '';
        if($this->request->file('code_img'))
            $code_img = $this->uploadLogo($this->request->file('code_img'));
        //添加公司
        $company = new Company();
        $company->name = $input['name'];
        $res = Company::where('name',$company->name)->first();
        if(!is_null($res)){
            return redirect('backend/product/company')->withErrors( '该公司已经录入！！');
        }
        $company->display_name = $input['display_name'];
        $company->category_id = $input['category_id'];
        $company->code = $input['code'];
        $company->bank_type = isset($input['bank_type']) ? $input['bank_type'] : '';
        $company->bank_num = isset($input['bank_num']) ? $input['bank_num'] : '';
        $company->url = $input['url'];
        $company->email = isset($input['email']) ? $input['email'] : '';
        $company->phone = $input['phone'];
        $company->code_img = $code_img;
        $company->logo = $logo_url;
        $company->save();
        return redirect('backend/product/company')->with('status', '成功录入公司信息!');
    }

    /**
     * 添加公司验证
     * @param $input
     * @return mixed
     */
    protected function checkAddCompany($input)
    {
        //规则
        $rules = [
            'name' => 'required|string',
            'display_name' => 'required|string',
            'category_id' => 'required|integer',
            'code' => 'required|string',
            'url' => 'required|string',
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
     * 公司LOGO 上传
     * @param $file
     * @return string
     * @throws \Exception
     */
    protected function uploadLogo($file)
    {
        $types = array('jpg', 'jpeg', 'png');

        $extension = $file->getClientOriginalExtension();
        if(!in_array($extension, $types)){
            throw  new \Exception('文件类型错误');
        }
        
        $path = 'upload/backend/company/logo/';
		$name = date("YmdHis") . rand(1000, 9999) . '.' . $extension;
        $file -> move($path, $name);
        return $path . $name;
    }


    /**
     * 更新公司信息
     * @param $id
     * @return mixed
     */
    public function updateCompanyIndex($id){
        $categories = Category::where(['status'=>'on',['path', 'like', ',0,2,%']])
            ->select(DB::raw('*, concat(path,id) as npath'))
            ->orderBy('npath', 'asc')
            ->get();
        $companies = Company::with('category')->where(['company.status'=>0,'company.id'=>$id])->get();
//        dd($companies);
        return view('backend.company.update',compact('companies','categories'));
    }

    /**
     * 更新公司提交
     * @return mixed
     * @throws \Exception
     */
    public  function updateCompany(){
        $input = $this->request->all();
        //验证
        $validator = $this->checkAddCompany($input);
        if ($validator->fails()) {
            return redirect('backend/product/company')
                ->withErrors($validator)
                ->withInput();
        }
        //上传LOGO
        $logo_url = !empty($this->request->file('logo')) ? $this->uploadLogo($this->request->file('logo')) : '';
        $code_img = !empty($this->request->file('code_img')) ? $this->uploadLogo($this->request->file('code_img')) : '';
        //添加公司
        $duty = Company::find($input['company_id']);
        $duty->name = $input['name'];
        $duty->display_name = $input['display_name'];
        $duty->category_id = $input['category_id'];
        $duty->code = $input['code'];
        $duty->url = $input['url'];
        $duty->email = $input['email'];
        if(!empty($logo_url))
            $duty->logo = $logo_url;
        if(!empty($code_img))
            $duty->code_img = $code_img;
        $duty->update();
        return redirect('backend/product/company')->with('status', '成功更改公司信息!');
    }

    /**
     * 删除公司
     * @param $id
     * @return array
     */
    public function deleteCompany($id){
        $duty = Company::find($id);
        $duty->status = '1';
        $duty->update();
        return (['status'=>'0','message'=>'删除成功！！']);
    }


}