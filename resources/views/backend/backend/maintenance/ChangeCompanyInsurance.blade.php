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
                            <li class="active"><span>保全管理</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    {{--@if()--}}
                                    <li><a href="{{ url('/backend/maintenance/change_insurance/user') }}">个人保额变更</a></li>
                                    <li class="active"><a href="{{ url('/backend/maintenance/change_insurance/company') }}">企业人员变更</a></li>
                                    <li><a href="{{ url('/backend/claim/get_claim/deal') }}">已处理</a></li>
                                </ul>
                    <div class="col-lg-12">
                        @include('backend.layout.alert_info')
                        <div class="main-box clearfix">
                            <header class="main-box-header clearfix">
                                <h2>保全</h2>

                                <form action="{{ url('/backend/maintenance/get_person_change') }}" method="post">
                                    {{ csrf_field() }}
                                    <label for="">开始时间</label>
                                    <input type="date" value="" name="start_time">
                                    <label for="">结束时间</label>
                                    <input type="date" name="end_time">
                                    <button>查询</button>
                                </form>
                            </header>
                            <div class="main-box-body clearfix">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th><span>保单编号</span></th>
                                            <th><span>投保公司</span></th>
                                            <th><span>真实姓名</span></th>
                                            <th><span>手机号码</span></th>
                                            <th><span>身份证号</span></th>
                                            <th><span>保障开始时间</span></th>
                                            <th><span>保障结束时间时间</span></th>
                                            <th><span>操作时间</span></th>
                                            <th><span>状态</span></th>
                                            <th><span>操作</span></th>


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

