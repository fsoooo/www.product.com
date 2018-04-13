@extends('backend.layout.base')
<link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
@section('content')
    <meta name="_token" content="{{ csrf_token() }}"/>
    <style>

    </style>
    <div id="content-wrapper">
        <div class="big-img" style="display: none;">
            <img src="" alt="" id="big-img" style="width: 75%;height: 90%;">
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">
                            <li><a href="{{ url('/backend') }}">主页</a></li>
                            <li ><span>销售管理</span></li>
                            <li><span><a href="{{ url('/backend/task/index') }}">任务管理</a></span></li>
                            <li class="active"><span><a href="{{ url('/backend/task/add_task') }}">新建任务</a></span></li>
                        </ol>

                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li><a href="{{ url('/backend/task/index') }}">任务列表</a></li>
                                    <li class="active"><a href="{{ url('/backend/task/add_task') }}">新建任务</a></li>
                                    {{--<li><a href="">查看完成情况</a></li>--}}
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        @include('backend.layout.alert_info')
                                        <div class="panel-group accordion" id="operation">
                                            <div class="panel panel-default">
                                                <form action="{{ url('/backend/task/add_task_submit') }}" method="post" id="form">
                                                    {{ csrf_field() }}
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr>
                                                            <td>任务名称</td>
                                                            <td><input type="text" name="name" placeholder="请输入任务名称" class="form-control"></td>
                                                        </tr>
                                                        <tr>
                                                            <td width="15%">任务类型</td>
                                                            <td width="60%">
                                                                <select name="task_type" id="task-type" class="form-control">
                                                                    <option value="0">请选择任务类型</option>
                                                                    <option value="1">每月任务</option>
                                                                    <option value="2">季度任务</option>
                                                                    <option value="3">年度任务</option>
                                                                    <option value="4">特定条件任务</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr id="start-time-block" hidden>
                                                            <td>开始时间</td>
                                                            <td>
                                                                <input type="date" name="start_time" id="start-time" min="{{ $min_time }}" class="form-control">
                                                            </td>
                                                        </tr>
                                                        <tr id="end-time-block" hidden>
                                                            <td>结束时间</td>
                                                            <td>
                                                                <input type="date" name="end_time" id="end-time" min="{{ $min_time }}" class="form-control">
                                                            </td>
                                                        </tr>
                                                        <div id="product-condition">
                                                            <tr>
                                                                <td colspan="2" id="product-condition-block"></td>
                                                            </tr>
                                                        </div>
                                                        </tbody>
                                                    </table>
                                                </form>

                                            </div>
                                            <button id="btn" class="btn btn-success">添加</button>
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




    <script src="/js/jquery-3.1.1.min.js"></script>
    <script charset="utf-8" src="/r_backend/js/modernizr.custom.js"></script>
    <script charset="utf-8" src="/r_backend/js/classie.js"></script>
    <script charset="utf-8" src="/r_backend/js/modalEffects.js"></script>


    <script>
        $(function(){
            //条件
            var start_time_block = $('#start-time-block');
            var end_time_block = $('#end-time-block');
            var start_time = $('#start_time');
            var end_time = $('#end_time');
            function time_block_hidden()
            {
                start_time_block.attr('hidden','');
                end_time_block.attr('hidden','');
            }
            function time_block_show()
            {
                start_time_block.removeAttr('hidden');
                end_time_block.removeAttr('hidden');
            }
            function reset_time_block()
            {
                start_time.val('');
                end_time.val('');
            }
            //设置任务类型
            var task_type = $('#task-type');
            task_type.change(function () {
                var task_type_val = $('#task-type option:selected').val();
                reset_time_block();
                if(task_type_val == 4){
                    time_block_show();
                }else{
                    time_block_hidden();
                }
            })

                //进行验证发送
                var name = $('input[name=name]');
                var task_type = $('input[name=task_type]');
                var start_time = $('#start-time');
                var end_time = $('#end-time');



                var btn = $('#btn');
                var form =  $('#form');
                btn.click(function(){
                    var task_type_val = $('#task-type option:selected').val();
                    var start_time_val = start_time.val();
                    var end_time_val = end_time.val();
                    //进行任务名称验证
                    var name_val = name.val();
                    if(name_val == ''){
                        name.parent().addClass("has-error");
                        alert('请输入任务名称');
                        return false;
                    }else if(task_type_val == 0){
                        name.parent().removeClass("has-error");
                        task_type.parent().addClass("has-error");
                        alert('请选择任务分组');
                        return false;
                    }else if(task_type_val == '4'){//特定条件任务，对时间进行判断
                        var end = end_time_val>start_time_val;
                       if(end){
                           form.submit();
                       }else{
                           alert('结束时间不能小于开始时间');
                       }
                    }else if(task_type_val != 4){
                        $.ajax({
                            type: "post",
                            dataType: "json",
                            async: true,
                            //修改的地址，
                            url: "/backend/task/check_task_ajax",
                            data: 'task_type='+task_type_val,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                            },
                            success: function(data){
                                var status = data['status'];
                                if(status == 200){
                                    if(confirm('该种任务类型只能有一种生效，继续操作将使其他同类型失效，是否继续')){
                                        form.submit();
                                    }else{

                                    }
                                }else {
                                    form.submit();
                                }
                            },error: function () {
                                alert('错误');
                            }
                        });
                    }else {
                        form.submit();
                    }
                })

        })
    </script>
@stop

