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
                                    <li class="active"><a href="{{ url('/backend/claim/get_claim/all') }}">理赔列表</a></li>
                                    <li><a href="{{ url('/backend/claim/get_claim/no_deal') }}">未处理</a></li>
                                    <li><a href="{{ url('/backend/claim/get_claim/deal') }}">已处理</a></li>
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
                                    {{--<th><span>客户名称</span></th>--}}
                                    <th><span>订单编号</span></th>
                                    <th><span>产品名称</span></th>
                                    <th><span>真实姓名</span></th>
                                    <th><span>手机号码</span></th>
                                    <th><span>身份证号</span></th>
                                    <th><span>理赔申请时间</span></th>
                                    <th><span>理赔状态</span></th>
                                    <th><span>查看</span></th>
                                </tr>
                                </thead>
                                <tbody>
                                @if( $count == 0 )
                                    <tr>
                                        <td colspan="8" style="text-align: center;">暂无理赔申请</td>
                                    </tr>
                                @else
                                    @foreach ( $claim as $value )
                                        <tr>
                                            {{--<td>--}}
                                                {{--{{ $value->user->name }}--}}
                                            {{--</td>--}}
                                            <td>
                                                {{ $value->order->order_code }}
                                            </td>
                                            <td>
                                                {{ $value->order->product->product_name }}
                                            </td>
                                            <td>
                                                {{ $value->user->real_name }}
                                            </td>
                                            <td>
                                                {{ $value->user->phone }}
                                            </td>
                                            <td>
                                                {{ $value->user->code }}
                                            </td>
                                            <td>
                                                {{ $value->created_at }}
                                            </td>
                                            <td class="text-center">
                                                @if ( $value->get_claim->status == 0 )
                                                    <span class="label label-success">尚未处理</span>
                                                @else
                                                   <span class="label label-primary"> {{ $value->get_claim->claim_status->status_name }}</span>
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
                        @if( $count != 0 )
                            {{ $claim->links() }}
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

