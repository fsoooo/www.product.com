@extends('backend.layout.base')
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
                            <li><span><a href="{{ url('/backend/business/competition') }}">竞赛方案管理</a></span></li>
                            <li class="active"><span><a href="{{ url('/backend/business/create_competition') }}">生成竞赛方案</a></span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li><a href="{{ url('/backend/business/competition') }}">竞赛方案列表</a></li>
                                    <li class="active"><a href="{{ url('/backend/business/create_competition') }}">生成竞赛方案</a></li>
                                    <li><a href="{{ url('/backend/business/get_expire') }}">已过期方案</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <header class="main-box-header clearfix">
                                            <h2 class="pull-left">添加新的竞赛方案</h2>
                                            <div class="filter-block pull-right">
                                                <span id="appoint-condition" class="btn btn-primary pull-right">
                                                    <i class="fa fa-pencil fa-lg"></i> 其他条件
                                                </span>
                                                <span id="appoint-product" class="btn btn-primary pull-right">
                                                    <i class="fa fa-pencil fa-lg"></i> 指定产品
                                                </span>
                                            </div>
                                        </header>
                                        @include('backend.layout.alert_info')
                                        <div class="panel-group accordion" id="operation">
                                            <div class="panel panel-default">
                                                <form id="form" method="post" action="{{ url('/backend/business/add_competition_submit') }}">
                                                {{--<form>--}}
                                                    {{ csrf_field() }}
                                                    <div class="form-group">
                                                        <label>方案名称</label>
                                                        <input type="text" class="form-control" id="competition-name" name="competition_name" placeholder="请输入竞赛方案" value="">
                                                    </div>
                                                    <div class="form-group" id="product-block">
                                                        <label for="">选择产品</label>
                                                        <select name="product_id" id="product-id" class="form-control">
                                                            <option value="0">请选择商品</option>
                                                            @foreach($product_list as $value)
                                                                <option value="{{ $value->id }}">{{ $value->product_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="">设置条件</label>
                                                        <select name="condition_type" id="condition" class="form-control">
                                                            <option value="0">请选择方案完成条件</option>
                                                            <option value="1">金额总数满足条件</option>
                                                            <option value="2">订单总数满足条件</option>
                                                            <option value="3">金额和订单满足条件</option>
                                                            <option value="4">金额或订单满足条件</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="">开始时间</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <span style="color:green">如果开始时间和结束时间小于当前时间，将会被自动替换成当前时间</span>
                                                        <input type="date"  id="" name="start_time" placeholder="请选择开始时间" class="form-control">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="">结束时间</label>
                                                        <input type="date"  id="" name="end_time"  placeholder="请选择结束时间" class="form-control">
                                                    </div>
                                                    <div class="stairs">
                                                        <label for=""></label>
                                                    </div>
                                                </form>
                                                <button type="button" id="btn" class="btn btn-success">生成方案</button>
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
    <script src="/js/jquery-3.1.1.min.js"></script>
    <script>
        $(function(){
            //大条件变化
            var appoint_product = $('#appoint-product');
            var appoint_condition = $('#appoint-condition');
            var product_block = $('#product-block');
            var condition_type = $('input[name=condition_type]');
            appoint_product.click(function(){
                //指定产品
                product_block.removeAttr('hidden');
            })
            appoint_condition.click(function () {
                //其他条件
                product_block.attr('hidden','');
                $('#product-id option:selected').val(0);
            })




            //提交进行表单验证
            var name = $('#competition-name');
            var form = $('#form');
            var btn = $('#btn');
            var start_time = $('input[name=start_time]');
            var end_time = $('input[name=end_time]');
            btn.click(function(){
                var start_time_val = start_time.val();
                var end_time_val = end_time.val();
                var name_val = name.val();
                var condition_type_val = $('#condition option:selected').val();
                if(!name_val){
                    alert('请输入方案名称');//方案名称效验
                    name.parent().addClass('has-error');
                    return false;
                }else if(condition_type_val == 0){
                    condition_type.parent().addClass('has-error');
                    name.parent().removeClass('has-error');
                    alert('请选择完成条件');
                    return false;
                }else {
                    //时间检测,不能为空，开始时间大于当前时间，结束时间大于开始时间
                    var condition1 = end_time_val>start_time_val;
                    if(!start_time_val){
                        alert('开始时间不能为空');
                    }else if(!end_time_val){
                        alert('结束时间不能为空');
                    }else if(!condition1){
                        alert('开始时间不能大于结束时间');
                    }else {
                        form.submit();
                    }
                }
            })

        })

    </script>
@stop






