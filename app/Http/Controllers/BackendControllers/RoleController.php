<?php

namespace App\Http\Controllers\BackendControllers;

use DB, Auth, Validator;
use App\Models\Role;
use App\Models\AdminUser;
use App\Models\Permission;


class RoleController extends BaseController
{

    /**
     * @return mixed
     * 角色列表
     */
    public function roles()
    {
        $roles = Role::paginate(config('list_num.backend.roles')); //分页查询 每页数量已统一写入配置文件
        return view('backend.role.roles',compact('roles'));
    }

    /**
     * @return mixed
     * 橘色添加
     */
    public function addRolePost()
    {
        $input = $this->request->all();

        //验证
        $validator = $this->checkAddRolePost($input);
        if ($validator->fails()) {
            return redirect('backend/role/roles')
                ->withErrors($validator)
                ->withInput();
        }

        //添加
        $role = new Role();
        $role->name = $input['name'];
        $role->display_name = $input['display_name'];
        $role->description = $input['description'];

        if(!$role->save()){
            return back()->withErrors('角色录入失败！');
        }
        return redirect('backend/role/roles')->with('status', '成功录入角色信息!');
    }

    /**
     * @param $input
     * @return mixed
     * 角色添加表单验证
     */
    protected function checkAddRolePost($input)
    {
        //规则
        $rules = [
            'name' => 'required|unique:roles|min:5|max:50',
            'display_name' => 'required|min:5|max:100',
            'description' => 'required|min:5|max:100',
        ];

        //自定义错误信息
        $messages = [
            'required' => 'The :attribute is null.',
            'unique' => 'The :attribute exist.',
            'max' => 'The :attribute length must be < :max.',
            'min' => 'The :attribute length must be > :min.'
        ];
        //验证
        $validator = Validator::make($input, $rules, $messages);
        return $validator;
    }



    /**
     * 角色删除
     */
    public function omitRole(){
        $id = $_GET['id'];
        $res = Role::where('id', '=', $id)->delete();
        if($res){
            return redirect('backend/role/roles')->with('status', '成功删除角色信息!');
        }else{
            return back()->withErrors('角色删除失败！');
        }
    }

    /**
     *
     * 角色修改
     */
    public function modify(){
        $input  = $this->request->all();

        $role_id   = $input['role_id'];
        $name   = $input['name'];
        $display_name   = $input['display_name'];
        $description   = $input['description'];

        $result =Role::where(['id'=>$role_id])->update(['name' => $name,'display_name'=>$display_name,'description'=>$description]);
        if($result){
            return redirect('backend/role/roles')->with('status', '成功修改角色信息!');
        }else{
            return back()->withErrors('角色修改失败！');
        }
    }

    /**
     * @return mixed
     * 权限列表
     */
    public function Permissions()
    {
        $permissions = Permission::paginate(config('list_num.backend.permissions'));
        return view('backend.role.permissions',compact('permissions'));
    }

    /**
     * @return mixed
     * 权限添加
     */
    public function addPermissionPost()
    {
        $input = $this->request->all();

        //验证
        $validator = $this->checkPermissionPost($input);
        if ($validator->fails()) {
            return redirect('backend/role/roles')
                ->withErrors($validator)
                ->withInput();
        }

        //添加
        $createPost = new Permission();
        $createPost->name = $input['name'];
        $createPost->display_name = $input['display_name'];
        $createPost->description = $input['description'];

        if(!$createPost->save()){
            return back()->withErrors('权限录入失败！');
        }
        return redirect('backend/role/permissions')->with('status', '成功录入权限信息!');
    }

    /**
     * @param $input
     * @return mixed
     * 权限添加表单验证
     */
    protected function checkPermissionPost($input)
    {
        //规则
        $rules = [
            'name' => 'required|unique:permissions|min:5|max:50',
            'display_name' => 'required|min:5|max:100',
            'description' => 'required|min:5|max:100',
        ];

        //自定义错误信息
        $messages = [
            'required' => 'The :attribute is null.',
            'unique' => 'The :attribute exist.',
            'max' => 'The :attribute length must be < :max.',
            'min' => 'The :attribute length must be > :min.'
        ];
        //验证
        $validator = Validator::make($input, $rules, $messages);
        return $validator;
    }

    /**
     * @return mixed
     * 角色权限展示
     */
    public function roleBindPermission(){
        $roles = Role::all();
        return view('backend.role.role_bind_permission',compact('roles'));
    }

