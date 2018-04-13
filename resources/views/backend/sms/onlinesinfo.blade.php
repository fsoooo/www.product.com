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
							<li ><span><a href="/backend/sms/onlineservice">在线客服</a></span></li>
							<li ><span><a href="/backend/sms/onlineservice">客服列表</a></span></li>
							<li class="active"><span>客服详情</span></li>
						</ol>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<div class="main-box clearfix" style="min-height: 1100px;">
							<div class="tabs-wrapper tabs-no-header">
								<ul class="nav nav-tabs">
									<li ><a href="{{ url('/backend/sms/onlineservice')}}">客服列表</a></li>
									<li ><a href="{{ url('/backend/sms/addonlines') }}">添加客服</a></li>
									<li class="active"><a>客服详情</a></li>
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
																{{$res->name}}
															</td>
														</tr>
														<tr>
															<td width="15%">客服号码</td>
															<td width="60%">
																{{$res->number}}
															</td>
														</tr>
														<tr>
															<td width="15%">真实姓名</td>
															<td width="60%">
																{{$res->real_name}}
															</td>
														</tr>
														<tr>
															<td width="15%">身份证号</td>
															<td width="40%">
																{{$res->card_id}}
																<span class="label label-success">实名认证通过</span>
															</td>

														</tr>
														<tr>
															<td width="15%">联系方式</td>
															<td width="60%">
																{{$res->phone}}
															</td>
														</tr>
														<tr>
															<td>客服资料</td>
															<td style="height:210px;width: 1270px">
																{{$res->datas}}
															</td>

														</tbody>
													</table>
												</form>
											</div>

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
	<script>
        function doLabel(){
            var name = $('#name').val();
            var description = $('#description').val();
            var model_id = Math.floor(Math.random () * 9000) + 1000;

            var params = {name:name,descrition:description,model_id:model_id};
            console.log(params);
            $.ajax( {
                type : "get",
                url : 'doaddmodels',
                dataType : 'json',
                data :  params,
                success:function(msg){
                    if(msg.status == 1){
                        alert(msg.message);
                        $('#check').html('<font color="red">'+msg.message+'</font>');
                    }else{
                        alert(msg.message);
                        // $('#check').html('<font color="green">'+msg.message+'</font>');
                        window.location.href=location;
                    }

                }
            });
        }
	</script>
@stop

