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
                            <li class="active"><a href="/backend/sms/message">发送站内信</a></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="">发送站内信</a></li>
                                    <li><a href="{{ url('/backend/sms/has_send') }}">已发站内信</a></li>
                                    {{--<li><a href="">模板</a></li>--}}
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>发送站内信</p></h3>
                                        @include('backend.layout.alert_info')
                                        <div class="panel-group accordion" id="operation">
                                            <div class="panel panel-default">
                                                <form action="{{ url('/backend/sms/send_message') }}" method="post" id="send-message-form">
                                                    {{ csrf_field() }}
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr>
                                                            <td width="15%">站内信标题</td>
                                                            <td width="60%">
                                                                <input type="text" class="form-control" placeholder="请输入站内信标题" name="title">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>站内信内容</td>
                                                            <td>
                                                                <textarea class="form-control" id="content-text" name="content" id="" cols="30" rows="10"></textarea>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>选择收件人</td>
                                                            <td>
                                                                <select name="receive_type" id="send-person-type" class="form-control">
                                                                    <option value="0">全体成员</option>
                                                                    <option value="1">全体代理人</option>
                                                                    <option value="2">全体客户</option>
                                                                    <option value="3">全体个人客户</option>
                                                                    <option value="4">全体企业客户</option>
                                                                    <option value="5">指定成员</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr id="designated-cust-block" hidden>
                                                            <td>指定成员</td>
                                                            <td>

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>发送时间</td>
                                                            <td>
                                                                <select class="form-control" id="send-time-type">
                                                                    <option value="1">当前发送</option>
                                                                    <option value="2">定时发送</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr id="designated-time-block" hidden>
                                                            <td>选择时间</td>
                                                            <td><input id="designated-time" class="form-control" type="datetime-local" name="designated-time"/></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </form>
                                            </div>
                                            <button id="send-message-btn" class="btn btn-success">发送</button>
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
    <script src="/js/jquery-3.1.1.min.js"></script>
    <script>
        $(function(){
            //选择时间
            var send_time_type = $('#send-time-type');
            var designated_time_block = $('#designated-time-block');
            var designated_time = $('#designated-time');
            send_time_type.change(function(){
                if($('#send-time-type option:selected').val() == 1){
                    designated_time_block.attr('hidden','');
                    designated_time.removeAttr('name');
                }else {
                    designated_time_block.removeAttr('hidden');
                    designated_time.attr('name','designated-time')
                }
            })
            //选择接受人员
            var send_person_type = $('#send-person-type');
            var designated_person_block = $('#designated-cust-block');
            send_person_type.change(function(){
                if($('#send-person-type option:selected').val() !== 5){
                    designated_person_block.attr('hidden','');

                }else if($('#send-person-type option:selected').val() == 5){
                    designated_person_block.removeAttr('hidden');
                }
            })



            //进行验证发送
            var title = $('input[name=title]');
            var content = $('#content-text');
            var btn = $('#send-message-btn');
            var send_message_form = $('#send-message-form');
            btn.click(function(){
                var title_val = title.val();
                var content_val = content.val();
                if(title_val == ''){
                    title.parent().addClass("has-error");
                    alert('请输入标题');
                    return false;
                }else{
                    title.parent().removeClass("has-error");
                    if(content_val == ''){
                        content.parent().addClass("has-error");
                        alert('请输入内容');
                        return false;
                    }else {
                        content.parent().removeClass("has-error");
                    }
                }
                var data = send_message_form.serialize();

                $.ajax({
                    type: "post",
                    dataType: "json",
                    async: true,
                    //修改的地址，
                    url: "/backend/sms/send_message",
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    success: function(data){
                        var status = data['status'];
                        alert(data['data']);
                        if(status == 200){
                            location.href = '/backend/sms/has_send'
                        }
                    },error: function () {
                        alert('发送失败');
                    }
                });
            })


        })

    </script>
@stop

