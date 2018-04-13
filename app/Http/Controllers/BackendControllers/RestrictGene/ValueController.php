<?php

namespace App\Http\Controllers\BackendControllers\RestrictGene;

use App\Http\Controllers\backendControllers\BaseController;
use App\Models\RestrictGene;
use App\Models\RestrictGeneValue;
use App\Repositories\InsuranceApiFromRepository;
use Illuminate\Http\Request;

class ValueController extends BaseController
{
    public function index(
        $rid,
        InsuranceApiFromRepository $repository)
    {
        $values = RestrictGeneValue::where('rid', $rid)->paginate(30);
        $restrict_gene = RestrictGene::where('id', $rid)->first();
        $relation = $repository->getBindRelationByBindId($restrict_gene->bind_id);

        return view('backend.restrict_gene.value.index', compact('values', 'restrict_gene', 'relation'));
    }

    public function create($rid)
    {
        return view('backend.restrict_gene.value.create', compact('rid'));
    }

    public function edit($vid)
    {
        $value = RestrictGeneValue::where('id', $vid)->first();

        return view('backend.restrict_gene.value.edit', compact('value', 'vid'));
    }

    public function store(Request $request)
    {
        $rid = $request->input('rid');
        $data = $request->all();
        $this->check($request);

        RestrictGeneValue::create($data);

        return redirect()->route('restrict_genes.values.index', $rid)->with('status', '添加成功');
    }

    public function update(Request $request, $vid)
    {
        $this->check($request);

        $value = RestrictGeneValue::where('id', $vid)->first();

        $value->update($request->all());

        return redirect()->route('restrict_genes.values.index', $value->rid)->with('status', '修改成功');
    }

    protected function check(Request $request)
    {
        $rules = [
            'name' => ['max:255']
        ];

        $messages = [
            'name.max' => '选项名称长度应小于255个字符'
        ];

        $this->validate($request, $rules, $messages);
    }
}
