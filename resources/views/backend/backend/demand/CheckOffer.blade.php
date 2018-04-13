@extends('backend.layout.base')
<link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
@section('content')
    <style>
        tr,td{
            text-align: center;
        }
    </style>
    <meta name="_token" content="{{ csrf_token() }}"/>
    <div id="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">
                            <li><a href="{{ url('/backend') }}">主页</a></li>
                            <li ><span>运营管理</span></li>
                            <li class="active"><span>需求管理</span></li>
                        </ol>
                    </div>
                </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li><a href="{{ url('/backend/demand/index/user') }}">客户发布</a></li>
                                    <li><a href="{{ url('/backend/demand/index/agent') }}">代理人发布</a></li>
                                    <li class="active"><a href="#">报价详情</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>产品报价</p></h3>
                                        @foreach($offer_list as $value)
                                            <div class="panel-group accordion" id="account">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">
                                                        <h4 class="panel-title">
                                                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion2" href="#account-message">
                                                                报价信息
                                                            </a>
                                                        </h4>
                                                    </div>
                                                    <div id="account-message" class="panel-collapse ">
                                                        <table id="basic" class="table table-hover" style="clear: both">
                                                            <tbody>
                                                            @foreach($value->product_detail as $values)
                                                                <tr>
                                                                    <td>产品名称</td>
                                                                    <td>{{ $values->product_name }}</td>
                                                                </tr>
                                                            @endforeach
                                                            <tr>
                                                                <td>报价</td>
                                                                <td>{{ $value->offer }}</td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
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

