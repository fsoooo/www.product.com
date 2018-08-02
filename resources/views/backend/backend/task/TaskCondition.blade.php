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
                            <li class="active"><span>修改条件</span></li>

                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="{{ url('/backend/task/add_task') }}">修改条件</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>任务列表</p></h3>
                                        @include('backend.layout.alert_info')
                                        <div class="panel-group accordion" id="operation">
                                            <div class="panel panel-default">
                                                <form method="post" action="{{ url('/backend/task/edit_condition_submit') }}" id="form" enctype="multipart/form-data">
                                                    {{ csrf_field() }}
                                                    <input type="text" name="task_id" value="{{$detail->task_id}}" hidden>
                                                    <input type="text" name="condition_id" value="{{ $detail->id }}" hidden>
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">选择产品</label>
                                                        <select name="product_id" id="product_id" class="form-control">
                                                            <option value="0">选择产品</option>
                                                            @foreach($product_list as $value)
                                                                @if($value->id == $detail->id)
                                                                    <option value="{{ $value->id }}" selected>{{ $value->product_name }}</option>
                                                                @else
                                                                    <option value="{{ $value->id }}">{{ $value->product_name }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">选择区域</label>
                                                        <select name="area_id" id="area_id" class="form-control">
                                                            <option value="0">无限制</option>
                                                            <option value="1">北京</option>
                                                            <option value="2">上海</option>
                                                            <option value="3">杭州</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="exampleInputPassword1">选择条件类型</label>
                                                        <select name="task_condition_type" id="appoint-condition" class="form-control">
                                                            <option value="0">请选择条件</option>
                                                            @if($detail->task_condition_type == 1)
                                                                <option value="1" selected>不计算折标率</option>
                                                                <option value="2">计算折标率</option>
                                                            @else
                                                                <option value="1">不计算折标率</option>
                                                                <option value="2" selected>计算折标率</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <div class="form-group" id="appoint-sum-block">
                                                        <label for="exampleInputPassword1">总金额要求</label>
                                                        <input type="text" name="sum" value="{{ $detail->sum/100 }}" id='appoint-sum' placeholder="请输入金额要求，单位元" class="form-control">
                                                    </div>
                                                </form>
                                            </div>
                                            <button id="btn" class="btn btn-success">修改</button>
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
//            var sum = $('#sum');
//            var count = $('#count');
//            var sum_block = $('#sum-block');
//            var count_block = $('#count-block');
//            function reset_condition()
//            {
//                sum.val('');
//                count.val('');
//            }
//            function condition_block_hidden()
//            {
//                sum_block.attr('hidden','');
//                count_block.attr('hidden','');
//            }
            //设置条件
//            var condition = $('#condition');
//            condition.change(function(){
//                var condition_val = $('#condition option:selected').val();
//                reset_condition();
//                if(condition_val == 0){
//                    condition_block_hidden();
//                }else {
//                    if(condition_val == 1){
//                        //金额满足
//                        condition_block_hidden();
//                        count.val(0);
//                        sum_block.removeAttr('hidden');
//                    }else if(condition_val == 2){
//                        condition_block_hidden();
//                        sum.val(0);
//                        count_block.removeAttr('hidden');
//                    }else{
//                        sum_block.removeAttr('hidden');
//                        count_block.removeAttr('hidden');
//                    }
//                }
//            })
//
//            //特定产品条件
//            var product_condition = $('#product-condition');
//            var product_condition_block = $('#product-condition-block');
//            var appoint_form = $('#appoint-form');
//            var appoint_condition = $('#appoint-condition');
//            var appoint_sum = $('#appoint-sum');
//            var appoint_count = $('#appoint-count');
//            var appoint_sum_block = $('#appoint-sum-block');
//            var appoint_count_block = $('#appoint-count-block');
//            function reset_appoint_condition(){
//                appoint_sum.val('');
//                appoint_count.val('');
//            }
//            function appoint_condition_block_hidden(){
//                appoint_sum_block.attr('hidden','');
//                appoint_count_block.attr('hidden','');
//            }
//            appoint_condition.change(function(){
//                var appoint_condition_val = $('#appoint-condition option:selected').val();
//                reset_appoint_condition();
//                if(appoint_condition_val == 0){
//                    appoint_condition_block_hidden();
//                }else {
//                    if(appoint_condition_val == 1){
//                        //金额满足
//                        appoint_condition_block_hidden();
//                        appoint_count.val(0);
//                        appoint_sum_block.removeAttr('hidden');
//                    }else if(appoint_condition_val == 2){
//                        appoint_condition_block_hidden();
//                        appoint_sum.val(0);
//                        appoint_count_block.removeAttr('hidden');
//                    }else{
//                        appoint_sum_block.removeAttr('hidden');
//                        appoint_count_block.removeAttr('hidden');
//                    }
//                }
//            })
//            var appoint_product_btn = $('#appoint-product-btn');
//            appoint_product_btn.click(function(){
//                var appoint_form_data = appoint_form.serialize();
//                var data = JSON.stringify(appoint_form_data);
//                var appoint_product_condition = $('<button class="btn">ttttttt</button>');
//                product_condition_block.append(appoint_product_condition);
//            })
//

            //进行验证发送
            var title = $('input[name=title]');
            var content = $('#content-text');
            var btn = $('#btn');
            var form =  $('#form');
            btn.click(function(){
                form.submit();
                var title_val = title.val();
                var content_val = content.val();
                if(title_val == ''){
                    title.parent().addClass("has-error");
                    alert('请输入标题');
                    return false;
                }else{
                    title.parent().removeClass("has-error");
                    if(content_val == ''){
                        content.parent().addClass("has-error");
                        alert('请输入内容');
                        return false;
                    }else {
                        content.parent().addClass("has-error");
                    }
                }
                var data = form.serialize();
                form.submit();
            })


        })

    </script>
@stop

