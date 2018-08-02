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
                            <li class="active"><span>退保管理</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="tabs-wrapper tabs-no-header">
                            <ul class="nav nav-tabs">
                                @if($type == 'hesitation')
                                    <li class="active"><a href="{{ url('/backend/cancel/hesitation') }}">犹豫期内退保</a></li>
                                    <li><a href="{{ url('/backend/cancel/out_hesitation') }}">犹豫期外退保</a></li>
                                @else
                                    <li><a href="{{ url('/backend/cancel/hesitation') }}">犹豫期内退保</a></li>
                                    <li class="active"><a href="{{ url('/backend/cancel/out_hesitation') }}">犹豫期外退保</a></li>
                                @endif
                            </ul>
                        @include('backend.layout.alert_info')
                        <div class="main-box">
                            <header class="main-box-header clearfix">
                                <h2 class="pull-left">退保申请</h2>
                            </header>
                            <div class="main-box-body clearfix">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th><span>订单编号</span></th>
                                            <th><span>保单生效时间</span></th>
                                            <th><span>保单结束时间</span></th>
                                            <th><span>申请时间</span></th>
                                            <th><span>查看更多</span></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @if($count == 0)
                                                <td colspan="8">暂无退保申请</td>
                                            @else
                                                @foreach($cancel_list as $value)
                                                    <tr>
                                                        <td>{{ $value->cancel_order->order_code }}</td>
                                                        <td>{{ $value->cancel_order->start_time }}</td>
                                                        <td>{{ $value->cancel_order->end_time }}</td>
                                                        <td>{{ $value->created_at }}</td>
                                                        <td><a href="{{ url('/backend/cancel/cancel_detail/'.$value->id) }}">详细信息</a></td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                {{--@if( $count != 0 )--}}
                                    {{--{{ $list->links() }}--}}
                                {{--@endif--}}
                            </div>
                        </div>
                    </div>
        </div>

    </div>
@stop

