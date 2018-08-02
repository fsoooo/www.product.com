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
                            <li class=""><a href="{{url('/backend/boss/product/index/all')}}"><span>产品统计</span></a></li>
                            <li class="active"><span>产品订单详情</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">

            <div class="col-lg-12">
                @include('backend.layout.alert_info')
                <div class="main-box clearfix">
                    <header class="main-box-header clearfix">
                        {{--<h2 class="pull-left">产品详情</h2>--}}

                    </header>
                    <div class="main-box-body clearfix">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th><span>产品名称</span></th>
                                    <th><span>产品唯一码</span></th>
                                    <th><span>订单支付时间</span></th>
                                    <th><span>理赔类型</span></th>
                                    <th><span>成交类型</span></th>
                                    <th><span>订单价格</span></th>
                                    <th><span>订单状态</span></th>
                                </tr>
                                </thead>
                                <tbody>
                                @if( $count == 0 )
                                    <tr>
                                        <td colspan="8" style="text-align: center;">暂无产品订单</td>
                                    </tr>
                                @else
                                    @foreach ( $product_list as $value )
                                        <tr>
                                            <td>
                                                {{ $value->product_name }}
                                            </td>

                                            <td>
                                                {{ $value->product_number }}
                                            </td>
                                            <td>
                                               {{ $value->pay_time }}
                                            </td>
                                            <td>
                                                @if($value->claim_type=='online')
                                                    线上
                                                @elseif($value->claim_type=='offline')
                                                    线下
                                                @endif
                                            </td>
                                            <td>
                                                @if($value->deal_type=='0')
                                                    线上成交
                                                @elseif($value->deal_type=='1')
                                                    线下成交
                                                @endif
                                            </td>
                                            <td>
                                               ￥{{ $value->premium/100 }}
                                            </td>
                                            <td>
                                                @if($value->status==1)
                                                    已支付
                                                @elseif($value->status==2)
                                                    未支付
                                                @elseif($value->status==3)
                                                    支付失败
                                                {{--@elseif($value->status==4)--}}
                                                    {{--处理理赔中--}}
                                                {{--@elseif($value->status==5)--}}
                                                    {{--理赔完成--}}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                        {{--@if( $count != 0 )--}}
{{--                            {{ $claim->links() }}--}}
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

