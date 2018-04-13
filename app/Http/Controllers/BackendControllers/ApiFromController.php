<?php
/**
 * Created by PhpStorm.
 * User: xyn
 * Date: 2017/6/19
 * Time: 15:19
 */

namespace App\Http\Controllers\BackendControllers;

use App\Models\ApiFrom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApiFromController extends BaseController
{
    public function index()
    {
        $api_from = ApiFrom::paginate(config('list_num.api_from'));
//        dd($api_from);

        return view('backend.api_from.index',compact('api_from'));
    }

//    public function add()
//    {
//        return view('backend.api_from.add');
//    }

    public function addPost(Request $request)
    {
        $rules = [
            'name' => ['max:155', 'unique:api_from'],
            'uuid' => ['max:255', 'unique:api_from'],
        ];
        $this->validate($request, $rules);

        if(ApiFrom::create($request->input())){
            return redirect('backend/product/api_from')->with('status', '成功录入接口来源!');
        }
        return redirect('backend/product/api_from')->withErrors('录入失败');
    }

    //更改
    public function edit($id)
    {
        $input = $this->request->all();
//        dd($input);
        $data = [
            'name'=>$input['name'],
            'uuid'=>$input['uuid'],
            'count_api'=>$input['count_api'],
            'hebao_api'=>$input['hebao_api'],
            'toubao_api'=>$input['toubao_api'],
            'pay_api'=>$input['pay_api'],
            'issue_api'=>$input['issue_api'],
        ];
        DB::table('api_from')
            ->where('id',$id)
            ->update($data);
        return redirect('backend/product/api_from')->with('status','成功更新接口来源!');
    }

    //删除
    public function delete($id)
    {
        DB::table('api_from')
            ->where('id',$id)
            ->delete();
        return redirect('backend/product/api_from')->with('status','成功删除接口来源!');
    }
}