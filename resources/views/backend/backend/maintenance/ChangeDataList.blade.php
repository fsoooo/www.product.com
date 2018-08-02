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
                            <li ><span>保全管理</span></li>
                            <li class="active"><span>投保人信息变更</span></li>

                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    @if($type == 'user')
                                        <li class="active"><a href="#">投保人信息变更</a></li>
                                        {{--<li><a href="{{ url('/backend/maintenance/change_insurance/company') }}">团险信息变更</a></li>--}}
                                    @elseif($type == 'company')
                                        <li><a href="{{ url('/backend/maintenance/change_insurance/user') }}">投保人信息变更</a></li>
                                        <li class="active"><a href="{{ url('/backend/maintenance/change_insurance/company') }}">团险信息变更</a></li>
                                    @endif
                                </ul>
                    <div class="col-lg-12">
                        @include('backend.layout.alert_info')
                        <div class="main-box clearfix">
                            <header class="main-box-header clearfix">
                                <h2 class="pull-left">投保人信息变更</h2>
                            </header>
                            <div class="main-box-body clearfix">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th><span>保单编号</span></th>
                                            <th><span>理赔类型</span></th>
                                            <th><span>成交方式</span></th>
                                            <th><span>开始时间</span></th>
                                            <th><span>结束时间</span></th>
                                            <th><span>查看更多</span></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @if($count == 0)
                                                <td colspan="8">暂无修改记录</td>
                                            @else
                                                @foreach($list as $value)
                                                    <tr>
                                                        <td>{{ $value->maintenance_record_order->order_code }}</td>
                                                        <td>
                                                            @if($value->maintenance_record_order->claim_type == 1)
                                                                线上理赔
                                                            @elseif($value->maintenance_record_order->claim_type == 2)
                                                                线下理赔
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($value->maintenance_record_order->deal_type == 0)
                                                                线上成交
                                                            @elseif($value->maintenance_record_order->deal_type == 1)
                                                                线下成交
                                                            @endif
                                                        </td>
                                                        <td>{{ $value->maintenance_record_order->start_time }}</td>
                                                        <td>{{ $value->maintenance_record_order->end_time }}</td>
                                                        <td><a href="{{ url('/backend/maintenance/change_data_list/'.$value->order_id) }}">更多修改内容</a></td>
                                                    </tr>
                                                @endforeach
                                            @endif

                                        </tbody>
                                    </table>
                                </div>
                                {{--@if( $count != 0 )--}}
                                    {{--{{ $claim->links() }}--}}
                                {{--@endif--}}
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

