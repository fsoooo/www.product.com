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
                            <li class="active"><span>工作流管理</span></li>
                        </ol>
                        <h1>工作流信息</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li><a href="{{ url('/backend/task/index') }}">任务列表</a></li>
                                    <li class="active"><a href="#">详细信息</a></li>
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>基本信息</p></h3>
                                        <div class="panel-group accordion" id="account">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion2" href="#account-message">
                                                            节点基本信息
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="account-message" class="panel-collapse ">
                                                    <table id="basic" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr>
                                                            <td style="width: 35%">节点名称</td>
                                                            <td>
                                                                {{ $node_detail->node_name }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>所属工作流</td>
                                                            <td>
                                                                {{ $node_detail->node_flow->flow_name }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>对应方法</td>
                                                            <td>
                                                                {{ $node_detail->node_route->route_name }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>节点描述</td>
                                                            <td>
                                                                {{ $node_detail->describe }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>节点状态</td>
                                                            <td>
                                                                {{ $node_detail->status }}
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <h3><p>行为规范</p></h3>
                                        <div class="panel-group accordion" id="bill">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        <a class="accordion-toggle collapsed md-trigger "   data-modal="modal-8" style="cursor: pointer;">
                                                            节点可行行为规范
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="bill-block" class="panel-collapse ">
                                                    <div class="panel-body" style="padding-bottom: 0">
                                                        <table id="product" class="table table-hover" style="clear: both">
                                                            <thead>
                                                                <th>行为规范名称</th>
                                                                <th>行为规范描述</th>
                                                                <th>行为规范状态</th>
                                                            </thead>
                                                            <tbody>
                                                                @if($possible_count == 0)
                                                                    <tr>
                                                                        <td colspan="5">
                                                                            暂无可行规范约束
                                                                        </td>
                                                                    </tr>
                                                                @else
                                                                    @foreach($possible as $value)
                                                                        <tr>
                                                                            <td></td>
                                                                            <td>{{ $value->describe }}</td>
                                                                            <td>{{ $value->status }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <h3><p>行为规范</p></h3>
                                        <div class="panel-group accordion" id="bill">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        <a class="accordion-toggle collapsed md-trigger "   data-modal="modal-8" style="cursor: pointer;">
                                                            禁止行为规范
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="bill-block" class="panel-collapse ">
                                                    <div class="panel-body" style="padding-bottom: 0">
                                                        <table id="product" class="table table-hover" style="clear: both">
                                                            <thead>
                                                            <th>行为规范名称</th>
                                                            <th>行为规范描述</th>
                                                            <th>返回错误信息</th>
                                                            <th>行为规范状态</th>
                                                            </thead>
                                                            <tbody>
                                                            @if($possible_count == 0)
                                                                <tr>
                                                                    <td colspan="5">
                                                                        暂无禁止规范约束
                                                                    </td>
                                                                </tr>
                                                            @else
                                                                @foreach($possible as $value)
                                                                    <tr>
                                                                        <td></td>
                                                                        <td>{{ $value->describe }}</td>
                                                                        <td>{{ $value->return_message }}</td>
                                                                        <td>{{ $value->status }}</td>
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
            <div class="modal-body">
                <form method="post" action="{{ url('/backend/task/add_condition') }}" id="product-condition-form" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    {{--<input type="text" name='task_id' value="{{ $task_detail->id }}" hidden>--}}
                    <input type="text" name="condition_id" value="0" hidden>
                    <div class="form-group">
                        <label for="exampleInputEmail1">选择产品<span class="red">*</span></label>
                        <select name="product_id" id="product_id" class="form-control">
                            <option value="0">选择产品</option>
                            <option value="1">产品1</option>
                            <option value="2">产品2</option>
                            <option value="3">产品3</option>
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
                        <label for="exampleInputPassword1">选择完成条件<span class="red">*</span></label>
                        <select name="task_condition_type" id="appoint-condition" class="form-control">
                            <option value="0">请选择条件</option>
                            <option value="1">金额达标</option>
                            <option value="2">数量达标</option>
                            <option value="3">数量或金额达标</option>
                            <option value="4">数量和金额达标</option>
                        </select>
                    </div>
                    <div class="form-group" id="appoint-sum-block" hidden>
                        <label for="exampleInputPassword1">金额达到要求<span class="red">*</span></label>
                        <input type="text" name="sum" id='appoint-sum' placeholder="请输入金额要求，单位元" class="form-control">
                    </div>
                    <div class="form-group" id="appoint-count-block" hidden>
                        <label for="exampleInputPassword1">数量要求<span class="red">*</span></label>
                        <input type="text" name="count" id="appoint-count" placeholder="请输入数量要求，单位单" class="form-control">
                    </div>

                </form>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" id='appoint-product-btn' class="btn btn-primary">确认保存</button>
            </div>
        </div>
    </div>
    <div class="md-overlay"></div>

    <script src="/js/jquery-3.1.1.min.js"></script>
    <script charset="utf-8" src="/r_backend/js/modernizr.custom.js"></script>
    <script charset="utf-8" src="/r_backend/js/classie.js"></script>
    <script charset="utf-8" src="/r_backend/js/modalEffects.js"></script>
    <script>
        //特定产品条件
        var product_condition = $('#product-condition');
        var product_condition_block = $('#product-condition-block');
        var product_condition_form = $('#product-condition-form');
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




    </script>
@stop

