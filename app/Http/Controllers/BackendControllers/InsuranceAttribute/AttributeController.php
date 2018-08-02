<?php

namespace App\Http\Controllers\BackendControllers\InsuranceAttribute;

use App\Http\Controllers\Controller;
use App\Models\InsuranceAttribute;
use App\Models\InsuranceAttributeModule;
use App\Repositories\InsuranceApiFromRepository;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    public function index($mid, InsuranceApiFromRepository $repository)
    {
        $module = InsuranceAttributeModule::where('id', $mid)->first();
        $attributes = InsuranceAttribute::where('mid', $mid)->get();
        $relation = $repository->getBindRelationByBindId($module->bind_id);

        return view('backend.insurance_attribute.attribute.index', compact('attributes', 'module', 'relation'));
    }

    public function create($mid)
    {
        $ins_key_value = config('ins_key_value');
        $module = InsuranceAttributeModule::where('id', $mid)->first();
        if(empty($module)){
            return view('backend.insurance_attribute.attribute.create', compact('mid'));
        }else{
            $module_key = $module['module_key'];
            if(key_exists($module_key,$ins_key_value)){
                $module_res = $ins_key_value[$module_key];
            }elseif(key_exists($module_key,$ins_key_value['ins_base'])){
                $module_res = $ins_key_value['ins_base'][$module_key];
            }else{
                $module_res = [];
            }
            return view('backend.insurance_attribute.attribute.create', compact('mid','module_res'));
        }

    }

    public function edit($aid)
    {
        $attribute = InsuranceAttribute::where('id', $aid)->first();
        if(empty($attribute)){
            return back()->withErrors('获取数据失败');
        }
        $ins_key_value = config('ins_key_value');
        $module = InsuranceAttributeModule::where('id', $attribute['mid'])->first();
        if(empty($module)){
            return view('backend.insurance_attribute.attribute.edit', compact('attribute'));
        }else{
            $module_key = $module['module_key'];
            if(key_exists($module_key,$ins_key_value)){
                $module_res = $ins_key_value[$module_key];
            }elseif(key_exists($module_key,$ins_key_value['ins_base'])){
                $module_res = $ins_key_value['ins_base'][$module_key];
            }else{
                $module_res = [];
            }
            return view('backend.insurance_attribute.attribute.edit', compact('attribute','module_res'));
        }


    }

    public function store(Request $request)
    {
        $mid = $request->input('mid');
        $request->merge(['mid' => $mid]);
        $data = $request->all();
        $this->checkAttribute($request);

        InsuranceAttribute::create($data);

        return redirect()->route('insurance_attributes.attributes.index', $mid)->with('status', '添加成功');
    }

    public function update(Request $request, $aid)
    {
        $this->checkAttribute($request);

        $attribute = InsuranceAttribute::where('id', $aid)->first();

        $attribute->update($request->all());

        return redirect()->route('insurance_attributes.attributes.index', $attribute->mid)->with('status', '修改成功');
    }

    protected function checkAttribute(Request $request)
    {
        $rules = [
            'name' => ['max:255'],
            'api_name' => ['max:255']
        ];

        $messages = [
            'name.max' => '属性名称长度应小于255个字符',
            'api_name.max' => 'api接口请求参数名长度应小于255个字符',
        ];

        $this->validate($request, $rules, $messages);
    }
}
