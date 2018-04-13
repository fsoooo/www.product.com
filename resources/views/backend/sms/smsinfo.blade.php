@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper" class="email-inbox-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <ol class="breadcrumb">
                    <li><a href="/backend/">主页</a></li>
                    <li ><span>消息管理</span></li>
                    <li ><span><a href="/backend/sms/sms">短信管理</a></span></li>
                    <li class="active"><span>短信详情</span></li>
                </ol>
                <div id="email-box" class="clearfix">
                    <div class="row">
                        <div class="col-lg-12">
                            <div id="email" class="email" style="height:830px">
                                <div class="email-detail-nano-content">
                                    <div id="email-detail-inner">
                                        <div id="email-detail-subject" class="clearfix">
                                            <span class="subject">TO:{{$info[0]->send_phone}}</span>
                                        </div>
                                        <div id="email-detail-sender" class="clearfix">

                                            <div class="users">
                                                <div class="from clearfix">
                                                    <div class="name">
                                                        {{$info[0]->content}}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tools">
                                                <div class="date">
                                                    发送时间：  {{$info[0]->created_at}}
                                                </div>
                                            </div>
                                        </div>
                                        <div id="email-body">
                                            <p>
                                                @if(isset($company_name))
                                                    发送方：{{$company_name}}
                                                @else
                                                    发送方：manager
                                                @endif

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