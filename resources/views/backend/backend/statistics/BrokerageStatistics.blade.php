@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">
                            <li><a href="{{ url('/backend') }}">主页</a></li>
                            <li class="active"><span>财务管理</span></li>
                        </ol>
                        <h1>佣金统计</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#">佣金列表</a></li>
                            </ul>
                            <header class="main-box-header clearfix">
                                @include('backend.layout.alert_info')
                            </header>
                            <div class="main-box-body clearfix">
                                <div class="table-responsive">
                                    <table id="user" class="table table-hover" style="clear: both">
                                        <thead>
                                        <tr>
                                            <th>订单编号</th>
                                            <th>代理人所得佣金</th>
                                            <th>订单总金额</th>
                                            <th>创建时间</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if($count == 0)
                                            <tr>
                                                <td colspan="3">暂无提奖记录</td>
                                            </tr>
                                        @else
                                            @foreach ( $brokerage_list as $value )
                                                <tr>
                                                    <td>{{ $value->order_brokerage_order->order_code }}</td>
                                                    <td>{{ $value->user_earnings }}</td>
                                                    <td>{{ $value->order_pay }}</td>
                                                    <td>{{ $value->created_at }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @if($count != 0)
                                {{ $brokerage_list->links() }}
                            @endif
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

@stop