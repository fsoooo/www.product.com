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
                                    @if($type == 'all')
                                        <li class="active"><a href="{{ url('backend/warranty/get_warranty/all') }}">保单列表</a></li>
                                        <li><a href="{{ url('/backend/warranty/get_warranty/online') }}">线上成交</a></li>
                                        <li><a href="{{ url('/backend/warranty/get_warranty/offline') }}">线下成交</a></li>
                                    @elseif($type == 'offline')
                                        <li><a href="{{ url('backend/warranty/get_warranty/all') }}">保单列表</a></li>
                                        <li><a href="{{ url('/backend/warranty/get_warranty/online') }}">线上成交</a></li>
                                        <li class="active"><a href="{{ url('/backend/warranty/get_warranty/offline') }}">线下成交</a></li>
                                    @elseif($type == 'online')
                                        <li><a href="{{ url('backend/warranty/get_warranty/all') }}">保单列表</a></li>
                                        <li class="active"><a href="{{ url('/backend/warranty/get_warranty/online') }}">线上成交</a></li>
                                        <li><a href="{{ url('/backend/warranty/get_warranty/offline') }}">线下成交</a></li>
                                    @endif
                                </ul>
            <div class="col-lg-12">
                @include('backend.layout.alert_info')
                <div class="main-box clearfix">
                    <header class="main-box-header clearfix">
                    </header>
                    <div class="main-box-body clearfix">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th><span>订单编号</span></th>
                                    <th><span>保险产品</span></th>
                                    <th><span>保单类型</span></th>
                                    <th><span>查看详情</span></th>
                                </tr>
                                </thead>
                                <tbody>
                                @if( $count == 0 )
                                    <tr>
                                        <td colspan="8" style="text-align: center;">暂无保单</td>
                                    </tr>
                                @else
                                    @foreach($warranty_list as $value)
                                        <tr>
                                            <td>{{ $value->warranty_rule_order->order_code }}</td>
                                            <td>{{ $value->warranty_product->product_name }}</td>
                                            <td>
                                                @if($value->type == 0)
                                                    个险保单
                                                @elseif($value->type == 1)
                                                    团险保单
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ url('/backend/warranty/get_warranty_detail/'.$value->union_order_code) }}">查看详情</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                        @if( $count != 0 )
                            {{ $warranty_list->links() }}
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

