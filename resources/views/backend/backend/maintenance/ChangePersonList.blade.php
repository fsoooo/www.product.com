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
                            <li class="active"><span>团险人员变更</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">

                    <div class="col-lg-12">
                        <div class="tabs-wrapper tabs-no-header">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#">团险人员变更</a></li>
                            </ul>
                        @include('backend.layout.alert_info')
                        <div class="main-box">
                            <header class="main-box-header clearfix">
                                <h2 class="pull-left">团险人员变更</h2>
                            </header>
                            <div class="main-box-body clearfix">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th><span>订单编号</span></th>
                                            <th><span>保障生效时间</span></th>
                                            <th><span>保障结束时间</span></th>
                                            <th><span>订单状态</span></th>
                                            <th><span>修改内容</span></th>
                                            <th><span>修改时间</span></th>
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
                                                            <td>{{ $value->maintenance_record_order->start_time }}</td>
                                                            <td>{{ $value->maintenance_record_order->end_time }}</td>
                                                            <td>订单状态，暂留</td>
                                                            <td>{{ $value->change_type }}</td>
                                                            <td>{{ $value->created_at }}</td>
                                                            <td><a href="{{ url('/backend/maintenance/change_person_list/'.$value->order_id) }}">更多修改内容</a></td>
                                                        </tr>
                                                @endforeach
                                            @endif

                                        </tbody>
                                    </table>
                                </div>
                                @if( $count != 0 )
                                    {{ $list->links() }}
                                @endif
                            </div>
                        </div>
                    </div>
        </div>

    </div>
@stop

