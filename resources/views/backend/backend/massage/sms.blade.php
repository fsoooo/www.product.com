@extends('backend.layout.base')
@section('content')
            <div id="content-wrapper">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-12">
                                <ol class="breadcrumb">
                                    <li><a href="{{ url('/backend') }}">主页</a></li>
                                    <li ><span>消息管理</span></li>
                                    <li class="active"><span><a href="/backend/sms/sms">短信管理</a></span></li>
                                </ol>
                                @if($status==0)
                                    <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal0" id="passss">
                                        申请开通短信功能
                                    </button>
                                    <script type="text/javascript">
                                    function show(){
                                        document.getElementById("email-new").style.display="block";
                                    }
                                    </script>
                                <div class="modal fade" id="myModal0" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">开通协议</h4>
                                            </div>
                                            <div class="modal-body">
                                                <p>

                                                    1、合作内容：甲乙双方联合开设短信服务项目，合作项目为《短信xxx》。双方精诚合作，互相支持，共同发展。

                                                    2、合作区域：____省（市） 。如扩大合作范围，必须事先告知乙方，便于乙方协调区域合作资源。

                                                    3、甲方与乙方共同合作，向移动用户合作提供短信节目服务，并分享因此产生的收益。

                                                    4、甲方作为业务运营商，在市场开发和运作、推动及与用户沟通的基础上，促使更多的用户使用短信节目。

                                                    5、甲方负责与移动运营商的合作洽谈及推动业务和客户服务、进行市场推广策划及执行，双方担宣传推广费用；乙方负责提供节目软件产品、节目系统维护。

                                                    6、关于产品版权：

                                                    短信节目（包括但不仅限于“节目”中所含的任何程序、代码、算法、文字、图像、声音）的知识产权由乙方拥有。甲方未经乙方正式书面同意，不得对“节目”进行反向工程、反向编译和反汇编；不得对“节目”进行任何形式的修改。

                                                    7、关于收益分配账单标准：双方收益分配所依据的账单标准以移动运营商所提供的本项目账单为准，合作双方有权要求进行对帐。

                                                    8、关于注册用户：注册用户资料归甲乙方双方所有。

                                                    9、合作期限：____年____月____日到____年____月____日。合作期为____年，期满后双方关系及本协议自动作废。在本协议到期后，双方可以续签，继续展开合作。

                                                </p>
                                                <p><input type="radio" class="modal-body"  name="rule" value="y" onclick="smspass()" checked>我同意</p>
                                                <p><input type="radio" class="modal-body"  name="rule" value="n" onclick="smspass()">我不同意</p>
                                                <!-- <input type="hidden" name="_token"         value="{csrf_token()}"/> -->
                                            </div>
                                            <div class="modal-footer">
                                                <div id="check"></div><br/>
                                                <div id="checktoken"></div><br/>
                                                <div id="check2"></div><br/>
                                                <button type="button" id="smspass" class="btn btn-primary" onclick="passService()">确认开通</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <script type="text/javascript">
                            function smspass(){
                                var check = $('input[type="radio"]:checked').val();

                                if(check=="y"){
                                    document.getElementById("smspass").disabled=false;
                                }else{
                                    document.getElementById("smspass").disabled=true;

                                }
                            }
                            function passService(){
                                var compony = "{{$company_id}}";//获取公司的ID
                                var params = {compony:compony};
                                $.ajax({
                                    url:'/backend/passservice',//封装好的路径
                                    type:'get',
                                    dataType:'json',
                                    data:params,
                                    success:function(msg){
                                        if(msg.status == '0'){
                                            $('#check').html('<font color="green">'+msg.message+'</font>');
                                            window.location = location;
                                        }else{
                                            $('#check').html('<font color="red">'+msg.message+'</font>');
                                        }
                                    }
                                },'JSON');
                            }
                                                    </script>
    <script src="http://www.product.com/js/jquery-1.12.4.js"></script>
    <footer id="footer-bar" class="row hidden-md hidden-lg">
        <p id="footer-copyright" class="col-xs-12">
            &copy; 2014 <a href="http://www.adbee.sk/" target="_blank">Adbee digital</a>. Powered by Centaurus Theme.
        </p>
    </footer>
                        </div>
                        </div>
                        </div>
                        </div>
                                @elseif($status==1)
                               <button type="button"  class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal1">
                                   短信发送
                               </button>
                               <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal2">
                                   短信模板
                               </button>
                               <!-- Modal -->
            
                                <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="myModal1">
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
                                                <input type="text" id="phone" name="phone" placeholder="    接收人手机号" onblur="model_view()" style="width: 867px;height: 33px;border:0.5px solid slategray"><br/><br/>
                                                <input type="text" id="name" name="name" placeholder="     接收人姓名" onblur="model_view()" style="width: 867px;height: 33px;border:0.5px solid slategray"><br/><br/>
                                                <input type="date"  id="time"  name="time" placeholder="     请选择发送时间" class="form-control" onblur="model_view()" style="border:0.5px solid slategray ">
                                                <br/>
                                                <span style="font-family: 微软雅黑;font-size: 15px">
                                                    <br/>时间格式：2017-06-05 12:00:00<br/><br/>
                                                    <p style="color:green">如果不填写时间，系统将自动使用当前时间！如果您选择的时间晚于今天，系统也会自动调整为今天！</p><br/>
                                                    <h4><b>短信模版预览</b></h4>
                                                    <div id="modelview"></div><br/><br/></span>
                                             {{--<label><a href="#">下载批量发送模板</a></label><br/>--}}
                                             {{--<label>请选择批量发送短信的excel文件</label><br/><br/>--}}
                                             {{--<input type="file" name="sendsms"><br/>--}}
                                             {{--<input type="submit" name="filesub" value="上传">--}}
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
                                    console.log(model);
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
                                                '亲爱的用户，您正在使用手机号快捷登录，验证码为{1}，请于3分钟内正确输入，请勿向任何单位或个人泄露。'
                                                +'</font>');
                                            break;
                                    }
                            }
                            function dosms(){//发送短信
                                
                                var phone = $('#phone').val();
                                var name = $('#name').val();
                                var model = $('#modelviews option:selected').val();
                                var time = $('#time').val();
                                var company = "{{$company_id}}";
                                var token ="{{$token}}";//从数据库中读取
                                var sms_content = {name:name};
                                var params = {company:company,phone:phone,token:token,sms_content:sms_content,model:model,time:time};
                                $.ajax({
                                     async: true,
                                    url:"/backend/sendsms",
                                    type:'get',
                                    dataType:'json',
                                    data:params,
                                    success:function(msg){
                                        if(msg.status == 0){
                                            $('#check').html('<font color="green">'+msg.message+'</font>');
                                        }else{
                                            alert(msg.message);
                                            $('#check').html('<font color="red">'+msg.message+'</font>');
                                            if(msg.status==2){
                                                window.statuss = msg.status;
                                                window.tokens = token;
                                   $.ajax({
                                     async:false,
                                    url:"test?token="+token,
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
                               <br/><br/><div class="row">
                            <div class="col-lg-3 col-sm-6 col-xs-12">
                                <div class="main-box infographic-box">
                                    <i class="fa fa-user red-bg"></i>
                                    <span class="headline">短信剩余</span>
                                        <span class="value">
                                            <span class="timer" data-from="120" data-to="2562" data-speed="1000" data-refresh-interval="50">
                                         {{$num}}
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
                                                                    <span class="timer" data-from="83" data-to="8400" data-speed="900" data-refresh-interval="60">
                            {{$moneys}}
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
                                    {{$send}}
                                    </span>
                                    </span>
                                </div>
                            </div>
                        </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="main-box clearfix">
                                        <header class="main-box-header clearfix">
                                            <h2 class="pull-left"><b>已发短信列表</b></h2>
                                        </header>
                                        <div class="main-box-body clearfix">
                                            <div class="table-responsive clearfix">
                                                <table class="table table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th><span>手机号</span></th>
                                                        <th><span>发送时间</span></th>
                                                        <th class="text-center"><span>发送状态</span></th>
                                                        <th>&nbsp;查看详情</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    @foreach($infos as $key=> $value)
                                                        <tr>
                                                            <td>
                                                                接收人：
                                                                <a href="smsinfolist?sms_id={{$value['id']}}">{{$value['send_phone']}}</a>
                                                            </td>
                                                            <td>
                                                                {{$value['created_at']}}
                                                            </td>

                                                            <td class="text-center">
                                                                <span class="label label-success">发送成功</span>
                                                            </td>
                                                            <td class="text-center" style="width: 15%;">
                                                                <a href="smsinfolist?sms_id={{$value['id']}}">
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
                                                @include('backend.layout.pages')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @elseif($status==2)
                                <h1>短信余额不足，请尽快<a onclick="showOrder()">缴费</a>... </h1>
                                <div class="tab-content" id="showorder" >
                                    <div class="tab-pane fade in active" id="tab-accounts">
                                        <h3><p>短信充值</p></h3>
                                        @include('backend.layout.alert_info')
                                        <div class="panel-group accordion" id="operation" >
                                            <div class="panel panel-default">
                                                <form action="{{ url('/backend/sms/send_message') }}" method="post" id="send-message-form">
                                                    {{ csrf_field() }}
                                                    <table id="user" class="table table-hover" style="clear: both">
                                                        <tbody>
                                                        <tr>
                                                            <td width="15%">充值金额</td>
                                                            <td width="30%">
                                                                <input type="text" class="form-control" placeholder="请输入您要充值的金额" name = 'money' id="money">
                                                            </td width="30%">
                                                        </tr>
                                                        <tr>
                                                            <td width="15%">选择支付方式</td>
                                                            <td>
                                                                <img alt="支付宝" title="支付宝" src="/r_backend/img/alipay.jpg"><br/>
                                                                <input type="radio" name="paytype" value="Alipay">
                                                            </td>
                                                            <td>
                                                                <img alt="微信" title="微信" src="/r_backend/img/wechat.jpg"><br/>
                                                                <input type="radio" name="paytype" value="WeChat">
                                                            </td>
                                                            <td>
                                                                <img alt="银联" title="银联" src="/r_backend/img/bank.jpg"><br/>
                                                                <input type="radio" name="paytype" value="Bank">
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </form>
                                            </div>
                                            <button id="send-message-btn" class="btn btn-success" onclick="doOrder()">确认充值</button>
                                        </div>
                                    </div>
                                </div>
                               <script type="text/javascript">
                                   function showOrder(){
                                        document.getElementById('showorder').style.display = 'block';
                                   }
                                   function doOrder(){
                                       var money = $('#money').val();
                                       var paytype = $("input[type='radio']:checked").val();
                                       var company = "{{$company_id}}";
                                       var params = {money:money,company:company,paytype:paytype};
//                                       console.log(params);
                                       $.ajax({
                                           url:'/backend/sms/doorder',
                                           type:'get',
                                           dataType:'json',
                                           data:params,
                                           success:function(msg){
                                               if(msg.status == 0){
                                                   alert(msg.message);
                                                   window.location = '/backend/sms/dopay';
                                               }else{
                                                   alert(msg.message);
                                               }
                                           }
                                       },'JSON');
                                   }
                               </script>
                            @endif

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