<?php

namespace App\Http\Controllers\BackendControllers\InsuranceAttribute;

use App\Http\Controllers\Controller;
use App\Models\InsuranceAttributeModule;
use App\Repositories\InsuranceApiFromRepository;
use App\Repositories\InsuranceAttributesRepository;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index($bind_id, InsuranceApiFromRepository $repository)
    {
//        dd((new InsuranceAttributesRepository())->findAttributesRecursionByBindId($bind_id));
        $modules = InsuranceAttributeModule::where('bind_id', $bind_id)->paginate(30);
        $relation = $repository->getBindRelationByBindId($bind_id);
        $data = compact('modules', 'bind_id', 'relation');

        return view('backend.insurance_attribute.module.index', $data);
    }

    public function create($bind_id)
    {
        return view('backend.insurance_attribute.module.create', compact('bind_id'));
    }

    public function edit($mid)
    {
        $module = InsuranceAttributeModule::where('id', $mid)->first();

        return view('backend.insurance_attribute.module.edit', compact('module'));
    }

    public function store(Request $request)
    {
        $bind_id = $request->input('bind_id');
        $request->merge(['bind_id' => $bind_id]);
        $data = $request->all();
        $this->checkModule($request);

        InsuranceAttributeModule::create($data);

        return redirect()->route('insurance_attributes.modules.index', $bind_id)->with('status', '添加成功');
    }

    public function update(Request $request, $mid)
    {
        $module = InsuranceAttributeModule::where('id', $mid)->first();

        $this->checkModule($request);

        $module->name = $request->input('name');
        $module->remark = $request->input('remark');
        $module->module_key = $request->input('module_key');
        $module->save();

        return redirect()->route('insurance_attributes.modules.index', $module->bind_id)->with('status', '修改成功');
    }

    protected function checkModule(Request $request)
    {
        $rules = [
            'name' => ['max:255']
        ];

        $messages = [
            'name.max' => '模块名称长度应小于255个字符'
        ];

        $this->validate($request, $rules, $messages);
    }
}
