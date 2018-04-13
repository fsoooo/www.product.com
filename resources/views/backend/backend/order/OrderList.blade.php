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
                            <li><span>售后管理</span></li>
                            <li class="active"><span>查看订单</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    @if($type == 'all')
                                        <li class="active"><a href="{{ url('backend/order/get_order/all') }}">订单列表</a></li>
                                        <li><a href="{{ url('/backend/order/get_order/online') }}">线上成交</a></li>
                                        <li><a href="{{ url('/backend/order/get_order/offline') }}">线下成交</a></li>
                                    @elseif($type == 'offline')
                                        <li><a href="{{ url('backend/order/get_order/all') }}">订单列表</a></li>
                                        <li><a href="{{ url('/backend/order/get_order/online') }}">线上成交</a></li>
                                        <li class="active"><a href="{{ url('/backend/order/get_order/offline') }}">线下成交</a></li>
                                    @elseif($type == 'online')
                                        <li><a href="{{ url('backend/order/get_order/all') }}">订单列表</a></li>
                                        <li class="active"><a href="{{ url('/backend/order/get_order/online') }}">线上成交</a></li>
                                        <li><a href="{{ url('/backend/order/get_order/offline') }}">线下成交</a></li>
                                    @endif
                                </ul>
            <div class="col-lg-12">
                @include('backend.layout.alert_info')
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
                                    <th><span>所属公司</span></th>
                                    <th><span>查看详情</span></th>
                                </tr>
                                </thead>
                                <tbody>
                                @if( $count == 0 )
                                    <tr>
                                        <td colspan="8" style="text-align: center;">暂无订单</td>
                                    </tr>
                                @else
                                    @foreach($order_list as $value)
                                        <tr>
                                            <td>{{ $value->order_code }}</td>
                                            <td>{{ $value->product->product_name }}</td>
                                            <td>{{ $value->product->company_name }}</td>
                                            <td>
                                                <a href="{{ url('/backend/order/get_order_detail/'.$value->id) }}">查看详情</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                        @if( $count != 0 )
                            {{ $order_list->links() }}
                        @endif
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
@stop

