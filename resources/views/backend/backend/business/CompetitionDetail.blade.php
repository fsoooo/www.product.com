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
                            <li><span><a href="{{ url('/backend/business/competition') }}">竞赛方案管理</a></span></li>
                            <li class="active"><span>竞赛方案详情</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a>竞赛方案详情</a></li>
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>基本信息</p></h3>
                                        <div class="panel-group accordion" id="account">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        {{--<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion2" href="#account-message">--}}
                                                            方案基本信息
                                                        {{--</a>--}}
                                                    </h4>
                                                </div>
                                                <div id="account-message" class="panel-collapse ">
                                                    <table id="basic" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr>
                                                            <td width="35%">方案名</td>
                                                            <td>
                                                                {{ $detail->name }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>产品限定 </td>
                                                            <td>
                                                                @if($detail->competition_product)
                                                                    {{ $detail->competition_product->product_name }}
                                                                @else
                                                                    无限定产品
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>完成条件</td>
                                                            <td>
                                                                @if($detail->condition_type == 1)
                                                                    金额满足条件
                                                                @elseif($detail->condition_type == 2)
                                                                    数量满足条件
                                                                @elseif($detail->condition_type == 3)
                                                                    金额和数量满足条件
                                                                @elseif($detail->condition_type == 4)
                                                                    金额或数量满足条件
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>方案开始时间</td>
                                                            <td>
                                                            {{ $detail->start_time }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>方案结束时间</td>
                                                            <td>{{ $detail->end_time }}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <h3><p>方案要求及奖励</p></h3>
                                        <div class="panel-group accordion" id="bill">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        @if($detail->end_time<date('Y-m-d',time()))
                                                        {{--<a class="accordion-toggle collapsed md-trigger "  index="0" style="cursor: pointer;"  data-modal="modal-8">--}}
                                                            必须条件
                                                        {{--</a>--}}
                                                        @else
                                                            <a class="accordion-toggle collapsed md-trigger "  index="0" style="cursor: pointer;"  data-modal="modal-8">
                                                                必须条件
                                                            </a>
                                                        @endif
                                                    </h4>
                                                </div>
                                                <div id="bill-block" class="panel-collapse ">
                                                    <div class="panel-body" style="padding-bottom: 0">
                                                        <table id="product" class="table table-hover" style="clear: both">
                                                            <thead>
                                                            <th>产品限定</th>
                                                            <th>条件类型</th>
                                                            <th>金额范围</th>
                                                            <th>数量范围</th>
                                                            <th>奖励类型</th>
                                                            {{--<th>操作</th>--}}
                                                            </thead>
                                                            <tbody>
                                                            @if(!$detail->competition_condition)
                                                                <tr>
                                                                    <td colspan="7">
                                                                        暂无条件
                                                                    </td>
                                                                </tr>
                                                            @else
                                                                @foreach($detail->competition_condition as $value)
                                                                    <tr>
                                                                        <td>
                                                                            @if($value->product_id == 0)
                                                                                无限制
                                                                            @else
                                                                                {{ $value->competition_product->product_name }}
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if($value->condition_type == 1)
                                                                                金额满足条件
                                                                            @elseif($value->condition_type == 2)
                                                                                数量满足条件
                                                                            @elseif($value->condition_type == 3)
                                                                                金额和数量都满足条件
                                                                            @elseif($value->condition_type == 4)
                                                                                金额或数量满足条件
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if(!$value->min_sum&&!$value->max_sum)
                                                                                无要求
                                                                            @elseif(!$value->max_sum)
                                                                                {{ $value->min_sum/100 }}元以上
                                                                            @else
                                                                                {{ $value->min_sum/100 }}元 - {{$value->max_sum/100}}元
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if(!$value->min_count&&!$value->max_count)
                                                                                无要求
                                                                            @elseif(!$value->max_count)
                                                                                {{ $value->min_count }}单以上
                                                                            @else
                                                                                {{ $value->min_count }}单 - {{$value->max_count}}单
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                           @if($value->award_type == 1)
                                                                               比率奖励
                                                                           @elseif($value->award_type == 2)
                                                                                固定金额奖励
                                                                           @elseif($value->award_type == 3)
                                                                                比率加金额奖励
                                                                           @endif
                                                                        </td>
                                                                        {{--<td>--}}

                                                                            {{--<a href="{{ url('/backend/task/edit_condition/'.$value->id) }}">修改</a>--}}
                                                                        {{--</td>--}}
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
                <form method="post" action="{{ url('/backend/business/add_condition_submit') }}" id="product-condition-form" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="text" name="competition_id" value="{{ $detail->id }}" hidden>
                    {{--<div class="form-group">--}}
                        {{--<label for="exampleInputEmail1">选择产品<span class="red">*</span></label>--}}
                        {{--<select name="product_id" id="product_id" class="form-control">--}}
                            {{--<option value="0">选择产品</option>--}}
                            {{--@foreach($product_list as $value)--}}
                                {{--<option value="{{ $value->id }}">{{ $value->product_name }}</option>--}}
                            {{--@endforeach--}}
                        {{--</select>--}}
                    {{--</div>--}}
                    {{--<div class="form-group">--}}
                        {{--<label for="exampleInputEmail1">选择区域<span class="red">*</span></label>--}}
                        {{--<select name="area_id" id="area_id" class="form-control">--}}
                            {{--<option value="0">无限制</option>--}}
                            {{--<option value="1">北京</option>--}}
                            {{--<option value="2">上海</option>--}}
                            {{--<option value="3">杭州</option>--}}
                        {{--</select>--}}
                    {{--</div>--}}
                    {{--<div class="form-group">--}}
                        {{--<label for="">设置条件</label>--}}
                        {{--<select name="condition_type" id="condition" class="form-control">--}}
                            {{--<option value="0">请选择方案完成条件</option>--}}
                            {{--<option value="1">金额总数满足条件</option>--}}
                            {{--<option value="2">订单总数满足条件</option>--}}
                            {{--<option value="3">金额和订单满足条件</option>--}}
                            {{--<option value="4">金额或订单满足条件</option>--}}
                        {{--</select>--}}
                    {{--</div>--}}
                    <div class="form-group" id="sum-block" >
                        <label for="">金额最低要求</label>
                        <input type="text" class="form-control" name="min_sum" placeholder="请输入金额下限，单位元，不输则表示无限制">
                        <label for="">最高金额</label>
                        <input type="text" class="form-control" id="sum" name="max_sum" placeholder="请输入金额,单位元" value="">
                    </div>
                    <div class="form-group" id="count-block" >
                        <label for="">数量要求下限</label>
                        <input type="text" class="form-control" name="min_count" placeholder="请输入数量下限，不输则表示无限制" >
                        <label for="">数量要求上限</label>
                        <input type="text" class="form-control" id="count" name="max_count" placeholder="请输入数量，不输则表示无限制" value="">
                    </div>
                    <div class="form-group">
                        <label for="">选择奖励类型</label>
                        <select name="award_type" id="award" class="form-control">
                            <option value="0">请选择奖励类型</option>
                            <option value="1">修改比率</option>
                            <option value="2">完成奖金</option>
                            <option value="3">修改比率加完成奖金</option>
                        </select>
                    </div>
                    <div class="form-group" id="rate-block" hidden=''>
                        <label for="">修改比率</label>
                        <input type="text" class="form-control" id="rate" name="rate" placeholder="请输入奖励比率" value="">
                    </div>
                    <div class="form-group" id="reward-block" hidden="">
                        <label for="">固定金额</label>
                        <input type="text" class="form-control" id="reward" name="reward" placeholder="请输入奖励金额,单位元" value="">
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
        //大条件变化
        var appoint_product = $('#appoint-product');
        var appoint_condition = $('#appoint-condition');
        var product_block = $('#product-block');
        appoint_product.click(function(){
            //指定产品
            product_block.removeAttr('hidden');
        })
        appoint_condition.click(function () {
            //其他条件
            product_block.attr('hidden','');
            $('#product-id option:selected').val(0);
        })


        //
        //            //选择渠道
        //            var ditch_id = $('#ditch-id');
        //            ditch_id.each(function (i,item) {
        //                $(item).click(function(){
        //                    alert($(item).val());
        //                })
        //            })






        //条件
        var sum = $('#sum');
        var count = $('#count');
        var sum_block = $('#sum-block');
        var count_block = $('#count-block');
        function reset_condition()
        {
            sum.val('');
            count.val('');
        }
        function condition_block_hidden()
        {
            sum_block.attr('hidden','');
            count_block.attr('hidden','');
        }
        //设置条件
        var condition = $('#condition');
        condition.change(function(){
            var condition_val = $('#condition option:selected').val();
            reset_condition();
            if(condition_val == 0){
                condition_block_hidden();
            }else {
                if(condition_val == 1){
                    //金额满足
                    condition_block_hidden();
                    count.val(0);
                    sum_block.removeAttr('hidden');
                }else if(condition_val == 2){
                    condition_block_hidden();
                    sum.val(0);
                    count_block.removeAttr('hidden');
                }else{
                    sum_block.removeAttr('hidden');
                    count_block.removeAttr('hidden');
                }
            }
        })



        //奖励
        var rate_block = $('#rate-block');
        var reward_block = $('#reward-block');
        var rate = $('#rate');
        var reward = $('#reward');
        function reset_award(){
            rate.val('');
            reward.val('');
        }
        function award_block_hidden(){
            rate_block.attr('hidden','');
            reward_block.attr('hidden','');
        }
        //设置奖励
        var award = $('#award');
        award.change(function(){
            var award_val = $('#award option:selected').val();
            reset_award();
            if(award_val == 0){
                award_block_hidden();
            }else {
                if(award_val == 1){
                    award_block_hidden();
                    reward.val(0);
                    rate_block.removeAttr('hidden');
                }else if(award_val == 2){
                    award_block_hidden();
                    rate.val(0);
                    reward_block.removeAttr('hidden');
                }else{
                    rate_block.removeAttr('hidden');
                    reward_block.removeAttr('hidden');
                }
            }
        })



        //提交进行表单验证
        var name = $('#competition-name');
        var form = $('#form');
        var btn = $('#btn');
        btn.click(function(){
            name_val = name.val();
            if(!name_val){
                alert('请输入方案名称');//方案名称效验
                return false;
            }else {
//
                form.submit();
            }
        })






    </script>
@stop

