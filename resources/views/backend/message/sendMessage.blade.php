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
                            <li><a href="/backend/">主页</a></li>
                            <li ><span>消息管理</span></li>
                            <li ><span><a href="/backend/sms/message">站内信管理</a></span></li>
                            <li  class="active"><a href="{{ url('/backend/sms/message')}}">发送站内信</a></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li  class="active"><a href="{{ url('/backend/sms/message')}}">发送站内信</a></li>
                                    <li><a href="{{ url('/backend/sms/has_send') }}">已发站内信</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>发送站内信</p></h3>
                                        @include('backend.layout.alert_info')
                                        <div class="panel-group accordion" id="operation">
                                            <div class="panel panel-default">
                                                <form  id="send-message-form">
                                                    {{ csrf_field() }}
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr>
                                                            <td width="15%">站内信标题</td>
                                                            <td width="60%">
                                                                <input type="text" class="form-control" placeholder="请输入站内信标题" name="title" id="title">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>站内信内容</td>
                                                            <td>
                                                                <textarea class="form-control" name="content" id="content" cols="30" rows="10"></textarea>
                                                            </td>
                                                        </tr>
                                                        <input type="hidden" name="send_id" id="send_id" = value="1">
                                                        <tr>
                                                            <td>选择收件人</td>
                                                            <td>
                                                                <select name="receive_type" id="send-person-type" class="form-control">
                                                                    <option value="0" onclick="closeform()">全体成员</option>
                                                                    <option value="1" onclick="showform()">指定成员</option>
                                                                </select>
                                                            </td>
                                                            <td id="designated-cust-block" style="display: none">
                                                                <select name="receive_name" id="send-person-name" class="form-control">
                                                                    @foreach($users as $value)
                                                                        <option  value="{{$value['id']}}">{{$value['display_name']}}</option>
                                                                    @endforeach
                                                                </select>
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
                                            <button id="send-message-btn" class="btn btn-success" onclick="domail()">发送</button>
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
        function showform() {
        document.getElementById('designated-cust-block').style.display = "block";
        }
        function closeform() {
            document.getElementById('designated-cust-block').style.display = "none";
        }
        function domail(){
            var receive = $("#send-person-type").find("option:selected").val();
            var send_id = "admin";
            var title = $('#title').val();
            var content = $('#content').val();
            if(receive == '1'){
                var receive_name = $("#send-person-name").find("option:selected").val();
                var params = {receive:receive,content:content,send_id:send_id,title:title,receive_name:receive_name};
            }else{
                var receive_name = '';
                var params = {receive:receive,content:content,send_id:send_id,title:title,receive_name:receive_name};
            }
                $.ajax({
                    url:'/backend/sms/mailsend',
                    type:'post',
                    dataType:'json',
                    data:params,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(msg){
                        if(msg.status == 0){
                            alert(msg.message);
                            $('#notice').html('<font color="green">'+msg.message+'</font>');
                            window.location.href=location;
                        }else{
                            alert(msg.message);
                            $('#notice').html('<font color="red">'+msg.message+'</font>');
                        }
                    }
                },'JSON');
        }
    </script>
@stop