    /**
     * omitpower
     * 权限删除
     */
    public function omitpower(){
        $id = $_GET['id'];

        $res = DB::table('permissions')->where('id', '=', $id)->delete();
        if($res){
            return redirect('backend/role/permissions')->with('status', '成功删除权限信息!');
        }else{
            return back()->withErrors('权限删除失败！');
        }
    }

    /**
     * omitRole
     * 权限修改
     */
    public function modifypower(){
        $input  = $this->request->all();

        $power_id   = $input['power_id'];
        $name   = $input['name'];
        $display_name   = $input['display_name'];
        $description   = $input['description'];

        $result =DB::table('permissions')->where(['id'=>$power_id])->update(['name' => $name,'display_name'=>$display_name,'description'=>$description]);
        if($result){
            return redirect('backend/role/permissions')->with('status', '成功修改权限信息!');
        }else{
            return back()->withErrors('权限修改失败！');
        }
    }



    /**
     * @return mixed
     * 角色权限关联查询
     */
    public function roleFindPermissions()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        $role_id = $this->request->get('role_id');

        //角色及其已关联的权限
        $role = Role::with(['permissions'=>function($q){
//            $q->where('permission_id',3);  //渴求式加载
        }])->where('id',$role_id)->first();

        //拼接权限ID 数组
        $role_permission_ids = array();
        if($role && count($role->permissions)){
            foreach($role->permissions as $k => $v){
                $role_permission_ids[] = $v->id;
            }
        }
        return view('backend.role.role_bind_permission',compact('role', 'roles','role_permission_ids', 'permissions'));
    }

    /**
     * @return mixed
     * 角色权限关联
     */
    public function attachPermissions()
    {
        $input = $this->request->all();
        //验证
        $validator = $this->checkAttachPermissionsPost($input);
        if ($validator->fails()) {
            return redirect('backend/role/role_bind_permission')
                ->withErrors($validator)
                ->withInput();
        }

        $role = Role::find($input['check_role_id']);
        if(empty($input['permission_ids'])){
            $role->perms()->detach();
        } else {
            $role->perms()->sync($input['permission_ids']);
        }

        return redirect('backend/role/role_bind_permission')->with('status', '关联成功!');

    }

    protected function checkAttachPermissionsPost($input)
    {
        //规则
        $rules = [
            'check_role_id' => 'required|integer',
            'permission_ids' => 'array',
        ];

        //自定义错误信息
        $messages = [
            'required' => 'The :attribute is null.',
            'integer' => 'The :attribute mast be integer.',
        ];
        //验证
        $validator = Validator::make($input, $rules, $messages);
        return $validator;
    }


//    =======================账户 && 角色==============================


    /**
     * @return mixed
     * 账户角色展示
     */
    public function userBindRoles()
    {
        $users = AdminUser::all();
        return view('backend.role.user_bind_roles',compact('users'));
    }

    /**
     * @return mixed
     * 账户角色查询
     */
    public function userFindRoles()
    {
        $users = AdminUser::all();
        $roles = Role::all();
        $user_id = $this->request->get('role_id');

        //用户已关联的角色
        $user = AdminUser::with(['roles'=>function($q){
//            $q->where('permission_id',3);  //渴求式加载
        }])->where('id',$user_id)->first();

        //拼接权限ID 数组
        $user_role_ids = array();
        if($user && count($user->roles)){
            foreach($user->roles as $k => $v){
                $user_role_ids[] = $v->id;
            }
        }
        return view('backend.role.user_bind_roles',compact('user', 'users','user_role_ids', 'roles'));
    }

    /**
     * @return mixed
     * 账户角色绑定
     */
    public function attachRoles()
    {
        $input = $this->request->all();
        //验证
        $validator = $this->checkAttachRolesPost($input);
        if ($validator->fails()) {
            return redirect('backend/role/user_bind_roles')
                ->withErrors($validator)
                ->withInput();
        }

        $user = Adminuser::find($input['check_user_id']);
        if(empty($input['role_ids'])){
            $user->roles()->detach();
        } else {
            $user->roles()->sync($input['role_ids']); //只需传递id即可
        }

        return redirect('backend/role/user_bind_roles')->with('status', '关联成功!');

    }

    protected function checkAttachRolesPost($input)
    {
        //规则
        $rules = [
            'check_user_id' => 'required|integer',
            'role_ids' => 'array',
        ];

        //自定义错误信息
        $messages = [
            'required' => 'The :attribute is null.',
            'integer' => 'The :attribute mast be integer.',
        ];
        //验证
        $validator = Validator::make($input, $rules, $messages);
        return $validator;
    }


}