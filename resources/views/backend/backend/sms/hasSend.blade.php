@extends('backend.layout.base')
@section('content')
    <meta name="_token" content="{{ csrf_token() }}"/>
    <style>

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
                            <li ><span>消息管理</span></li>
                            <li><span><a href="/backend/sms/message">站内信管理</a></span></li>
                            <li class="active"><a href="{{ url('/backend/sms/has_send') }}">已发站内信</a></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix">
                            <ul class="nav nav-tabs">
                                <li><a href="/backend/sms/message">发送站内信</a></li>
                                <li class="active"><a href="{{ url('/backend/sms/has_send') }}">已发站内信</a></li>
                                {{--<li><a href="">模板</a></li>--}}
                            </ul>

                            <header class="main-box-header clearfix">
                                @include('backend.layout.alert_info')
                            </header>
                            <div class="main-box-body clearfix">
                                <div class="table-responsive">
                                    <table id="user" class="table table-hover" style="clear: both">
                                        <thead>
                                        <tr>
                                            <th>站内信标题</th>
                                            <th>发送时间</th>
                                            <th>查看详情</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ( $list as $value )
                                            <tr>
                                                <td>{{ $value->get_detail->title }}</td>
                                                <td>
                                                    {{ date('Y-m-d H:i:s',$value->read_time) }}
                                                </td>
                                                <td>
                                                    <a href="{{ url('/backend/sms/get_detail/'.$value->get_detail->id) }}">查看详情</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @include('backend.layout.pages')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop