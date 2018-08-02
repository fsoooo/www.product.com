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
                            <li ><span><a href="/backend/sms/onlineservice">在线客服</a></span></li>
                            <li class="active"><span><a href="/backend/sms/addonlines">添加客服</a></span></li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-box clearfix" style="min-height: 1100px;">
                            <div class="tabs-wrapper tabs-no-header">
                                <ul class="nav nav-tabs">
                                    <li><a href="{{ url('/backend/sms/onlineservice')}}">客服列表</a></li>
                                    <li class="active"><a href="{{ url('/backend/sms/addonlines') }}">添加客服</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        @include('backend.layout.alert_info')
                                        <div class="panel-group accordion" id="operation">
                                            <div class="panel panel-default">
                                                <form action="{{ url('/backend/sms/send_message') }}" method="post" id="send-message-form">
                                                    {{ csrf_field() }}
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <tbody>


                                                        <tr>
                                                            <td width="15%">客服名称</td>
                                                            <td width="60%">
                                                                <input type="text" class="form-control" placeholder="请输入客服名称" name = 'name' id="name">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="15%">客服号码</td>
                                                            <td width="60%">
                                                                <input type="text" class="form-control" placeholder="请输入客服号码" name = 'number' id="number">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="15%">真实姓名</td>
                                                            <td width="60%">
                                                                <input type="text" class="form-control" placeholder="请输入真实姓名" name = 'real_name' id="real_name">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="15%">身份证号</td>
                                                            <td width="60%">
                                                                <input type="text" class="form-control" placeholder="请输入身份证号" name = 'card_id' id="card_id">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="15%">联系方式</td>
                                                            <td width="60%">
                                                                <input type="text" class="form-control" placeholder="请输入联系方式" name = 'phone' id="phone">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>客服资料</td>
                                                            <td>
                                                                <textarea class="form-control"  cols="30" rows="10" name="data" id="data"></textarea>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </form>
                                            </div>
                                            <div id="check"></div>
                                            <button id="send-message-btn" class="btn btn-success" onclick="doAddOnlines()">确认添加</button>
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
    <script type="text/javascript">
        function doAddOnlines() {
            var name = $('#name').val();
            var number = $('#number').val();
            var datas = $('#data').val();
            var real_name = $('#real_name').val();
            var card_id = $('#card_id').val();
            var phone = $('#phone').val();

            var reg = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/; //身份证
            var qq = /^[1-9][0-9]{4,9}$/; //QQ号
            if(!qq.test(number)){
                alert("请输入合法QQ号");back;
            }
            if(!reg.test(card_id)){
                alert("身份证输入不合法");back;
            }
            var re = /^1\d{10}$/; //手机号
            if(!re.test(phone)){
                alert("手机号格式有误");back;
            }
            var params = {name:name,number:number,datas:datas,real_name:real_name,card_id:card_id,phone:phone};
            $.ajax({
                url:'/backend/sms/doaddonlines',
                type:'post',
                dataType:'json',
                data:params,
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function(msg){
                    if(msg.status == '0'){
                        alert(msg.message);
                        window.location = location;
                    }else{
                        alert(msg.message);
                    }
                }
            },'JSON');
        }
    </script>
@stop

