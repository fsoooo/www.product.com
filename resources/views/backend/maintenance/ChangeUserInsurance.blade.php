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
                            <li><span>售后管理</span></li>
                            <li><span>保全管理</span></li>
                            <li class="active"><span>个人保额变更</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    {{--@if()--}}
                                    <li class="active"><a href="{{ url('/backend/maintenance/change_insurance/user') }}">个人保额变更</a></li>
                                    <li><a href="{{ url('/backend/maintenance/change_insurance/company') }}">企业保额变更</a></li>
                                    <li><a href="{{ url('/backend/claim/get_claim/deal') }}">已处理</a></li>
                                </ul>
                    <div class="col-lg-12">
                        @include('backend.layout.alert_info')
                        <div class="main-box clearfix">
                            <header class="main-box-header clearfix">
                                <h2 class="pull-left">个人保额变更</h2>
                            </header>
                            <div class="main-box-body clearfix">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th><span>保单编号</span></th>
                                            <th><span>客户名称</span></th>
                                            <th><span>真实姓名</span></th>
                                            <th><span>身份证号</span></th>
                                            <th><span>保额变更</span></th>
                                            <th><span>保单开始时间</span></th>
                                            <th><span>保单结束时间</span></th>
                                            <th><span>理赔申请时间</span></th>
                                            <th><span>理赔状态</span></th>
                                            <th><span>查看</span></th>
                                        </tr>
                                        </thead>
                                        <tbody>

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

