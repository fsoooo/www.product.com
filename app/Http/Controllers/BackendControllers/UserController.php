<?php

namespace App\Http\Controllers\BackendControllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * 代理商API账户管理
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::paginate(config('list_num.user'));

        return view('backend.user.index', compact('users'));
    }

    /**
     * 存储账户数据
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->storeValidate($request);
        $data = $request->input();
        $rand = substr(str_shuffle('123456789'),0,4);
        $data['account_id'] = time() . $rand;
        $data['password'] = bcrypt($data['password']);
        $data['sign_key'] = MD5($data['password']);
        $res = User::create($data);
        return redirect('backend/user')->with('status', '添加成功');
    }

    /**
     * 更新账户数据
     *
     * @param  \Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function update(Request $request, $id)
    {
        $this->updateValidate($request, $id);

        $data = $request->input();

        if ($data['password']) {
            $data['password'] = bcrypt($data['password']);
            $data['sign_key'] = MD5($data['password']);
        } else {
            unset($data['password']);
        }
        User::find($id)->update($data);
        return redirect('backend/user')->with('status', '更新成功');
    }

    /**
     * 删除数据
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::destroy($id);

        return redirect('backend/user')->with('status', '删除成功');
    }

    /**
     * 新增数据验证
     *
     * @param Request $request
     */
    protected function storeValidate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:100|unique:users',
//            'password' => 'required|max:32',
//            'account_id' => 'required|max:100|unique:users',
//            'sign_key' => 'required|max:255|unique:users',
        ]);
    }

    /**
     * 更新数据验证
     *
     * @param Request $request
     * @param $id
     */
    protected function updateValidate(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => [
                'required',
                'email',
                'max:100',
                Rule::unique('users')->ignore($id),
            ],
//            'password' => 'max:32',
//            'account_id' => [
//                'required',
//                'max:100',
//                Rule::unique('users')->ignore($id),
//            ],
//            'sign_key' => [
//                'required',
//                'max:255',
//                Rule::unique('users')->ignore($id),
//            ],
        ]);
    }
}
