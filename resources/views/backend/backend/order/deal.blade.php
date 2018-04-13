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
                <div class="main-box clearfix">
                    <header class="main-box-header clearfix">
                        <h2 class="pull-left">未处理的理赔列表</h2>
                        <div id="reportrange" class="pull-right daterange-filter">
                            <a href="{{ url('/backend/claim/getdeal') }}">未处理理赔</a>
                            <a href="">已处理理赔</a>
                            {{--<i class="icon-calendar"></i>--}}
                            {{--<span></span> <b class="caret"></b>--}}
                        </div>
                    </header>
                    <div class="main-box-body clearfix">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th><a href="#"><span>客户名称</span></a></th>
                                    <th><a href="#" class="desc"><span>方案名称</span></a></th>
                                    <th><a href="#" class="asc"><span>真实姓名</span></a></th>
                                    <th><span>手机号码</span></th>
                                    <th><span>身份证号</span></th>
                                    <th><span>申请理赔时间</span></th>
                                    <th><span>理赔状态</span></th>
                                    <th><span>查看</span></th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ( $claim as $value )
                                        <tr>
                                            <td>
                                                <a href="#">{{ $value->name }}</a>
                                            </td>
                                            <td>
                                                2013/08/08
                                            </td>
                                            <td>
                                                <a href="#">{{ $value->rname }}</a>
                                            </td>
                                            <td>
                                                <a href="#">{{ $value->phone }}</a>
                                            </td>
                                            <td>
                                                <a href="#">{{ $value->code }}</a>
                                            </td>
                                            <td>
                                                <a href="#">{{ $value->ctime }}</a>
                                            </td>
                                            <td class="text-center">
                                                <span class="label label-success">已完成</span>
                                            </td>
                                            <td>
                                                <a href="/backend/claim/getClaimDetail">查看详情</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $claim->links() }}
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

