@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper" class="email-inbox-wrapper">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="{{ url('/backend') }}">主页</a></li>
                <li ><span>消息管理</span></li>
                <li><span><a href="/backend/sms/sms">短信管理</a></span></li>
                <li class="active"><span>已发短信详情</span></li>
            </ol>
            <div class="col-lg-12">
                <div id="email-box" class="clearfix">
                    <div class="row">
                        <div class="col-lg-12">
                            <div id="email" class="email" style="height:830px">
                                <div class="email-detail-nano-content">
                                    <div id="email-detail-inner">
                                        <div id="email-detail-subject" class="clearfix">
                                            <span class="subject">收信人:{{$info->send_phone}}</span>
                                        </div>
                                        <h4 style="font-family: 微软雅黑;font-size: 18px"><b>短信内容</b></h4>
                                        <div id="email-detail-sender" class="clearfix">

                                            <div class="users">
                                                <div class="from clearfix">
                                                    <div class="name" style="font-family: 微软雅黑;font-size: 15px">
                                                        {{$info->content}}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tools">
                                                <div class="date">
                                                    发送时间：  {{$info->created_at}}
                                                </div>
                                            </div>
                                        </div>
                                        <div id="email-body">
                                            <p>
                                                发送方：{{$info->company_id}}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop