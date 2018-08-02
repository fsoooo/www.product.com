<?php

namespace App\Http\Controllers\BackendControllers\RestrictGene;

use App\Models\Insurance;
use App\Models\RestrictGene;
use Illuminate\Http\Request;
use App\Models\InsuranceApiInfo;
use App\Repositories\InsuranceApiFromRepository;
use App\Http\Controllers\backendControllers\BaseController;

class RestrictGeneController extends BaseController
{
    public function index($bind_id, InsuranceApiFromRepository $repository)
    {
        $restrict_genes = RestrictGene::where('bind_id', $bind_id)->paginate(30);
        $relation = $repository->getBindRelationByBindId($bind_id);

        $data = compact('restrict_genes', 'bind_id', 'relation');
        return view('backend.restrict_gene.index', $data);
    }

    public function create($bind_id)
    {
        $clauses = Insurance::find(InsuranceApiInfo::find($bind_id)->insurance_id)->clauses;
        $quote_base = config('ins_key_value')['quote_base'];
        return view('backend.restrict_gene.create', compact('bind_id', 'quote_base', 'clauses'));
    }

    public function edit($rid)
    {
        $restrict_gene = RestrictGene::where('id', $rid)->first();
        $clauses = Insurance::find(InsuranceApiInfo::find($restrict_gene->bind_id)->insurance_id)->clauses;
        $quote_base = config('ins_key_value')['quote_base'];
        return view('backend.restrict_gene.edit', compact('restrict_gene', 'quote_base', 'clauses'));
    }

    public function store(Request $request)
    {
        $bind_id = $request->input('bind_id');
        $request->merge(['bind_id' => $bind_id]);
        $data = $request->all();
        $this->check($request);

        RestrictGene::create($data);

        return redirect()->route('restrict_genes.index', $bind_id)->with('status', '添加成功');
    }

    public function update(Request $request, $rid)
    {
        $this->check($request);

        $restrict_gene = RestrictGene::where('id', $rid)->first();

        $restrict_gene->update($request->all());

        return redirect()->route('restrict_genes.index', $restrict_gene->bind_id)->with('status', '修改成功');
    }

    protected function check(Request $request)
    {
        $rules = [
            'key' => ['required', 'max:255'],
            'name' => ['required', 'max:255']
        ];

        $messages = [
            'key.required' => '请填写试算因子对应属性Key',
            'key.max' => '试算因子对应属性Key长度应小于255个字符',
            'name.required' => '请填写试算因子名',
            'name.max' => '试算因子名长度应小于255个字符'
        ];

        $this->validate($request, $rules, $messages);
    }
}
