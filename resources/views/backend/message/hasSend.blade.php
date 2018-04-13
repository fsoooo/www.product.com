@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">
                            <li><a href="/backend/">主页</a></li>
                            <li ><span>消息管理</span></li>
                            <li><a href="{{ url('/backend/sms/message')}}">站内信管理</a></li>
                            <li  class="active"><a href="{{ url('/backend/sms/has_send')}}">已发站内信</a></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix">
                            <ul class="nav nav-tabs">
                                <li><a href="{{ url('/backend/sms/message')}}">发送站内信</a></li>
                                <li class="active"><a href="{{ url('/backend/sms/has_send') }}">已发站内信</a></li>
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
                                            <th>发送人</th>
                                            <th>收件人</th>
                                            <th>发送时间</th>
                                            <th>查看详情</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ( $res as $value )
                                                <td>{{ $value->title}}</td>
                                                <td>
                                                    @if($value->send_id == '0')
                                                        <b>admin</b>
                                                    @endif
                                                </td>
                                                    @if($value->receive_id <> '0')
                                                        @foreach($user as $v)
                                                            @if($value->receive_id == $v['id'])
                                                            <td>
                                                                {{ $v->display_name}}
                                                            </td>
                                                            @endif
                                                        @endforeach
                                                    @elseif($value->receive_id == '0')
                                                        <td>
                                                            所有人
                                                        </td>
                                                    @endif
                                                <td>{{ $value->created_at}}</td>
                                                <td>
                                                    <a href="{{ url('/backend/sms/get_detail?id='.$value->id) }}">查看详情</a>
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