<?php

namespace App\Http\Controllers\BackendControllers\ApiOption;

use App\Models\ApiFrom;
use App\Models\ApiOption;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RelationshipController extends Controller
{
    public function index()
    {
        $apis = ApiFrom::with(['api_option' => function ($query) {
            $query->where('type', 'relationship');
        }])->get();

        return view('backend.api_option.relationship.index', compact('apis'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'api_from_uuid' => 'required|max:255',
            'name' => 'required|max:255',
            'number' => 'required|max:255',
            'code' => 'required|max:255',
        ]);

        $data = $request->input();
        $data['type'] = 'relationship';

        ApiOption::create($data);

        return redirect()->route('api_option.relationship.index')->with('status', '添加成功');
    }
}
