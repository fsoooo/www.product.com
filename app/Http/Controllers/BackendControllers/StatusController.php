<?php

namespace App\Http\Controllers\BackendControllers;

use App\Models\Node;
use App\Models\NodeCondition;
use App\Models\Status;
use App\Http\Controllers\Controller;
use App\Models\StatusRule;
use App\Models\TableField;
use Illuminate\Support\Facades\DB;
use App\Models\StatusClassify;
use App\Models\StatusGroup;
use Request;
class StatusController extends BaseController
{
    public function index()
    {
        //查所有的状态，并查询 对应的表和父状态，跟状态
        $list = Status::with('status_group')->get();
        return view('backend.status.index',compact('list','count'));
    }
    //添加状态
    public function addStatus()
    {

        //获取所有的表和对应的描述
        $tables = TableField::where('status',0)
            ->get();
        //获取所有的状态分类
        $status_group = $this->getAllStatusGroup();
        $status_count = count($status_group);
        return view('backend.status.add',compact('status_group','tables','status_count'));
    }
    //添加状态表单提交
    public function addStatusSubmit()
    {
        $input = Request::all();
        $Status = new Status();
        $StatusArray = array(
            'status_name'=>$input['status_name'],
            'describe'=>$input['describe'],
            'field_id'=>$input['field_id'],
            'group_id'=>$input['group_id'],
            'status'=>0,
        );
        $result = $this->add($Status,$StatusArray);
        if($result){
            return redirect('/backend/status/index')->with('status','添加成功');
        }else{
            return back()->withErrors('添加失败')->withInput($input);
        }
    }

    //跳转到状态分类界面
    public function getGroup()
    {

        $status_group = $this->getAllStatusGroup();
        $count = count($status_group);
        return view('backend.status.StatusGroup',compact('status_group','count'));
    }
    //跳转到添加分组页面
    public function addGroup()
    {
        return view('backend.status.AddGroup');
    }
    //分组表单提交
    public function addGroupSubmit()
    {
        $input = Request::all();
        $StatusGroup = new StatusGroup();
        $StatusGroupArray = array(
            'group_name'=>$input['group_name'],
            'group_describe'=>$input['group_describe'],
            'status'=>0,
        );
        $result = $this->add($StatusGroup,$StatusGroupArray);
        if($result){
            return redirect('backend/status/group')->with('status','添加成功');
        }else{
            return back()->withErrors('添加失败')->withInput($input);
        }
    }


    //跳转到状态详情页面
    public function getStatusDetail($status_id)
    {
        //判断有无该状态

        //获取状态的详细信息
        $status_detail = Status::where('id',$status_id)->with('status_group','status_field')->first();
        //获取该表的其他状态值
        $field_id = $status_detail->field_id;
        $status_list = Status::where('field_id',$field_id)
            ->where('id','!=',$status_detail['id'])
            ->get();
        //获取该状态的所有上一级
        $parent_list = StatusRule::where('status_id',$status_id)
            ->with('parent_rule_status')->get();
        //获取素有下一级的状态
        $children_list = StatusRule::where('parent_id',$status_id)
            ->with('children_rule_status')->get();
        $parent_count = count($parent_list);
        $children_count = count($children_list);
        return view('backend.status.StatusDetail',compact('status_list','status_detail','parent_list','children_list','parent_count','children_count'));
    }

    //跳转到添加状态关系页面
    public function addStatusRelation()
    {
        $group_list = StatusGroup::get();
        return view('backend.status.StatusRelation',compact('group_list'));
    }
    //添加状态关系表单提交
    public function addStatusRelationSubmit()
    {
        $input = Request::all();
        $parent_id = $input['parent_id'];
        $status_id = $input['status_id'];
        //判断是否已经存在该关系了
        $isRelation = StatusRule::where('parent_id',$parent_id)
            ->where('status_id',$status_id)
            ->where('status',0)
            ->count();
        $isRelation1 = StatusRule::where('parent_id',$status_id)
            ->where('status_id',$parent_id)
            ->where('status',0)
            ->count();


        if($isRelation||$isRelation1){
            return back()->withErrors('已经添加过该关系了,请勿重复添加');
        }else{
            //添加到关系中
            $StatusRule = new StatusRule();
            $StatusRuleArray = array(
                'parent_id'=>$input['parent_id'],
                'status_id'=>$input['status_id'],
                'status'=>0
            );
            $result = $this->add($StatusRule,$StatusRuleArray);
        }
        if($result){
            return back()->with('status','添加成功');
        }else{
            return back()->withErrors('添加失败');
        }
    }
    //跳转到状态分组详情页面
    public function getGroupDetail($group_id)
    {//读取分组信息和下属的子状态
        $group_detail = StatusGroup::where('id',$group_id)
            ->with('group_status')->first();
        if($group_detail){
            $count = count($group_detail->group_status);
            return view('backend.status.GroupDetail',compact('group_detail','count'));
        }else{
            return back()->withErrors('非法操作');
        }




    }


    //封装一个方法，用来获取所有的状态分组
    public function getAllStatusGroup()
    {
        $status_group = StatusGroup::paginate(config('list_num.backend.claim'));
        return $status_group;
    }


    //封装一个方法，用来获取所有的表名
    public function getTables()
    {
        $list = DB::table('table_field')
            ->select('table')
            ->distinct()
            ->get();
        $count = count($list);
        if($count){
            return $list;
        }else{
            return false;
        }
    }


    //写一个方法，ajax，通过分组，获取该分组下的所有的状态
    public function getStatusByGroupAjax()
    {
        $input = Request::all();
        $group_id = $input['group_id'];
        $result = Status::where('group_id',$group_id)
            ->get();
        echo returnJson('200',$result);
    }
    //写一个方法，通过父级状态，获取所有的该表的状态
    public function getChildrenStatusAjax()
    {
        $input = Request::all();
        $status_id = $input['id'];
        //获取对应的表名
        $table_field_id = Status::where('id',$status_id)->first()->field_id;
        //通过表名查找到所有的id
        $status_id_array = Status::where('field_id',$table_field_id)
            ->where('id','!=',$status_id)->get();
        $count = count($status_id_array);
        if($count){
            echo returnJson('200',$status_id_array);
        }else{
            echo returnJson('0','暂无同类状态');
        }
    }
}
