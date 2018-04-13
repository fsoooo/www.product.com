

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
        <div class="big-img" style="display: none;">
            <img src="" alt="" id="big-img" style="width: 75%;height: 90%;">
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">
                            <li><a href="{{ url('/backend') }}">主页</a></li>
                            <li class=""><span>客户统计</span></li>
                            <li class="active"><span>注册客户统计</span></li>
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
                        <h2 class="pull-left">注册客户统计列表</h2>
                    </header>
                        <div class="main-box-body clearfix">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th><span>客户名称</span></th>
                                        <th><span>联系方式</span></th>
                                        <th><span>客户联系邮箱</span></th>
                                        <th><span>身份标识</span></th>
                                        <th><span>客户类型</span></th>
                                        <th><span>住址</span></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if( $count == 0 )
                                        <tr>
                                            <td colspan="8" style="text-align: center;">暂无客户</td>
                                        </tr>
                                    @else
                                        @foreach ( $data as $value )
                                            <tr>
                                                <td>
                                                    {{ $value->real_name }}
                                                </td>
                                                <td>
                                                    {{ $value->phone }}
                                                </td>
                                                <td>
                                                    {{ $value->email }}
                                                </td>
                                                <td>
                                                    {{ $value->code }}
                                                </td>
                                                <td>
                                                    @if($value->type==0)
                                                        <a class="label label-primary" href="#" style="color: white">个人客户</a>
                                                    @else
                                                        <a  class="label label-info" href="#" style="color: white">企业客户</a>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{$value->address}}
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

