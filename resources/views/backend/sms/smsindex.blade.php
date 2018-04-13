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
        <li class="active"><span>短信管理</span></li>
    </ol>

    <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
      发送短信
    </button>
    <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal1">
        添加模板
    </button>
    <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal2">
        短信模板
    </button>
<!-- Modal -->
    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="myModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">编辑短信</h4>
                </div>
                <div class="modal-body">
                    <label>短信模板选择</label>
                    <select name="model" id="modelviews">
                        <option value="null" selected>请选择</option>
                        @foreach($models as $model)
                        <option value="{{$model['model_id']}}" onclick="model_view()">{{$model['model_name']}}</option>
                        @endforeach
                    </select><br/><br/>
                    <input type="text" class="modal-body" id="phone" name="phone" placeholder="Phone" onblur="model_view()"><br/><br/>
                    <input type="text" class="modal-body" id="name" name="name" placeholder="Name" onblur="model_view()"><br/><br/>
                    <input type="text" class="modal-body" id="time" name="time" placeholder="time" onblur="model_view()">
                    <br/>
                    <br/>
                                                    <span style="font-family: 微软雅黑;font-size: 15px">
                                                    <br/>时间格式：2017-06-05 12:00:00<br/><br/>
                                                    <p style="color:green">如果不填写时间，系统将自动使用当前时间！如果您选择的时间晚于今天，系统也会自动调整为今天！</p><br/>
                                                    <h4><b>短信模版预览</b></h4>
                                                    <div id="modelview"></div><br/><br/>
                                                    </span>

                </div>
                <div class="modal-footer">
                    <div id="check"></div>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="dosms()">确认发送</button>
                </div>

            </div>
        </div>
    </div>
    <!-- Modal -->
    <script type="text/javascript">
        function model_view(){
            var phone = $('#phone').val();
            var name = $('#name').val();
            var model = $('#modelviews option:selected').val();
            var time = $('#time').val();
            switch (model)
            {
                case "null":
                    break;
                case '52339':
                    $('#modelview').html('<font color="green">'+
                        '尊敬的用户，您本次的验证码为:****，3分钟内有效,如非本人操作请忽略本信息。'
                        +'</font>');
                    break;
                case '62523':
                    $('#modelview').html('<font color="green">'+
                        '尊敬的用户，您正在申请重置密码，验证码为****，请勿向任何单位或个人泄露此验证码。'
                        +'</font>');
                    break;
                case '62524':
                    $('#modelview').html('<font color="green">'+
                        '感谢您的注册，本次注册验证码为****，请于3分钟内正确输入，切勿泄露他人。'
                        +'</font>');
                    break;
                case '62526':
                    $('#modelview').html('<font color="green">'+
                        '尊敬的'+name+'先生/女士，您在我们平台上****的申请已成功，请登录平台-我的申请，查看申请记录。'
                        +'</font>');
                    break;
                case '62529':
                    $('#modelview').html('<font color="green">'+
                        '尊敬的'+name+'先生/女士，您在我们平台上****的申请已过期，请登录平台-我的申请，查看申请记录。'
                        +'</font>');
                    break;
                case '62508':
                    $('#modelview').html('<font color="green">'+
                        '尊敬的'+name+'先生/女士：您上传的理赔案件我们已于'+timestamp3+'收到并查验完毕，与您上传的内容一致，我们已开始受理，请您随时关注网站名称公众号了解理赔进度，或拨打客户服务热线400-886-2309进行咨询。'
                        +'</font>');
                    break;
                case '62509':
                    $('#modelview').html('<font color="green">'+
                        '尊敬的'+name+'先生/女士，您好！您参加的我们网站的****活动，因未及时录入保障人员信息，保障申请及账户将在24小时后自动失效，如有疑问请联系客服400-886-2309。'
                        +'</font>');
                    break;
                case '107973':
                    $('#modelview').html('<font color="green">'+
                        '亲爱的用户，您正在使用手机号快捷登录，验证码为{1}，请于3分钟内正确输入，请勿向任何单位或个人泄露。 '
                        +'</font>');
                    break;
            }
        }

        function dosms(){//发送短信
            var phone = $('#phone').val();
            var name = $('#name').val();
            var model = $('#modelviews option:selected').val();
            var time = $('#time').val();
            var company = "44";
            var token ="f7da7ba7e0eadbe8ea01d418b93c32";//从数据库中读取
            var sms_content = {name:name};
            var params = {company:company,phone:phone,token:token,sms_content:sms_content,model:model,time:time};
            $.ajax({
                async: true,
                url:"dosms",
                type:'get',
                dataType:'json',
                data:params,
                success:function(msg){
                    if(msg.status == 0){
                        $('#check').html('<font color="green">'+msg.message+'</font>');
                    }else{
                        $('#check').html('<font color="red">'+msg.message+'</font>');
                        if(msg.status==2){
                            window.statuss = msg.status;
                            window.tokens = token;
                            $.ajax({
                                async:false,
                                url:"sms/test?token="+token,
                                type:'get',
                                dataType:'json',

                                success:function(msg){
                                    // alert(msg.status);
                                    console.log(msg);
                                    window.location=location;
                                }
                            },'JSON');

                        }
                    }

                }
            },'JSON');
        }
    </script>
    <div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">添加模板</h4>
                </div>
                <div class="modal-body">
                    <input type="text" class="modal-body" id="model" name="phone" placeholder="请输模板识别码">
                    &nbsp;&nbsp;&nbsp;
                    <input type="text" class="modal-body" id="model_name" name="model_name" placeholder="请输模板名称"><br/><br/>
                    <textarea class="modal-body" id="content" cols="60" rows="10" >请输入模板内容</textarea><br/><br/>
                    <font color="green"><p>示例：</p>
                        <p>模板识别码：62509</p>
                        <p>模板名称：账户失效</p>
                        <p>尊敬的{1}先生/女士，您好！您参加的我们网站的{2}活动，因未及时录入保障人员信息，保障申请及账户将在24小时后自动失效，如有疑问请联系客服400-886-2309。</p>
                    </font>
                </div><br/>
                <div id="check"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" onclick="doClear()">清空</button>
                    <button type="button" class="btn btn-primary" onclick="doAdd()">确认添加</button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
	<script type="text/javascript">
        function doClear(){
            $("#content").val('');
        }
        function doAdd(){
           var model = $('#model').val();
           var model_name = $('#model_name').val();
           var content = $('#content').val();
           var params = {content:content,model:model,model_name:model_name};
            $.ajax({
         					  url:'/backend/sms/addmodels',
         					  type:'get',
        					  dataType:'json',
         					  data:params,
					          success:function(msg){
					            if(msg.status == 1){
					                alert('添加失败，请刷新页面重试');
					              $('#check').html('<font color="red">'+msg.message+'</font>');
					            }else if(msg.status == 0){
					                alert('添加成功');
					              $('#check').html('<font color="green">'+msg.message+'</font>');
					            }else{
					                alert('您已经添加过了')
                                  }
					          }
					        },'JSON');	
        }
	</script><br/>
