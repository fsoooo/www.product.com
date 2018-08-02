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
                            <li><span><a href="/backend/sms/emails">邮件管理</a></span></li>
                            <li class="active"><a href="{{ url('/backend/sms/emails') }}">发送邮件</a></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="{{ url('/backend/sms/emails') }}">发送邮件</a></li>
                                    <li><a href="{{ url('/backend/sms/emaillists') }}">已发邮件</a></li>
                                    <li><a href="{{ url('/backend/sms/emailinfos') }}">代理公司邮件</a></li>
                                    <li><a href="{{ url('/backend/sms/emailmodels') }}">邮件模板</a></li>

                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>发送邮件</p></h3>
                                        @include('backend.layout.alert_info')
                                        <div class="panel-group accordion" id="operation">
                                            <div class="panel panel-default">
                                                <form action="{{ url('/backend/sms/send_message') }}" method="post" id="send-message-form">
                                                    {{ csrf_field() }}
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <tbody>


                                                        <tr>
                                                            <td width="15%">收件人地址</td>
                                                            <td width="60%">
                                                                <input type="text" class="form-control" placeholder="请输入收件人的邮箱地址" name = 'email' id="email">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="15%">邮件标题</td>
                                                            <td width="60%">
                                                                <input type="text" class="form-control" placeholder="请输入邮件标题" id="title" name='title'>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>邮件内容</td>
                                                            <td>
                                                                <textarea class="form-control"  cols="30" rows="10" name="content" id="content"></textarea>
                                                            </td>

                                                        </tr>
                                                        <tr>
                                                            <td>邮件模板</td>
                                                             <td><select name="models" id="models">
                                                                    <option onclick="doClean()">不使用模板</option>

                                                                    @foreach($models as $value)
                                                                        <option id="select_id" onclick="a({{$value['model_id']}})">
                                                                            {{$value['model_name']}}
                                                                        </option>

                                                                        @endforeach
                                                                     <script>
                                                                         function doClean(){
                                                                             //            alert(111);
                                                                             $("#content").text(' ');
                                                                         }
                                                                         function a(model_id){
                                                                             var model_id = model_id;
                                                                             var params = {model_id:model_id};
                                                                             $.ajax({
                                                                                 type: "post",
                                                                                 dataType: "json",
                                                                                 async: true,
                                                                                 url: "getemailmodels",
                                                                                 data: params,
                                                                                 headers: {
                                                                                     'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                                                                                 },
                                                                                 success: function(data){
                                                                                     var status = data['status'];
                                                                                     var model = data['model'];
                                                                                     if(status ==   0){
                                                                                         $("#content").text(model);


                                                                                     }else {
                                                                                       alert('error');
                                                                                     }
                                                                                 }
                                                                             });

                                                                         }
                                                                     </script>
                                                                </select>

                                                            </td>
                                                        </tr>
                                                        <tr>

                                                            {{--<td width="15%">上传附件</td>--}}
                                                            {{--<td width="5%"><input type="file" name="file" id="uploadForm"></td>--}}
                                                            {{--<td width="10%"><input type="button" value="上传" onclick="doUpload()" /></td>--}}

                                                        </tr>

                                                        {{--<tr>--}}
                                                            {{--<td>选择收件人</td>--}}
                                                            {{--<td>--}}
                                                                {{--<select name="receive_type" id="send-person-type" class="form-control">--}}
                                                                    {{--<option value="0">全体成员</option>--}}
                                                                    {{--<option value="1">全体代理人</option>--}}
                                                                    {{--<option value="2">全体客户</option>--}}
                                                                    {{--<option value="3">全体个人客户</option>--}}
                                                                    {{--<option value="4">全体企业客户</option>--}}
                                                                    {{--<option value="5">指定成员</option>--}}
                                                                {{--</select>--}}
                                                            {{--</td>--}}
                                                        {{--</tr>--}}
                                                        {{--<tr id="designated-cust-block" hidden>--}}
                                                            {{--<td>指定成员</td>--}}
                                                            {{--<td>--}}

                                                            {{--</td>--}}
                                                        {{--</tr>--}}
                                                        {{--<tr>--}}
                                                            {{--<td>发送时间</td>--}}
                                                            {{--<td>--}}
                                                                {{--<select class="form-control" id="send-time-type">--}}
                                                                    {{--<option value="1">当前发送</option>--}}
                                                                    {{--<option value="2">定时发送</option>--}}
                                                                {{--</select>--}}
                                                            {{--</td>--}}
                                                        {{--</tr>--}}
                                                        {{--<tr id="designated-time-block" hidden>--}}
                                                            {{--<td>选择时间</td>--}}
                                                            {{--<td><input id="designated-time" class="form-control" type="datetime-local" name="designated-time"/></td>--}}
                                                        {{--</tr>--}}
                                                        </tbody>
                                                    </table>
                                                </form>
                                            </div>
                                            <div id="check"></div>
                                            <form id= "uploadForm">
                                                <table class="table table-hover" style="clear: both">
                                                    <tr>
                                                        <td width="20%">上传附件：</td>
                                                        <td width="15%"><input type="file" name="file"/></td>
                                                        <td><input type="button" value="上传" onclick="doUpload()" /></td>
                                                        <td><font style="color: red">附件大小不能大于2M</font></td>
                                                    </tr>
                                                </table>
                                            </form>
                                            <button id="send-message-btn" class="btn btn-success" onclick="doemail()">确认发送</button>
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
    </div>
    <script src="/js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript">
        function doUpload() {
            var formData = new FormData($( "#uploadForm" )[0]);
            $.ajax({
                url: 'emailfilesend' ,
                type: 'POST',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (returndata) {
                    if(returndata.status=true){
                        alert('上传附件成功！！');
                        var file  = returndata.message[0];
                        window.file = file;
                    }
                },
                error: function (returndata) {
                    alert(returndata);
                }
            });
        }

        function doemail(){


            var time = $('#time option:selected').val();
            var title = $('#title').val();
            var email = $('#email').val();
            var content = $('#content').val();
            if(typeof(file)=="undefined"){
                var params = {title:title,email:email,content:content,file:''};
            }else{
                var params = {title:title,email:email,content:content,file:file};
            }

//                        if(time==0){
            $.ajax({
                url:'emailsend',
                type:'get',
                dataType:'json',
                data:params,
                success:function(msg){
                    if(msg.status == 1){
                        //alert(msg.message);
                        $('#check').html('<font color="red">'+msg.message+'</font>');
                    }else{
                        //alert(msg.message);
                        $('#check').html('<font color="green">'+msg.message+'</font>');
                        window.location = location;


                    }

                }
            },'JSON');
//                        }else{
//                            var sec  = time*60*1000;
//
//                            setTimeout(send(),sec);
//                            function send(){
//                                 $.ajax({
//                                              url:'/backend/emailsend',
//                                              type:'get',
//                                              dataType:'json',
//                                              data:params,
//                                              success:function(msg){
//                                                if(msg.status == 1){
//                                                  //alert(msg.message);
//                                                  $('#check').html('<font color="red">'+msg.message+'</font>');
//                                                }else{
//                                                    //alert(msg.message);
//                                                  $('#check').html('<font color="green">'+msg.message+'</font>');
//
//
//                                                }
//
//                                              }
//                                            },'JSON');
//                            }
//
//                        }

        }

    </script>


@stop

