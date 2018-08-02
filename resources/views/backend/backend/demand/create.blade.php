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
                            <li class="active"><span>任务管理</span></li>
                        </ol>
                        <h1>任务管理</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li><a href="{{ url('/backend/task/index') }}">任务列表</a></li>
                                    <li class="active"><a href="{{ url('/backend/task/add_task') }}">新建任务</a></li>
                                    <li><a href="">查看完成情况</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>任务列表</p></h3>
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
                                                                <input type="datetime-local" name="start_time" id="start-time" class="form-control">
                                                            </td>
                                                        </tr>
                                                        <tr id="end-time-block" hidden>
                                                            <td>结束时间</td>
                                                            <td>
                                                                <input type="datetime-local" name="end_time" id="end-time" class="form-control">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>完成条件</td>
                                                            <td>
                                                                <select name="condition_type" id="condition-type" class="form-control">
                                                                    <option value="0">请选择条件</option>
                                                                    <option value="1">金额达标</option>
                                                                    <option value="2">数量达标</option>
                                                                    <option value="3">数量或金额达标</option>
                                                                    <option value="4">数量和金额达标</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr id="count-block" hidden>
                                                            <td>数量要求</td>
                                                            <td>
                                                                <input type="text" name="count" id="count" placeholder="请输入数量要求，单位单" class="form-control">
                                                            </td>
                                                        </tr>
                                                        <tr id="sum-block" hidden>
                                                            <td>金额要求</td>
                                                            <td>
                                                                <input type="text" name="sum" id="sum" placeholder="请输入金额要求，单位元" class="form-control">
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
            var sum = $('#sum');
            var count = $('#count');
            var sum_block = $('#sum-block');
            var count_block = $('#count-block');
            var start_time_block = $('#start-time-block');
            var end_time_block = $('#end-time-block');
            var start_time = $('#start_time');
            var end_time = $('#end_time');
            function reset_condition()
            {
                sum.val('');
                count.val('');
            }
            function condition_block_hidden()
            {
                start_time_block.attr('hidden','');
                end_time_block.attr('hidden','');
            }
            function time_block_hidden()
            {
                sum_block.attr('hidden','');
                count_block.attr('hidden','');
            }
            function time_block_show()
            {

            }
            function reset_condition()
            {
                sum.val('');
                count.val('');
            }
            //设置任务类型
            var task_type = $('#task-type');
            task_type.change(function () {
                var task_type_val = $('#task-type option:selected').val();
                reset_task_type();
                if(task_type_val)
            })
            //设置条件
            var condition_type = $('#condition-type');
            condition_type.change(function(){
                var condition_type_val = $('#condition-type option:selected').val();
                reset_condition();
                if(condition_type_val == 0){
                    condition_block_hidden();
                }else {
                    if(condition_type_val == 1){
                        //金额满足
                        condition_block_hidden();
                        count.val(0);
                        sum_block.removeAttr('hidden');
                    }else if(condition_type_val == 2){
                        condition_block_hidden();
                        sum.val(0);
                        count_block.removeAttr('hidden');
                    }else{
                        sum_block.removeAttr('hidden');
                        count_block.removeAttr('hidden');
                    }
                }
            })

            //特定产品条件
            var product_condition = $('#product-condition');
            var product_condition_block = $('#product-condition-block');
            var appoint_form = $('#appoint-form');
            var appoint_condition = $('#appoint-condition');
            var appoint_sum = $('#appoint-sum');
            var appoint_count = $('#appoint-count');
            var appoint_sum_block = $('#appoint-sum-block');
            var appoint_count_block = $('#appoint-count-block');
            function reset_appoint_condition(){
                appoint_sum.val('');
                appoint_count.val('');
            }
            function appoint_condition_block_hidden(){
                appoint_sum_block.attr('hidden','');
                appoint_count_block.attr('hidden','');
            }
            appoint_condition.change(function(){
                var appoint_condition_val = $('#appoint-condition option:selected').val();
                reset_appoint_condition();
                if(appoint_condition_val == 0){
                    appoint_condition_block_hidden();
                }else {
                    if(appoint_condition_val == 1){
                        //金额满足
                        appoint_condition_block_hidden();
                        appoint_count.val(0);
                        appoint_sum_block.removeAttr('hidden');
                    }else if(appoint_condition_val == 2){
                        appoint_condition_block_hidden();
                        appoint_sum.val(0);
                        appoint_count_block.removeAttr('hidden');
                    }else{
                        appoint_sum_block.removeAttr('hidden');
                        appoint_count_block.removeAttr('hidden');
                    }
                }
            })
            var appoint_product_btn = $('#appoint-product-btn');
            appoint_product_btn.click(function(){
                var appoint_form_data = appoint_form.serialize();
                var data = JSON.stringify(appoint_form_data);
                var appoint_product_condition = $('<button class="btn">ttttttt</button>');
                product_condition_block.append(appoint_product_condition);
            })


            //进行验证发送
            var name = $('input[name=name]');
            var btn = $('#btn');
            var form =  $('#form');
            btn.click(function(){

                var task_type_val = $('#task-type option:selected').val();
                //进行任务名称验证
                var name_val = name.val();
                if(name_val == ''){
                    name.parent().addClass("has-error");
                    alert('请输入任务名称');
                    return false;
                }


                //进行各条件验证
                var condition_type_val = $('#condition-type option:selected').val();
                var condition_pattern = /^[0-9]+$/
                var sum_val = $('#sum').val();
                alert(condition_pattern.test(sum_val));
                var count_val = $('#count').val();
                if(condition_type_val == 1){
                    if(!condition_pattern.test(sum_val)){
                        alert('金额格式错误');
                        return false;
                    }
                }else if(condition_type_val == 2)
                {
                    if(!condition_pattern.test(count_val)){
                        alert('数量格式错误');
                        return false;
                    }
                }else {
                    if(!condition_pattern.test(count_val)||!condition_pattern.test(sum_val))
                    {
                        alert('条件格式错误');
                        return false;
                    }
                }






                return false;
                if(task_type_val != 4){
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

