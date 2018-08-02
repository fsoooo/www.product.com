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
                            <li ><span>售后管理</span></li>
                            <li class="active"><span>理赔管理</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="{{ url('/backend/claim/index') }}">理赔列表</a></li>
                                    {{--<li><a href="{{ url('/backend/claim/get_claim/no_deal') }}">未处理</a></li>--}}
                                    {{--<li><a href="{{ url('/backend/claim/get_claim/deal') }}">已处理</a></li>--}}
                                </ul>
            <div class="col-lg-12">
                @include('backend.layout.alert_info')
                <div class="main-box clearfix">
                    <header class="main-box-header clearfix">
                        <h2 class="pull-left">理赔列表</h2>

                    </header>
                    <div class="main-box-body clearfix">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th><span>订单编号</span></th>
                                    <th><span>联合订单号</span></th>
                                    <th><span>手机号码</span></th>
                                    <th><span>收款账户类型</span></th>
                                    <th><span>收款账户</span></th>
                                    <th hidden><span>银行名称</span></th>
                                    <th><span>客户名称</span></th>
                                    <th><span>状态<an></th>
                                    <th><span>操作</span></th>

                                </tr>
                                </thead>
                                <tbody>
                                @if( $count == 0 )
                                    <tr>
                                        <td colspan="8" style="text-align: center;">暂无理赔申请</td>
                                    </tr>
                                @else
                                    @foreach ($claim as $value )
                                        <tr>
                                            <td>
                                                {{ $value->order_code }}
                                            </td>
                                            <td>
                                                {{ $value->union_order_code }}
                                            </td>
                                            <td>
                                                {{ $value->phone }}
                                            </td>
                                            @if($value->account_type=='1')
                                                <td>
                                                    银行卡账户
                                                </td>
                                            @elseif($value->account_type=='2')
                                                <td>
                                                    支付宝账户
                                                </td>
                                            @elseif($value->account_type=='3')
                                                <td>
                                                    微信账户
                                                </td>
                                            @endif
                                            <td>
                                                @if($value->account_type=='1')
                                                    {{$value->bank_name}}
                                                    <br/>
                                                @endif
                                                {{ $value->account }}
                                            </td>
                                            <td>
                                                @if(key_exists($value->api_account,$users))
                                                {{ $users[$value->api_account]}}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                    @if(key_exists($value->status,config('status')))
                                                    <span class="label label-success">{{config('status')[$value->status]}}</span>
                                                    @endif
                                            </td>
                                            <td>
                                                <a href="/backend/claim/get_detail/{{ $value->id }}">查看详情</a>
                                            </td>
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
@stop

