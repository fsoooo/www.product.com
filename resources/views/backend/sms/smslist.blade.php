@extends('backend.layout.base')
@section('content')
            <div id="content-wrapper" class="email-inbox-wrapper">
                <div class="row">
                    <div class="col-lg-12">
                        <ol class="breadcrumb">
                            <li><a href="/backend/">主页</a></li>
                            <li ><span>消息管理</span></li>
                            <li ><span><a href="/backend/sms/sms">短信管理</a></span></li>
                            <li class="active"><span>中介公司短信列表</span></li>
                        </ol>
                        <div id="email-box" class="clearfix">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div id="email" class="email" style="height:830px">
                                        <div class="email-content-nano-content">
                                            <ul id="email-list">
                                                @foreach($infos as $info)
                                                    <li class="unread">
                                                        <a href="smslistinfo?sms_id={{$info['id']}}" class="attachment">
                                                  <div class="name">
                                                      接收人手机号： {{$info['send_phone']}}
                                                    </div>
                                                    <a>
                                                    <div class="meta-info">
                                                          发送时间：   {{$info['created_at']}}</i>
                                                        </a>
                                                        <span class="date"></span></a>
                                                    </div>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @include('backend.layout.pages')
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
@stop