<div class="row">
<div class="col-lg-3 col-sm-6 col-xs-12">
<div class="main-box infographic-box">
<i class="fa fa-user red-bg"></i>
<span class="headline">短信剩余</span>
<span class="value">
<span class="timer" data-from="120" data-to="2562" data-speed="1000" data-refresh-interval="50">
6580
</span>
</span>
</div>
</div>
<div class="col-lg-3 col-sm-6 col-xs-12">
<div class="main-box infographic-box">
<i class="fa fa-shopping-cart emerald-bg"></i>
<span class="headline">短信订单</span>
<span class="value">
<span class="timer" data-from="30" data-to="658" data-speed="800" data-refresh-interval="30">
6580
</span>
</span>
</div>
</div>
<div class="col-lg-3 col-sm-6 col-xs-12">
<div class="main-box infographic-box">
<i class="fa fa-money green-bg"></i>
<span class="headline">余额</span>
<span class="value">
&#36;<span class="timer" data-from="83" data-to="8400" data-speed="900" data-refresh-interval="60">
6580
</span>
</span>
</div>
</div>
<div class="col-lg-3 col-sm-6 col-xs-12">
<div class="main-box infographic-box">
<i class="fa fa-eye yellow-bg"></i>
<span class="headline">已发送短信</span>
<span class="value">
<span class="timer" data-from="539" data-to="12526" data-speed="1100">
6580
</span>
</span>
</div>
</div>
</div>

    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">天眼后台短信列表</h2>
                </header>
                <div class="main-box-body clearfix">
                    <div class="table-responsive clearfix">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th><a href="#"><span> 接收人电话</span></a></th>
                                <th><a href="#" class="desc"><span>发送时间</span></a></th>
                                <th class="text-center"><span>查看详情</span></th>
                                <th>&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($info as $value)
                                <tr>
                                    <td>
                                        <a href="smslistinfo?sms_id={{$value['id']}}">{{$value['send_phone']}}</a>
                                    </td>
                                    <td>
                                        {{$value['created_at']}}
                                    </td>
                                    <td class="text-center" style="width: 15%;">
                                        <a href="smslistinfo?sms_id={{$value['id']}}">
                                            <span class="fa-stack">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-search-plus fa-stack-1x fa-inverse"></i>
                                            </span>
                                        </a>
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






    <div class="row">
    <div class="col-lg-12">
    <div class="main-box clearfix">
        <header class="main-box-header clearfix">
            <h2 class="pull-left">中介公司短信列表</h2>
        </header>
        <div class="main-box-body clearfix">
            <div class="table-responsive clearfix">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th><a href="#"><span>中介公司</span></a></th>
                        <th class="text-center"><span>查看详情</span></th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                 @foreach($infos as $key=> $value)
                <tr>
                        <td>
                        <a href="smsinfolist?company_id={{$key}}">{{$key}}</a>
                        </td>
                        <td class="text-center" style="width: 15%;">
                        <a href="smsinfolist?company_id={{$key}}">
                            <span class="fa-stack">
                                <i class="fa fa-square fa-stack-2x"></i>
                                <i class="fa fa-search-plus fa-stack-1x fa-inverse"></i>
                            </span>
                        </a>
                        </td>
                   </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
    </div>
    <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="myModal2">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">模板列表</h4>
                </div>
                <div class="modal-body">
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="tab-accounts">
                            <div class="panel-group accordion" id="operation">
                                <div class="panel panel-default">
                                    <form action="{{ url('/backend/sms/send_message') }}" method="post" id="send-message-form">
                                        {{ csrf_field() }}
                                        <table id="user" class="table table-hover" style="clear: both">
                                            <tbody>
                                            <tr>
                                                <td width="15%">模板编号</td>
                                                <td width="10%">模板名称</td>
                                                <td width="60%">
                                                   模板内容
                                                </td>
                                                <td width="15%">状态</td>
                                            </tr>
                                            @foreach($models as $model)

                                                    <td width="15%">{{$model['model_id']}}</td>
                                                    <td width="10%">{{$model['model_name']}}</td>
                                                    <td width="60%">
                                                        {{$model['content']}}
                                                    </td>
                                                    @if($model['status'] == '1')
                                                    <td style="color: green">审核通过</td>
                                                    @else
                                                    <td>审核中</td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <div class="modal-footer">
                    <div id="check"></div>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
@stop