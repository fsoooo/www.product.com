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
                            <li ><span>运营管理</span></li>
                            <li ><span>工作流管理</span></li>
                            <li class="active"><span>工作流</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li  class="active"><a href="{{ url('/backend/flow/index') }}">工作流列表</a></li>
                                    <li><a href="{{ url('/backend/flow/add_flow') }}">新增</a></li>
                                    {{--<li class="active"><a href="#">详细信息</a></li>--}}
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>基本信息</p></h3>
                                        <div class="panel-group accordion" id="account">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion2" href="#account-message">
                                                            工作流基本信息
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="account-message" class="panel-collapse ">
                                                    <table id="basic" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr>
                                                            <td width="35%">工作流名称</td>
                                                            <td id="task_name" index="{{ $flow_detail->id }}">
                                                                {{ $flow_detail->flow_name }}
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <h3><p>工作流节点</p></h3>
                                        <div class="panel-group accordion" id="bill">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        <a class="accordion-toggle collapsed md-trigger "  index="0" style="cursor: pointer;"  data-modal="modal-8">
                                                            节点
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="bill-block" class="panel-collapse ">
                                                    <div class="panel-body" style="padding-bottom: 0">
                                                        <table id="product" class="table table-hover" style="clear: both">
                                                            <thead>
                                                            <th>节点名称</th>
                                                            <th>节点描述</th>
                                                            <th>操作</th>
                                                            </thead>
                                                            <tbody>
                                                                @if( $node_count==0 )
                                                                    <tr>
                                                                        <td colspan="4" style="text-align: center;">
                                                                            暂无节点
                                                                        </td>
                                                                    </tr>
                                                                @else
                                                                    @foreach($flow_detail->node as $value)
                                                                        <tr>
                                                                            <td>{{ $value->node_name }}</td>
                                                                            <td>{{ $value->describe }}</td>
                                                                            <td><a href="{{ url('/backend/flow/node_detail/'.$value->id) }}">查看详情</a></td>
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

