<?php

namespace App\Http\Controllers\BackendControllers\InsuranceAttribute;

use App\Http\Controllers\Controller;
use App\Models\InsuranceAttribute;
use App\Models\InsuranceAttributeValue;
use App\Repositories\InsuranceApiFromRepository;
use Illuminate\Http\Request;

class ValueController extends Controller
{
    public function index($aid, InsuranceApiFromRepository $repository)
    {
        $attribute = InsuranceAttribute::with('module')->where('id', $aid)->first();
        $values = InsuranceAttributeValue::where('aid', $aid)->get();
        $relation = $repository->getBindRelationByBindId($attribute->module->bind_id);
        return view('backend.insurance_attribute.value.index', compact('values', 'aid', 'attribute', 'relation'));
    }

    public function create($aid)
    {
        $attribute = InsuranceAttribute::with('module')->where('id', $aid)->first();
        if(empty($attribute)){
            return back()->withErrors('获取数据失败！');
        }
        $ty_keys = explode('_',$attribute->ty_key);
        $ty_key = $ty_keys[count($ty_keys)-1];
//        $ty_key_on = $ty_keys[1];
//        if($ty_key_on=='toubaoren'||$ty_key_on=='beibaoren'||$ty_key_on=='shouyiren') {
//            $ins_key_value = [];
//        }else{
            $ins_key_value = isset(config('ins_key_value')[$ty_key])?config('ins_key_value')[$ty_key]:[];
//        }
        return view('backend.insurance_attribute.value.create', compact('aid','ins_key_value'));
    }

    public function edit($vid)
    {
        $value = InsuranceAttributeValue::where('id', $vid)->first();

        return view('backend.insurance_attribute.value.edit', compact('value', 'vid'));
    }

    public function store(Request $request)
    {
        $aid = $request->input('aid');
        $request->merge(['aid' => $aid]);
        $data = $request->all();
        $this->checkValue($request);

        InsuranceAttributeValue::create($data);

        return redirect()->route('insurance_attributes.values.index', $aid)->with('status', '添加成功');
    }

    public function update(Request $request, $vid)
    {
        $this->checkValue($request);

        $value = InsuranceAttributeValue::where('id', $vid)->first();

        $value->update($request->all());

        return redirect()->route('insurance_attributes.values.index', $value->aid)->with('status', '修改成功');
    }

    protected function checkValue(Request $request)
    {
        $rules = [
            'value' => ['required', 'max:255']
        ];

        $messages = [
            'name.required' => '请填写属性名称',
            'name.max' => '属性名称长度应小于255个字符'
        ];

        $this->validate($request, $rules, $messages);
    }
}
