@extends('backend.layout.base')
@section('content')
    <meta name="_token" content="{{ csrf_token() }}"/>
    <div id="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">
                            <li><a href="{{ url('/backend') }}">主页</a></li>
                            <li ><span>工单管理</span></li>
                            <li class="active"><span>工单详情</span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                {{--<ul class="nav nav-tabs">--}}
                                    {{--<li><a href="{{ url('/backend/special/addspecial') }}">发起工单</a></li>--}}
                                    {{--<li><a href="{{ url('/backend/special/special') }}">已发工单</a></li>--}}
                                    {{--<li><a href="{{ url('/backend/special/recspecial') }}">回收站</a></li>--}}
                                {{--</ul>--}}
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>工单详情</p></h3>
                                        <div class="panel-group accordion" id="account">
                                            <div class="panel panel-default">
                                                <table id="user" class="table table-hover" style="clear: both">
                                                    <tbody>
                                                    <tr>
                                                        <td width="15%">名称</td>
                                                        <td width="65%">
                                                            {{ $res['title'] }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="15%">内容</td>
                                                        <td width="65%">
                                                            {{ $res['content'] }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>发送时间</td>
                                                        <td>
                                                            {{ $res['created_at'] }}
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
    </div>
    <script src="/js/jquery-3.1.1.min.js"></script>
@stop

