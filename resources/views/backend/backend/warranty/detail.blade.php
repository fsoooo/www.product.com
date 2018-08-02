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
                            <li class="active"><span>售后管理</span></li>
                            <li class="active"><span>查看保单</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li><a href="{{ url('backend/warranty/get_warranty/all') }}">保单列表</a></li>
                                    <li><a href="{{ url('/backend/warranty/get_warranty/online') }}">线上成交</a></li>
                                    <li><a href="{{ url('/backend/warranty/get_warranty/offline') }}">线下成交</a></li>
                                    {{--<li class="active"><a href="#">保单详情</a></li>--}}
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>保单详情</p></h3>
                                        @include('backend.layout.alert_info')
                                        <div class="panel-group accordion" id="operation">
                                            <div class="panel panel-default">
                                                <form id="warranty-form" action="{{ url('/backend/warranty/add_warranty_submit') }}" method="post">
                                                    {{ csrf_field() }}
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <tbody>

                                                        <tr>
                                                            <td width="25%">订单编号</td>
                                                            <td width="60%">
                                                                {{ $warranty_detail->warranty_rule_order->order_code }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="25%">投保单号</td>
                                                            <td width="60%">
                                                                {{ $warranty_detail->union_order_code }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="25%">保单编号</td>
                                                            <td width="60%">
                                                                {{ $warranty_detail->warranty->warranty_code }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>所属保险公司</td>
                                                            <td>
                                                                {{ $warranty_detail->warranty_product->company_name }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>产品</td>
                                                            <td>
                                                                {{ $warranty_detail->warranty_product->product_name }}
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>

                                            </div>
                                        </div>
                                        <h3><p>投保人信息</p></h3>
                                        <div class="panel-group accordion" id="operation">
                                            <div class="panel panel-default">
                                                <table id="user" class="table table-hover" style="clear: both">
                                                    <tbody>
                                                    <tr>
                                                        <td width="25%">姓名</td>
                                                        <td width="60%">
                                                            {{ $policy_detail->name }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>证件类型</td>
                                                        <td>
                                                            {{ $policy_detail->policy_card_type->name }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>证件号码</td>
                                                        <td>
                                                            {{ $policy_detail->code }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>手机号码</td>
                                                        <td>
                                                            {{ $policy_detail->phone }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>电子邮箱</td>
                                                        <td>
                                                            {{ $policy_detail->email }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>职业</td>
                                                        <td> {{ $policy_detail->policy_occupation->name }}</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div id="recognizee-block">
                                            <h3><p>被保人信息</p></h3>
                                            @foreach($recognizee_detail as $value)
                                                <div class="panel-group accordion" id="operation">
                                                <div class="panel panel-default">
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr>
                                                            <td width="25%">姓名</td>
                                                            <td width="60%">
                                                                {{ $value->name }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>与投保人关系</td>
                                                            <td>{{ $value->recognizee_relation->name  }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>证件类型</td>
                                                            <td>
                                                                {{ $value->recognizee_card_type->name }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>证件号码</td>
                                                            <td>
                                                                {{ $value->code }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>手机号码</td>
                                                            <td>
                                                                {{ $value->phone }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>电子邮箱</td>
                                                            <td>
                                                                {{ $value->email }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>职业</td>
                                                            <td> {{ $value->recognizee_occupation->name }}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                    </form>
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
                <footer id="footer-bar" class="row">
                    <p id="footer-copyright" class="col-xs-12">
                        &copy; 2014 <a href="http://www.adbee.sk/" target="_blank">Adbee digital</a>. Powered by Centaurus Theme.
                    </p>
                </footer>
            </div>
        </div>
    </div>
    <script src="/js/jquery-3.1.1.min.js"></script>
    <script>
        $(function(){
            var relation_type = $('#relation-type');
            var recognizee_block = $('#recognizee-block');

            //各个form表单xinxi
            var warranty_form = $('#warranty-form');

            var btn = $('#btn');


            relation_type.change(function(){
                var relation_type_val = $('#relation-type option:selected').html();
                if(relation_type_val == '本人'){
                    recognizee_block.attr('hidden','');
                }else{
                    recognizee_block.removeAttr('hidden');
                }
            })

            btn.click(function(){
                var warranty_form_val = warranty_form.serialize();
                warranty_form.submit();
            })


        })

    </script>
@stop

