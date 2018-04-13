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
                            <li class="active"><span>产品统计</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    @if($type == 'all')
                                        <li class="active"><a href="{{ url('/backend/boss/product/index/all') }}">所有产品</a></li>
                                        <li><a href="{{ url('/backend/boss/product/index/on_sale') }}">上架产品</a></li>
                                        <li><a href="{{ url('/backend/boss/product/index/not_sale') }}">未上架产品</a></li>
                                    @elseif($type == 'on_sale')
                                        <li><a href="{{ url('/backend/boss/product/index/all') }}">所有产品</a></li>
                                        <li class="active"><a href="{{ url('/backend/boss/product/index/on_sale') }}">上架产品</a></li>
                                        <li><a href="{{ url('/backend/boss/product/index/not_sale') }}">未上架产品</a></li>
                                    @elseif($type == 'not_sale')
                                        <li><a href="{{ url('/backend/boss/product/index/all') }}">所有产品</a></li>
                                        <li><a href="{{ url('/backend/boss/product/index/on_sale') }}">上架产品</a></li>
                                        <li class="active"><a href="{{ url('/backend/boss/product/index/not_sale') }}">未上架产品</a></li>
                                    @endif
                                </ul>
            <div class="col-lg-12">
                @include('backend.layout.alert_info')
                <div class="main-box clearfix">
                    <header class="main-box-header clearfix">
                        <h2 class="pull-left">产品列表</h2>

                    </header>
                    <div class="main-box-body clearfix">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th><span>产品名称</span></th>
                                    <th><span>所属公司</span></th>
                                    <th><span>产品唯一码</span></th>
                                    <th><span>查看</span></th>
                                </tr>
                                </thead>
                                <tbody>
                                @if( $count == 0 )
                                    <tr>
                                        <td colspan="8" style="text-align: center;">暂时没有产品</td>
                                    </tr>
                                @else
                                    @foreach ( $product_list as $value )
                                        <tr>
                                            <td>
                                                {{ $value->product_name }}
                                            </td>
                                            <td>
                                                此处显示产品的归属公司
                                            </td>
                                            <td>
                                                {{ $value->product_number }}
                                            </td>

                                            <td>
                                                <a href="/backend/boss/product/detail/{{ $value->id }}">查看详情</a>
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

