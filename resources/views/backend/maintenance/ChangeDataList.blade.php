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
                            <li class="active"><span>个险变更</span></li>

                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    @if($type == 'user')
                                        <li class="active"><a href="#">个险变更</a></li>
                                        {{--<li><a href="{{ url('/backend/maintenance/change_insurance/company') }}">团险信息变更</a></li>--}}
                                    @elseif($type == 'company')
                                        <li><a href="{{ url('/backend/maintenance/change_insurance/user') }}">个险变更</a></li>
                                        <li class="active"><a href="{{ url('/backend/maintenance/change_insurance/company') }}">团险变更</a></li>
                                    @endif
                                </ul>
                    <div class="col-lg-12">
                        @include('backend.layout.alert_info')
                        <div class="main-box clearfix">
                            <header class="main-box-header clearfix">
                                <h2 class="pull-left">个险变更</h2>
                            </header>
                            <div class="main-box-body clearfix">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th><span>联合订单编号</span></th>
                                            <th><span>保全状态</span></th>
                                            <th><span>发起时间</span></th>
                                            <th><span>查看详情</span></th>
                                            <th><span>操作</span></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @if($count == 0)
                                                <td colspan="8">暂无修改记录</td>
                                            @else
                                                @foreach($list as $value)
                                                        <tr>
                                                            <td>{{$value->union_order_code}}</td>
                                                            <td>
                                                                    发起成功，等待审核
                                                            </td>
                                                            <td>{{$value->created_at}}</td>
                                                            <td><a href="{{ url('/backend/maintenance/change_data_detail/'.$value->union_order_code)}}" target="_blank">更多修改内容</a></td>
                                                            <td><a href="{{ url('/backend/maintenance/change_submit/'.$value->union_order_code)}}" target="_blank">提交保全</a></td>
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

