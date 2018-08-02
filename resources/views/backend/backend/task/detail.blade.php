@extends('backend.layout.base')
<link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
@section('content')
    <meta name="_token" content="{{ csrf_token() }}"/>
    <style>
        th,td{
            text-align: center;
        }
    </style>
    <div id="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">
                            <li><a href="{{ url('/backend') }}">主页</a></li>
                            <li ><span>销售管理</span></li>
                            <li><span><a href="{{ url('/backend/task/index') }}">任务管理</a></span></li>
                            <li><span><a href="{{ url('/backend/task/index') }}">任务列表</a></span></li>
                            <li class="active"><span>任务详情</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                {{--<ul class="nav nav-tabs">--}}
                                    {{--<li class="active"><a href="{{ url('/backend/task/index') }}">任务列表</a></li>--}}
                                    {{--<li><a href="{{ url('/backend/task/add_task') }}">新建任务</a></li>--}}
                                    {{--<li><a href="#">详细信息</a></li>--}}
                                {{--</ul>--}}

                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>任务详情</p></h3>
                                        <div class="panel-group accordion" id="account">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        {{--<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion2" href="#account-message">--}}
                                                            任务基本信息
                                                        {{--</a>--}}
                                                    </h4>
                                                </div>
                                                <div id="account-message" class="panel-collapse ">
                                                    <table id="basic" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr>
                                                            <td width="35%">任务名</td>
                                                            <td id="task_name" index="{{ $task_basic->id }}">
                                                                {{ $task_basic->name }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>任务要求类型</td>
                                                            <td>
                                                            @if($task_basic->task_type == 1)
                                                                月任务
                                                            @elseif($task_basic->task_type == 2)
                                                                季度任务
                                                            @elseif($task_basic->task_type == 3)
                                                                年任务
                                                            @elseif($task_basic->task_type == 4)
                                                                其他条件任务
                                                            @endif
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>


                                        {{--<h3><p>任务分组</p></h3>--}}
                                        {{--<button id="add-non-essential-condition">添加条件分组</button>--}}
                                        {{--<div class="panel-group accordion" id="bill">--}}
                                            {{--<div class="panel panel-default">--}}
                                                {{--<div class="panel-heading">--}}
                                                    {{--<h4 class="panel-title">--}}
                                                        {{--<a class="accordion-toggle collapsed add-condition-btn" index="0" style="cursor: pointer;">--}}
                                                            {{--必须条件--}}
                                                        {{--</a>--}}
                                                    {{--</h4>--}}
                                                {{--</div>--}}
                                                {{--<div id="bill-block" class="panel-collapse ">--}}
                                                    {{--<div class="panel-body" style="padding-bottom: 0">--}}
                                                        {{--<table id="product" class="table table-hover" style="clear: both">--}}
                                                            {{--<thead>--}}
                                                                {{--<tr>--}}
                                                                    {{--<th>分组名称</th>--}}
                                                                    {{--<th>操作</th>--}}
                                                                {{--</tr>--}}
                                                            {{--</thead>--}}
                                                            {{--<tbody>--}}
                                                                {{--<tr>--}}
                                                                    {{--<td width="35%">必要条件</td>--}}
                                                                {{--</tr>--}}
                                                            {{--</tbody>--}}
                                                            {{--@if($task_group_count != 0)--}}
                                                                {{--@foreach($task_group as $value)--}}
                                                                    {{--<tr>--}}
                                                                        {{--<td>{{ $value->group_name }}</td>--}}
                                                                        {{--<td>查看详情</td>--}}
                                                                    {{--</tr>--}}
                                                                {{--@endforeach--}}
                                                            {{--@endif--}}

                                                        {{--</table>--}}
                                                    {{--</div>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                        {{----}}


                                        <h3><p>任务条件</p></h3>
                                        <div class="panel-group accordion" id="bill">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        <a class="accordion-toggle collapsed md-trigger "  index="0" style="cursor: pointer;"  data-modal="modal-8">

                                                            必须条件
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="bill-block" class="panel-collapse ">
                                                    <div class="panel-body" style="padding-bottom: 0">
                                                        <table id="product" class="table table-hover" style="clear: both">
                                                            <thead>
                                                            <th>产品限定</th>
                                                            <th>地域限定</th>
                                                            <th>条件类型</th>
                                                            <th>金额要求</th>
                                                            <th>当前状态</th>
                                                            <th>操作</th>
                                                            </thead>
                                                            <tbody>
                                                            @if($count == 0)
                                                                <tr>
                                                                    <td colspan="7">
                                                                        暂无条件
                                                                    </td>
                                                                </tr>
                                                            @else
                                                                @foreach($condition_list as $value)
                                                                    <tr>
                                                                        <td>
                                                                            @if($value->product_id == 0)
                                                                                无限制
                                                                            @else
                                                                                {{ $value->task_product->product_name }}
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if($value->area_id == 0)
                                                                                无区域限制
                                                                            @else
                                                                                {{ $value->area_id }}
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if($value->task_condition_type == 1)
                                                                                不计算折标率
                                                                            @elseif($value->task_condition_type == 2)
                                                                                计算折标率
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if($value->sum == 0)
                                                                                无要求
                                                                            @else
                                                                                {{ $value->sum/100 }} 元
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if($value->status == -1)
                                                                                <a class="label label-info" href="{{ url('/backend/task/change_condition_status/'.$value->id) }}" style="color: white">禁用</a>
                                                                            @else
                                                                                <a  class="label label-primary " href="{{ url('/backend/task/change_condition_status/'.$value->id) }}" style="color: white">启用</a>
                                                                            @endif
                                                                        </td>
                                                                        <td>

                                                                            <a href="{{ url('/backend/task/edit_condition/'.$value->id) }}">修改</a>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="md-modal md-effect-8 md-hide" id="modal-8">
        <div class="md-content">
            <div class="modal-header">
                <button class="md-close close">×</button>
                <h4 class="modal-title">添加特定条件</h4>
            </div>
            {{--{{ $task_detail->id }}--}}
            <div class="modal-body">
                <form method="post" action="{{ url('/backend/task/add_condition') }}" id="product-condition-form" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="text" name='task_id' value="{{ $task_basic->id }}" hidden>
                    <input type="text" name="task_group" value="" hidden>
                    <input type="text" name="condition_id" value="0" hidden>
                    <div class="form-group">
                        <label for="exampleInputEmail1">选择产品<span class="red">*</span></label>
                        <select name="product_id" id="product_id" class="form-control">
                            <option value="0">选择产品</option>
                            @foreach($product_list as $value)
                                <option value="{{ $value->id }}">{{ $value->product_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">选择区域<span class="red">*</span></label>
                        <select name="area_id" id="area_id" class="form-control">
                            <option value="0">无限制</option>
                            <option value="1">北京</option>
                            <option value="2">上海</option>
                            <option value="3">杭州</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">选择类型<span class="red">*</span></label>
                        <select name="task_condition_type" id="appoint-condition" class="form-control">
                            <option value="0">请选择条件</option>
                            <option value="1">不计算折标率</option>
                            <option value="2">计算折标率</option>
                        </select>
                    </div>
                    <div class="form-group" id="appoint-sum-block">
                        <label for="exampleInputPassword1">金额达到要求<span class="red">*</span></label>
                        <input type="text" name="sum" id='appoint-sum' placeholder="请输入金额要求，单位元" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" id='appoint-product-btn' class="btn btn-primary">确认保存</button>
            </div>
        </div>
    </div>
    <div class="md-overlay" id="add-condition-wrap"></div>

    <script src="/js/jquery-3.1.1.min.js"></script>
    <script charset="utf-8" src="/r_backend/js/modernizr.custom.js"></script>
    <script charset="utf-8" src="/r_backend/js/classie.js"></script>
    <script charset="utf-8" src="/r_backend/js/modalEffects.js"></script>
    <script>
        //特定产品条件
        var task_group_input = $('input[name=task_group]');
        var product_condition = $('#product-condition');
        var product_condition_block = $('#product-condition-block');
        var product_condition_form = $('#product-condition-form');
        var appoint_condition = $('#appoint-condition');
        var appoint_sum = $('#appoint-sum');
        var appoint_count = $('#appoint-count');
        var appoint_sum_block = $('#appoint-sum-block');
        var appoint_count_block = $('#appoint-count-block');
//        function reset_appoint_condition(){
//            appoint_sum.val('');
//            appoint_count.val('');
//        }
//        function appoint_condition_block_hidden(){
//            appoint_sum_block.attr('hidden','');
//            appoint_count_block.attr('hidden','');
//        }
//        appoint_condition.change(function(){
//            var appoint_condition_val = $('#appoint-condition option:selected').val();
//            reset_appoint_condition();
//            if(appoint_condition_val == 0){
//                appoint_condition_block_hidden();
//            }else {
//                if(appoint_condition_val == 1){
//                    //金额满足
//                    appoint_condition_block_hidden();
//                    appoint_count.val(0);
//                    appoint_sum_block.removeAttr('hidden');
//                }else if(appoint_condition_val == 2){
//                    appoint_condition_block_hidden();
//                    appoint_sum.val(0);
//                    appoint_count_block.removeAttr('hidden');
//                }else{
//                    appoint_sum_block.removeAttr('hidden');
//                    appoint_count_block.removeAttr('hidden');
//                }
//            }
//        })




        $task_id = $('#task_name').attr('index');



        var appoint_product_btn = $('#appoint-product-btn');
        appoint_product_btn.click(function(){
           //进行验证发送
            var product_id_val = $('#product_id option:selected').val();
            var area_id_val = $('#area_id option:selected').val();

            //验证ajax判断是否为已经存在的条件
            $.ajax({
                type: "post",
                dataType: "json",
                async: true,
                //修改的地址，
                url: "/backend/task/check_condition_ajax",
                data: 'task_id='+$task_id+'&product_id='+product_id_val+'&area_id='+area_id_val,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function(data){
                    var status = data['status'];
                    if(status == 200){
                        if(confirm('该条件已经存在了，您确定要修改吗？')){
                            $('input[name=condition_id]').val(data['data']);
                            product_condition_form.attr('action','/backend/task/edit_condition_submit');
                        }else{


                        }
                    }

                    product_condition_form.submit();
                },error: function () {
                    alert('错误');
                }
            });
        })

//        var add_condition_btn = $('.add-condition-btn');
//
//        var add_condition_wrap = $('#add-condition-wrap');
//        var add_condition_block = $('#add-condition-block');
//        add_condition_wrap.click(function(){
//            add_condition_block.removeClass('md-show');
//        })
//        add_condition_btn.click(function () {
//            var task_group = $(this).attr('index');
//            task_group_input.val(task_group);
//            add_condition_block.addClass('md-show');
//        })





    </script>
@stop

