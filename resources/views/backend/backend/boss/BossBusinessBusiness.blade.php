@extends('backend.layout.base')
@section('content')
    <style>
        th{
            text-align: center;
        }
        td{
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
                            <li class="active"><span>业务统计</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    {{--@if($type == 'all')--}}
                                        <li class="active"><a href="{{ url('/backend/boss/business/index') }}">已成交订单</a></li>
                                        {{--<li><a href="{{ url('/backend/boss/business/cust/person') }}">已成交保单</a></li>--}}
                                    {{--@elseif($type == 'person')--}}
                                        {{--<li><a href="{{ url('/backend/boss/business/cust/all') }}">客户池</a></li>--}}
                                        {{--<li class="active"><a href="{{ url('/backend/boss/business/cust/person') }}">个人客户</a></li>--}}
                                        {{--<li><a href="{{ url('/backend/boss/business/cust/company') }}">企业客户</a></li>--}}
                                    {{--@elseif($type == 'company')--}}
                                        {{--<li><a href="{{ url('/backend/boss/business/cust/all') }}">客户池</a></li>--}}
                                        {{--<li><a href="{{ url('/backend/boss/business/cust/person') }}">个人客户</a></li>--}}
                                        {{--<li class="active"><a href="{{ url('/backend/boss/business/cust/company') }}">企业客户</a></li>--}}
                                    {{--@endif--}}
                                </ul>
            <div class="col-lg-12">

                <div class="main-box clearfix">
                    <header class="main-box-header clearfix">
                        <h2 class="pull-left">订单列表</h2>
                    </header>
                    <div class="main-box-body clearfix">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th><span>订单编号</span></th>
                                    <th><span>产品名称</span></th>
                                    <th><span>投保人</span></th>
                                    <th><span>联系电话</span></th>
                                    <th><span>客户类型</span></th>
                                    <th><span>代理人</span></th>
                                </tr>
                                </thead>
                                <tbody>
                                    @if($count == 0)
                                        <tr>
                                            <td colspan="7">
                                                暂时没有客户
                                            </td>
                                        </tr>
                                    @else
                                        @foreach($order_list as $value)
                                            <tr>
                                                <td>{{ $value->order_code }}</td>
                                                <td>{{ $value->product->product_name }}</td>
                                                @if(isset($value->warranty_rule->policy->name))
                                                <td>{{ $value->warranty_rule->policy->name }}</td>
                                                @else
                                                <td>投保人信息不全</td>
                                                @endif
                                                @if(isset($value->warranty_rule->policy->phone))
                                                <td>{{ $value->warranty_rule->policy->phone }}</td>
                                                @else
                                                <td>投保人信息不全</td>
                                                @endif
                                                <td>
                                                    @if(!isset($value->order_user))
                                                        <a class="label label-primary" href="#" style="color: white">线下录入</a>
                                                    @elseif($value->order_user->type == 'user'&& isset($value->order_user->type))
                                                        <a class="label label-primary" href="#" style="color: white">个人客户</a>
                                                    @else
                                                        <a  class="label label-info" href="#" style="color: white">企业客户</a>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(isset($value->order_agent->user->real_name))
                                                    {{$value->order_agent->user->real_name}}
                                                    @else
                                                    平台卖出
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        <div>
                    </div>
                </div>
            </div>
        </div>




    </div>
@stop

